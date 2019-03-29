<?php

namespace App\Http\Middleware;

use Closure;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUser;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class DorcasAuthViaUrlToken
{
    const PAGE_MODE_MOBILE = 'mobile';
    const PAGE_MODE_DEFAULT = 'default';
    
    /** @var array  */
    protected $pageModes = [self::PAGE_MODE_MOBILE, self::PAGE_MODE_DEFAULT];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->query->has('token')) {
            return $next($request);
        }
        $token = $request->query('token');
        $sdk = app(Sdk::class);
        $sdk->setAuthorizationToken($token);
        # instantiate the sdk
        $query = $sdk->createProfileService()->send('get');
        if (!$query->isSuccessful()) {
            # we couldn't load the profile
            return $next($request);
        }
        $data = $query->getData();
        if (!empty($query->meta)) {
            $data = array_merge($data, ['meta' => $query->meta]);
        }
        $user = new DorcasUser($data, $sdk);
        $guard = Auth::guard();
        # the session guard
        $request->session()->put($guard->getName(), $user->getAuthIdentifier());
        if (!empty($data['meta']['granted_for'])) {
            # this person's access was authorised by the "granted-for" method
            $grantedFor = $user->meta['granted_for']['data'];
            $request->session()->put('granted_for', $grantedFor['id']);
            $storeId = $data['id'].'.granted_for.'.$grantedFor['id'];
            Cache::put('dorcas.auth_token.'.$storeId, $token, 24 * 60);
            # setting a granted for token
            Cookie::queue('store_id', $storeId, 24 * 60);
            # set the user id cookie
        } elseif ($request->session()->has('granted_for')) {
            # the granted-for data exists in the session
            $storeId = $data['id'].'.granted_for.'. $request->session()->get('granted_for');
            Cache::put('dorcas.auth_token.'.$storeId, $token, 24 * 60);
            # setting a granted for token
        } else {
            # neither of the above situations
            $storeId = $data['id'];
            Cookie::queue('store_id', $storeId, 24 * 60);
            # set the user id cookie
            Cache::put('dorcas.auth_token.'.$storeId, $token, 24 * 60);
            $request->session()->remove('granted_for');
            $request->session()->put('skip-configuration-check', true);
        }
        $request->session()->migrate(true);
        if (isset($guard->events)) {
            $guard->events->dispatch(new \Illuminate\Auth\Events\Login($user, false));
        }
        $guard->setUser($user);
        if ($request->query->has('mode')) {
            # we have a page mode
            $pageMode = strtolower($request->query('mode'));
            if (in_array($pageMode, $this->pageModes, true)) {
                $request->session()->put('pageMode', $pageMode);
            }
        } else {
            $request->session()->put('pageMode', self::PAGE_MODE_DEFAULT);
        }
        return $next($request);
    }
}
