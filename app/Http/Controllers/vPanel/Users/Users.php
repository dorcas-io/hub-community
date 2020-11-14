<?php

namespace App\Http\Controllers\vPanel\Users;

use GuzzleHttp\Exception\GuzzleException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Users extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => 'Users'],
            'header' => ['title' => 'Users'],
            'selectedMenu' => 'members',
        ];
    }
    
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setViewUiResponse($request);
        return view('vpanel.users.listing', $this->data);
    }
    
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function managers(Request $request)
    {
        $this->data = [
            'page' => ['title' => 'Managers'],
            'header' => ['title' => 'Managers'],
            'selectedMenu' => 'managers',
        ];
        $this->setViewUiResponse($request);
        $this->data['mode'] = 'managers';
        $this->data['listing'] = 'managers';
        return view('vpanel.users.listing', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function post(Request $request, Sdk $sdk)
    {
        $this->validate($request,[
            'email' => 'required|email',
            'firstname' => 'required|string|max:30',
            'lastname' => 'required|string|max:30'
        ]);
        # create the validator
        $toast = null;
        try {
            $resource = $sdk->createPartnerResource();
            foreach ($request->except(['_token']) as $key => $value) {
                $resource->addBodyParam($key, $value);
            }
            $response = $resource->send('post', ['invites']);
            if (!$response->isSuccessful()) {
                throw new \RuntimeException(
                    $response->getErrors()[0]['title'] ?? 'Error while sending invite.'
                );
            }
            $toast = toast('Successfully sent the invite.');
        
        } catch (GuzzleException $e) {
            $toast = toast('Network error.');
        } catch (\RuntimeException $e) {
            $toast = toast($e->getMessage());
        }
        return redirect(url()->current())->with('UiToast', $toast->json());
    }
}
