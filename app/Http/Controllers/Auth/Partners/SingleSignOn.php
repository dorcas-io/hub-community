<?php

namespace App\Http\Controllers\Auth\Partners;

use App\Http\Controllers\Auth\RegisterController;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUser;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUserProvider;
use Hostville\Dorcas\Sdk;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SingleSignOn extends RegisterController
{
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sso(Request $request, Sdk $sdk)
    {
        if (!$request->session()->has('partner')) {
            abort(404, 'Single Sign on is only available for Dorcas partners.');
        }
        $sdk = $sdk ?: app(Sdk::class);
        $provider = new DorcasUserProvider($sdk);
        # get the provider
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'password' => 'required|string',
                'business_name' => 'nullable|string',
                'phone' => 'nullable|numeric'
            ]);
            # validate the request
            $dorcasUser = $this->findUserByEmail($sdk, $request->input('email'));
            # get the authenticated user
            if (empty($dorcasUser)) {
                # this user account does not exist -- create it
                $data = $request->only(['firstname', 'lastname', 'email', 'password', 'phone']);
                if ($request->has('business_name')) {
                    $data['company'] = $request->input('business_name');
                }
                $data['phone'] = $data['phone'] ?? '00000000000';
                event(new Registered($user = $this->create($request, $data)));
                $dorcasUser = $provider->retrieveByCredentials(['email' => $user->email, 'password' => $request->password]);
                # get the authenticated user
            } else {
                $dorcasUser = $provider->retrieveByEmailOnly([
                    'email' => $dorcasUser->email,
                    'client_id' => config('dorcas-api.client_personal.id'),
                    'client_secret' => config('dorcas-api.client_personal.secret'),
                ]);
                # get the authenticated user
            }
    
            $this->guard()->login($dorcasUser);
            
            return $this->registered($request, $dorcasUser)
                ?: redirect($this->redirectPath());
            
        } catch (ValidationException $e) {
            $messages = validation_errors_to_messages($e);
            foreach ($messages as $field => $failures) {
                $errors[] = $failures[0] ?? '';
            }
            $response = material_ui_html_response(collect($errors)->filter()->all());
        }
        return redirect()->route('login')->with('UiResponse', $response);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ssoSilent(Request $request, Sdk $sdk)
    {
        if (!$request->session()->has('partner')) {
            abort(404, 'Single Sign on is only available for Dorcas partners.');
        }
        $sdk = $sdk ?: app(Sdk::class);
        $provider = new DorcasUserProvider($sdk);
        # get the provider
        $data = [];
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'password' => 'required|string',
                'business_name' => 'nullable|string',
                'phone' => 'nullable|numeric'
            ]);
            # validate the request
            $dorcasUser = $this->findUserByEmail($sdk, $request->input('email'));
            # get the authenticated user
            if (empty($dorcasUser)) {
                # this user account does not exist -- create it
                $data = $request->only(['firstname', 'lastname', 'email', 'password', 'phone']);
                if ($request->has('business_name')) {
                    $data['company'] = $request->input('business_name');
                }
                $data['phone'] = $data['phone'] ?? '00000000000';
                $user = $this->create($request, $data);
                $dorcasUser = $provider->retrieveByCredentials(['email' => $user->email, 'password' => $request->password]);
                # get the authenticated user
            } else {
                $dorcasUser = $provider->retrieveByEmailOnly([
                    'email' => $dorcasUser->email,
                    'client_id' => config('dorcas-api.client_personal.id'),
                    'client_secret' => config('dorcas-api.client_personal.secret'),
                ]);
                # get the authenticated user
            }
            
            $data = [
                'token' => $dorcasUser->getDorcasSdk()->getAuthorizationToken(),
                'id' => $dorcasUser->id,
                'email' => $dorcasUser->email
            ];
            
        } catch (ValidationException $e) {
            $messages = validation_errors_to_messages($e);
            foreach ($messages as $field => $failures) {
                $errors[] = $failures[0] ?? '';
            }
            $data['errors'] = $errors;
        }
        return response()->json($data);
    }
    
    /**
     * Returns the user, searching by email.
     *
     * @param Sdk    $sdk
     * @param string $email
     *
     * @return DorcasUser|null
     */
    private function findUserByEmail(Sdk $sdk, string $email)
    {
        $query = $sdk->createUserResource($email)->addQueryArgument('select_using', 'email')->send('get');
        if (!$query->isSuccessful()) {
            return null;
        }
        return new DorcasUser($query->getData(), $sdk);
    }
}
