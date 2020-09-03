<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class SetPageMessages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeMessages = config('dorcas.path_messages');
        $paths = array_keys($routeMessages);
        $messages = null;
        foreach ($paths as $path) {
            if (!starts_with($request->path(), $path)) {
                continue;
            }
            $messages = $routeMessages[$path];
            View::composer('*', function ($view) use ($messages) {
                $view->with('pageUpgradeMessage', $messages['upgrade'] ?? '');
                $view->with('pageStandardMessage', $messages['standard'] ?? '');
            });
            break;
        }
        
        return $next($request);
    }
}
