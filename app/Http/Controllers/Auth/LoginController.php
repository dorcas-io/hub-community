<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUser;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
        $this->data['page']['title'] = 'Login';
        $this->data['page']['login_product_name'] = !empty($partner->name) ? $partner->name : config('app.name');
    }

    /**
     * @inheritdoc
     */
    public function showLoginForm(Request $request)
    {
        $this->data['header']['title'] = 'Login';

        $this->setViewUiResponse($request);
        return view('auth.login-v2', $this->data);
    }

    /**
     * @param Request $request
     * @param    $dorcasUser
     */
    protected function authenticated(Request $request, $dorcasUser)
    {
        $viewMode = 'business';
        $company = $dorcasUser->company();
        # get the company information
        $companyConfiguration = (array) $company['extra_data'] ?? [];
        # get the company configuration
        if (empty($companyConfiguration['ui_setup'])) {
            # this key will be set after proper UI setup for the account; working on the assumption that an account
            # will not have "is_processional"/"is_vendor" set at account creation - unless the person signed up using
            # those specific modes. Once the key is set though, it will automatically load in "business" mode.
            if ($dorcasUser->is_professional) {
                $viewMode = 'professional';
            } elseif ($dorcasUser->is_vendor) {
                $viewMode = 'vendor';
            }
        }
        $request->session()->put('viewMode', $viewMode);
        # set it to the session
        try {
            # we use this opportunity to sync the user data with what we have in our db
            DB::transaction(function () use ($dorcasUser) {
                $data = $dorcasUser->company(true, true);
                # get the company data
                $company = Company::firstOrNew(['uuid' => $data->id]);
                # get the company
                $company->reg_number = $data->registration;
                $company->name = $data->name;
                $company->phone = $data->phone;
                $company->email = $data->email;
                $company->website = $data->website;
                # set the properties
                $company->save();

                $user = User::firstOrNew(['uuid' => $dorcasUser->id]);
                $user->company_id = $company->id;
                $user->firstname = $dorcasUser->firstname;
                $user->lastname = $dorcasUser->lastname;
                $user->gender = $dorcasUser->gender;
                $user->email = $dorcasUser->email;
                $user->password = $dorcasUser->password;
                $user->phone = $dorcasUser->phone;
                $user->photo_url = $dorcasUser->photo;
                $user->is_verified = $dorcasUser->is_verified;
                # set the properties
                $user->save();
                # update the data
            });
        } catch (\Throwable $e) {
            // be quiet about it
            Log::error($e->getMessage());
        }
    }
}
