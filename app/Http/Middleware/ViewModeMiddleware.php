<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class ViewModeMiddleware
{
    /** @var array  */
    private $supportedViews = ['business', 'professional', 'vendor'];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $viewMode = $request->session()->get('viewMode', 'business');
        # get the view mode
        if ($request->query->has('view')) {
            $viewMode = strtolower($request->query('view'));
            $viewMode = !in_array($viewMode, $this->supportedViews) ? 'business' : $viewMode;
        }
        $request->session()->put('viewMode', $viewMode);
        # set it to the session
        View::composer('*', function ($view) use ($viewMode) {
            $view->with('viewMode', $viewMode);
        });
        return $next($request);
    }
}
