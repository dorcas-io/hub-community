<?php

namespace App\Http\Middleware;

use App\Http\Controllers\HomeController;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProtectUiConfigurationAccess
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }
        $dorcasUser = $request->user();
        $skipPaths = ['directory', 'home', 'xhr', 'logout', 'login', 'register', 'password', 'store', 'blog', 'subscription','access-grants','mcu','mpe','mli','mas','map','mit','dashboard-business'];
        $allModules = collect(HomeController::SETUP_UI_COMPONENTS)->map(function ($module) {
            return $module['id'];
        })->all();
        if (empty($dorcasUser->meta['granted_for'])) {
            $skipRestrictions = $request->session()->get('skip-configuration-check', false);
            # whether or not to skip the restrictions - applies to authentication via tokens in the URL
            $company = $dorcasUser->company(true, true);
            $userConfigurations = (array) $dorcasUser->extra_configurations;
            $userUiSetup = $userConfigurations['ui_setup'] ?? $allModules;
            $configurations = (array) $company->extra_data;
            if ($skipRestrictions) {
                $uiConfiguration = $allModules;
                array_push($skipPaths, 'access-grants', 'subscription', 'settings');
            } else {
                if (!empty($userUiSetup)) {
                    $uiConfiguration = $userUiSetup;
                } else {
                    $uiConfiguration = $configurations['ui_setup'] ?? $allModules;
                    array_push($skipPaths, 'access-grants', 'subscription', 'settings');
                }
            }
        } else {
            $uiConfiguration = $dorcasUser->meta['granted_for']['data']['extra_json']['modules'] ?? [];
        }
        if (starts_with($request->path(), $skipPaths)) {
            return $next($request);
        }
        $availableModules = collect(HomeController::SETUP_UI_COMPONENTS)->filter(function ($module) use ($uiConfiguration) {
            return in_array($module['id'], $uiConfiguration, true);
        })->all();
        $allowedPaths = [];
        foreach ($availableModules as $module) {
            if (empty($module['path'])) {
                continue;
            }
            $allowedPaths = array_merge($allowedPaths, (array) $module['path']);
        }
        if (!starts_with($request->path(), $allowedPaths)) {
            # not one of the approved paths
            throw new AuthorizationException('You do not have access to this feature.');
        }
        return $next($request);
    }
}
