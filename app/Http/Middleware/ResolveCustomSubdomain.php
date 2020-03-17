<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Psr7\Uri;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Dorcas\Hub\Utilities\DomainManager\DorcasSubdomain;

class ResolveCustomSubdomain
{
    /**
     * Array containing paths that should be redirected to a specified custom sub-domain along.
     * service => path
     *
     * @var array
     */
    protected $redirectPathsToSubdomain = [
        'store' => 'store',
        'blog' => 'blog'
    ];
    
    /** @var array  */
    protected $standardHosts = ['dorcas.io', 'dorcas.ng','dorcas-hub.test','localhost:8001'];
    
    /**
     * ResolveCustomSubdomain constructor.
     */
    public function __construct()
    {
        $uri = new Uri(config('app.url'));
        # the base URI
        $this->standardHosts[] = $uri->getHost();
        # append the configured host
    }
    
    /**
     * @param         $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->header('host');
        # get the host header value
        try {
            $domainInfo = $this->splitHost($host, $request->path());
            # get the host information
            $slug = $domainInfo->getSubdomain();
            # get the slug
            if (empty($slug)) {
                # no matches
                throw new \RuntimeException('Could not reliably determine the URL slug for this host.');
            }
            $serviceRedirectUrl = $this->getServiceRedirectUrl($domainInfo, $request->path(), $request->getQueryString());
            if ($serviceRedirectUrl !== null) {
                //dd($serviceRedirectUrl);
                return redirect($serviceRedirectUrl);
            }
            $sdk = app(Sdk::class);
            # get the Sdk
            $domain = Cache::remember('domain_' . $slug, 60, function () use ($slug, $sdk) {
                $query = $sdk->createDomainResource()->addQueryArgument('id', $slug)
                                                        ->addQueryArgument('include', 'owner,owner.users')
                                                        ->send('get', ['resolver']);
                # send the query
                if (!$query->isSuccessful()) {
                    return null;
                }
                return (object) $query->getData();
            });
            if (empty($domain)) {
                throw new \RuntimeException('Could not resolve the custom subdomain');
            }
            $owner = !empty($domain->owner['data']['slug']) ? (object) $domain->owner['data'] : null;
            # set the partner, if it was resolved as such
            $partner = $this->isPartner($owner) ? $owner : null;
            # if the object is a partner, we set it - else, just ignore it
            $isPartnerAdmin = false;
            $request->session()->put('domainInfo', $domainInfo);
            $request->session()->put('domain', $domain);
            if (!empty($partner)) {
                $request->session()->put('partner', $partner);
                $userRoles = Auth::check() ? $request->user()->roles['data'] : [];
                if (!empty($userRoles)) {
                    $partnerRole = collect($userRoles)->filter(function ($r) {
                        return $r['name'] === 'partner';
                    });
                    $isPartnerAdmin = $partnerRole->count() > 0;
                    if ($isPartnerAdmin) {
                        $request->session()->put('isPartnerAdministrator', true);
                    }
                }
            }
            View::composer('*', function ($view) use ($domain, $partner, $isPartnerAdmin, $sdk) {
                $view->with('domain', $domain);
                $defaultProductName = !empty($partner) ? $partner->name : 'Hub';
                if (!empty($partner)) {
                    $partnerConfig = (array) $partner->extra_data;
                    $hubConfig = $partnerConfig['hubConfig'] ?? [];
                    $hubConfig['product_logo'] = !empty($partner->logo) ? $partner->logo : null;
                    $view->with('partner', $partner);
                    $view->with('partnerHubConfig', $hubConfig);
                    $view->with('appUiSettings', $hubConfig);
                    $view->with('partnerHubProductName', $hubConfig['product_name'] ?? $defaultProductName);
                    $view->with('isPartnerAdministrator', $isPartnerAdmin);
                    $scheme = app()->environment() === 'production' ? 'https' : 'http';
                    $vPanelUrl = $scheme . '://' . $domain->prefix . '.' . $domain->domain['data']['domain'] . '/vpanel';
                    $view->with('vPanelUrl', $vPanelUrl);
                }
            });
            # set the domain to the session
        } catch (\RuntimeException $e) {
            $request->session()->remove('domainInfo');
            $request->session()->remove('domain');
            # remove the domain from the session, if previously set
            $request->session()->remove('partner');
            # same for the partner
        }
        return $next($request);
    }
    
    /**
     * @param \stdClass|null $object
     *
     * @return bool
     */
    protected function isPartner(\stdClass $object = null): bool
    {
        if (empty($object)) {
            return false;
        }
        return !empty($object->name) && !empty($object->slug);
    }
    
    /**
     * @param \App\Dorcas\Hub\Utilities\DomainManager\DorcasSubdomain $domainInfo
     * @param string                                                  $path
     * @param string|null                                             $query
     *
     * @return string|null
     */
    public function getServiceRedirectUrl(DorcasSubdomain $domainInfo, string $path, string $query = null): ?string
    {
        if ($domainInfo->getService() === null || !starts_with($path, array_values($this->redirectPathsToSubdomain))) {
            # no resolved service OR it's not a service configured for compulsory redirect
            return null;
        }
        $servicePath = $this->redirectPathsToSubdomain[$domainInfo->getService()];
        $resultingPath = substr($path, strlen($servicePath));
        $redirectUrl = $domainInfo->getDomain() . '/' .
            (strpos($resultingPath, '/') === 0 ? substr($resultingPath, 1) : $resultingPath) . '?' . ($query ?? '');
        return $redirectUrl;
    }
    
    /**
     * @param string      $host
     * @param string|null $path
     *
     * @return \App\Dorcas\Hub\Utilities\DomainManager\DorcasSubdomain
     */
    public function splitHost(string $host, string $path = null): DorcasSubdomain
    {
        if (in_array($host, $this->standardHosts, true)) {
            throw new \RuntimeException('Accessing via the regular domain.');
        }
        $parts = explode('.', $host, 3);
        # split it up to at most 3 parts -- we're trying to match things like:
        # xyz.dorcas.io, xyz.store.dorcas.io
        $slug = $parts[0];
        # the actual sub-domain
        $services = array_keys($this->redirectPathsToSubdomain);
        # get the services - we want to identify the service to be loaded
        $serviceName = null;
        foreach ($services as $service) {
            $pathRefersToService = $path !== null && starts_with($path, $service);
            if (!$pathRefersToService && !str_contains($slug, $service)) {
                continue;
            }
            $serviceName = $service;
            break;
        }
        if ($parts[count($parts) - 2] === 'dorcas') {
            # since the 2nd to the last index is just dorcas - we merge it with the TLD
            $parts[1] .= '.' . $parts[2];
            if ($serviceName !== null) {
                $parts[2] = $parts[1];
                # switch the domain to the last index since we have discovered a service
                $parts[1] = $serviceName;
            } else {
                unset($parts[2]);
            }
        }
        if (count($parts) === 3 && !starts_with($parts[2], 'dorcas')) {
            # we still have 3 indexes in the array, but the host domain is a not a Dorcas domain
            $host = $parts[1] . '.' . $parts[2];
            # recreate the parts
            $parts[0] = $parts[1];
            # set the slug to be the domain name
            if ($serviceName !== null) {
                $parts[1] = $serviceName;
                $parts[2] = $host;
            } else {
                $parts[1] = $host;
                unset($parts[2]);
            }
        }
        return new DorcasSubdomain($parts);
    }
}
