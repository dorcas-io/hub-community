<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUser;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUserProvider;
use Hostville\Dorcas\Sdk;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Reset Password';
        $this->data['header']['title'] = 'Reset Password';
        $this->middleware('guest');
    }

    /**
     * @inheritdoc
     */
    public function showResetForm(Request $request, $token = null)
    {
        if ($request->session()->has('status')) {
            $this->data['status'] = $request->session()->get('status');
        }
        $this->data = array_merge($this->data, ['token' => $token, 'email' => $request->email]);
        $this->setViewUiResponse($request);
        return view('auth.passwords.reset-v2', $this->data);
    }

    /**
     * @inheritdoc
     */
    protected function resetPassword($user, $password)
    {
        $sdk = app(Sdk::class);
        $service = $sdk->createProfileService()->addBodyParam('email', $user->email)
                                                ->addBodyParam('password', $password)
                                                ->addBodyParam('client_id', $sdk->getClientId())
                                                ->addBodyParam('client_secret', $sdk->getClientSecret())
                                                ->addBodyParam('token', Str::random(60));
        # send the update request
        $response = $service->send('put', ['auth']);
        if (!$response->isSuccessful()) {
            throw new \RuntimeException('Failed to update your password.');
        }
        $provider = new DorcasUserProvider($sdk);
        # get the provider
        $dorcasUser = $provider->retrieveByCredentials(['email' => $user->email, 'password' => $password]);
        # get the authenticated user
        event(new PasswordReset($user));
        $this->guard()->login($dorcasUser);
    }

    /**
     * @inheritdoc
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated views. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET ? $this->sendResetResponse($response) : $this->sendResetFailedResponse($request, $response);

        /*if ($response == Password::PASSWORD_RESET) {
            //$presponse = (tabler_ui_html_response(['Successfully reset your password. Try to login again']))->setType(UiResponse::TYPE_SUCCESS);
            $this->sendResetResponse($response);
        } else {
            //$presponse = (tabler_ui_html_response(['Unable to reset your password']))->setType(UiResponse::TYPE_ERROR);
           $this->sendResetFailedResponse($request, $response);
        }
        return redirect(url()->current())->with('UiResponse', $presponse);*/
    }
}
