<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{

    //protected $addHttpCookie = false;
    # added wil troubouleshootinng TokenMismatch

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'mit/webhook',
    ];

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            return redirect(route('login'));
        }
    }
}
