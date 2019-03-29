<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ECommerce\OnlineStore;
use Closure;
use Illuminate\Support\Facades\View;

class StoreVerifier
{
    const SERVICE_NAME = 'store';
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('domain')) {
            # not accessing it via a custom subdomain that has been resolved
            abort(404, 'No store at this URL');
        }
        $domainInfo = $request->session()->get('domainInfo');
        $domain = $request->session()->get('domain');
        # get the domain
        if ($domainInfo->getService() !== self::SERVICE_NAME) {
            return next($request);
        }
        if (!empty($domain->owner['data'])) {
            $storeOwner = (object) $domain->owner['data'];
            $firstUser = !empty($storeOwner->users['data']) && !empty($storeOwner->users['data'][0]) ?
                (object) $storeOwner->users['data'][0] : null;
            # get the first user account
            $partner = !empty($firstUser) && !empty($firstUser->partner['data']) ?
                (object) $firstUser->partner['data'] : null;
            # get the partner information
            View::composer('webstore.*', function ($view) use ($domainInfo, $storeOwner, $partner) {
                $view->with('storeOwner', $storeOwner);
                $view->with('storeDomain', $domainInfo->getDomain());
                $view->with('storeSettings', OnlineStore::getStoreSettings((array) $storeOwner->extra_data));
                # our store settings container
                if (!empty($partner)) {
                    $view->with('partnerHubConfig', $partner->extra_data['hubConfig'] ?? []);
                }
            });
        }
        return $next($request);
    }
}
