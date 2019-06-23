<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class CheckActiveSubscriptionPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            # not yet authenticated -- skip the check
            return $next($request);
        }
        $allowEvenWhenExpired = ['plans', 'subscription','mse'];
        # paths to allow even when the account is in the expired state
        if (in_array($request->path(), $allowEvenWhenExpired)) {
            # allow people to view the pages even when their account has expired
            return $next($request);
        }
        $dorcasUser = $request->user();
        # get the dorcas user
        $company = $dorcasUser->company(true, true);
        # get the company information
        if (empty($company)) {
            throw new \RuntimeException('Error while reading company information for authenticated user.', $dorcasUser->toArray());
        }
        $expiry = Carbon::parse($company->access_expires_at);
        # get the expiry
        if ($expiry->lessThan(Carbon::now()) && $request->path() !== 'home' && !starts_with($request->path(), 'xhr')) {
            $message = 'Your account subscription expired on '.$expiry->format('D jS M, Y');
            return redirect(route('home') . '?' . http_build_query(['message' => $message]));
        }
        return $next($request);
    }
}
