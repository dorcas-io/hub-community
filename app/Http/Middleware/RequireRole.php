<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class RequireRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param string|null               $rolesFilter
     *
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next, string $rolesFilter = null)
    {
        if (!Auth::check()) {
            throw new AuthorizationException();
        }
        if (!empty($rolesFilter)) {
            $roles = explode('|', $rolesFilter);
            $processedRoles = [];
            foreach ($roles as $id => $role) {
                if (stripos($role, '-') === false) {
                    continue;
                }
                $components = explode('-', $role);
                $roles[$id] = $components[0];
                unset($components[0]);
                $processedRoles[$roles[$id]] = $components[1];
            }
            $user = $request->user();
            $userRoles = !empty($user->roles) && !empty($user->roles['data']) ? $user->roles['data'] : [];
            if (empty($userRoles)) {
                $toast = toast('You do not have permissions to access that feature', 4);
                return redirect()->route('home')->with('UiToast', $toast->json());
            }
            $flattenedRoles = collect($userRoles)->map(function ($role) {
                return $role['name'];
            })->all();
            # flatten the roles out
            foreach ($roles as $role) {
                if (!in_array($role, $flattenedRoles)) {
                    continue;
                }
                return $next($request);
            }
            $toast = toast('You do not have permissions to access that feature', 4);
            return redirect()->route('home')->with('UiToast', $toast->json());
        }
        return $next($request);
    }
}
