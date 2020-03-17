<?php

namespace App\Http\Middleware;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Closure;

class ProfessionalModeAccessGate
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
        if (!$request->user()->is_professional) {
            # this person doesn't have a professional profile
            $redirect = route('dashboard') . '?views=business';
            $messages = ['You need to activate the professional profile on your account to access this feature.'];
            $response = (tabler_ui_html_response($messages))->setType(UiResponse::TYPE_ERROR);
            return redirect($redirect)->with('UiResponse', $response);
        }
        return $next($request);
    }
}
