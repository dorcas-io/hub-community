<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
        $this->data['page']['title'] = 'Forgot Password';
        $this->data['header']['title'] = 'Forgot Password';
    }

    /**
     * @inheritdoc
     */
    public function showLinkRequestForm(Request $request)
    {
        if ($request->session()->has('status')) {
            $this->data['status'] = $request->session()->get('status');
        }
        $this->data['header']['title'] = 'Password Reset';
        $this->setViewUiResponse($request);

        return view('modules-auth::forgot', $this->data);
        //return view('auth.passwords.forgot-v2', $this->data);
    }

    /**
     * @inheritdoc
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        //return $response == Password::RESET_LINK_SENT ? $this->sendResetLinkResponse($response) : $this->sendResetLinkFailedResponse($request, $response);
        if ($response == Password::RESET_LINK_SENT) {
            $presponse = (tabler_ui_html_response(['Successfully initiated your password reset. Check <strong>'.$request->email.'</strong> for details.']))->setType(UiResponse::TYPE_SUCCESS);
        } else {
            $presponse = (tabler_ui_html_response(['Unable to initiate your password reset']))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $presponse);
    }
}
