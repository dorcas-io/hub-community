<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUserProvider;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;

class Invites extends Controller
{
    public function __construct()
    {
        parent::__construct();
        /*$this->data['page']['title'] = 'Invite';
        $this->data['page']['header'] = ['title' => 'Respond to Invite'];
        $this->data['currentPage'] = 'invite';*/
        $this->data = [
            'page' => ['title' => 'Invite'],
            'header' => ['title' => 'Respond to Invite'],
            'selectedMenu' => '',
            'submenuConfig' => '',
            'submenuAction' => ''
        ];
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk, string $id)
    {
        $this->setViewUiResponse($request);
        if ($request->has('reject_invite')) {
            # reject the request
            return $this->rejectInvite($sdk, $id);
        }
        $this->data['invite'] = $invite = $this->getInvite($sdk, $id);
        return view('invite-v2', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function post(Request $request, Sdk $sdk, string $id)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'firstname' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'phone' => 'required|string|max:30',
            'email' => 'required|string|email|max:80',
            'password' => 'required|string',
            'company' => 'nullable|string',
        ]);
        # create the validator
        $validator->validate();
        # validate the request
        $toast = null;
        try {
            $resource = $sdk->createInviteResource($id)->addBodyParam('status', 'accepted');
            foreach ($request->except(['_token']) as $key => $value) {
                $resource->addBodyParam($key, $value);
            }
            $response = $resource->send('post');
            if (!$response->isSuccessful()) {
                throw new \RuntimeException(
                    $response->getErrors()[0]['title'] ?? 'Error while accepting the invite, and creating your account.'
                );
            }
            $data = $response->getData();
            if (!empty($data['password'])) {
                # we got a user resource back
                $provider = new DorcasUserProvider($sdk);
                # get the provider
                $user = $provider->retrieveByCredentials(['email' => $request->email, 'password' => $request->password]);
                # get the authenticated user
                Auth::guard()->login($user);
                return redirect()->route('dashboard');
                
            }
            //$toast = toast('Successfully updated the invite status.');
            $response = (tabler_ui_html_response(['Successfully updated the invite status.']))->setType(UiResponse::TYPE_SUCCESS);
            
        } catch (GuzzleException $e) {
            //$toast = toast('Network error.');
            $response = (tabler_ui_html_response(['Network error.']))->setType(UiResponse::TYPE_ERROR);
        } catch (\RuntimeException $e) {
            //$toast = toast($e->getMessage());
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        //return redirect(url()->current())->with('UiToast', $toast->json());
        return redirect(url()->current())->with('UiResponse', $response);
    }
    
    /**
     * @param Sdk    $sdk
     * @param string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function rejectInvite(Sdk $sdk, string $id)
    {
        try {
            $resource = $sdk->createInviteResource($id)->addBodyParam('status', 'rejected');
            $response = $resource->send('post');
            if (!$response->isSuccessful()) {
                throw new \RuntimeException(
                    $response->getErrors()[0]['title'] ?? 'Error while rejecting the invite.'
                );
            }
            //$toast = toast('Successfully rejected the invite.');
            $response = (tabler_ui_html_response(['Successfully rejected the invite.']))->setType(UiResponse::TYPE_SUCCESS);
            
        } catch (GuzzleException $e) {
            //$toast = toast('Network error.');
            $response = (tabler_ui_html_response(['Network error.']))->setType(UiResponse::TYPE_ERROR);
        } catch (\RuntimeException $e) {
            //$toast = toast($e->getMessage());
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        //return redirect(url()->current())->with('UiToast', $toast->json());
        return redirect(url()->current())->with('UiResponse', $response);
    }
    
    /**
     * @param Sdk    $sdk
     * @param string $id
     *
     * @return mixed
     */
    private function getInvite(Sdk $sdk, string $id)
    {
        $response = $sdk->createInviteResource($id)->addQueryArgument('include', 'inviter,inviting_user')->send('get');
        if (!$response->isSuccessful()) {
            $message = $response->getErrors()[0]['title'] ?? 'Something went wrong while pulling up the invite information.';
            abort(404, $message);
        }
        return $response->getData(true);
    }
}
