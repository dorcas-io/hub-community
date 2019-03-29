<?php

namespace App\Http\Middleware;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Closure;

class VendorsModeAccessGate
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
        if (!$request->user()->is_vendor) {
            # this person doesn't have a professional profile
            $redirect = route('home') . '?view=business';
            $messages = ['You need to activate the vendor profile on your account to access this feature.'];
            $response = (material_ui_html_response($messages))->setType(UiResponse::TYPE_ERROR);
            return redirect($redirect)->with('UiResponse', $response);
        }
        return $next($request);
    }
}
