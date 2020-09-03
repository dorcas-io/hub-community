<?php

namespace App\Http\Controllers\vPanel;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Settings extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => 'Account Settings'],
            'header' => ['title' => 'Account Settings'],
            'selectedMenu' => 'settings',
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
        return view('vpanel.settings', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'action' => 'required|string|in:update_profile,update_photo',
            'firstname' => 'required_if:action,update_profile|string|max:30',
            'lastname' => 'required_if:action,update_profile|string|max:30',
            'gender' => 'required_if:action,update_profile|string|in:female,male',
            'phone' => 'required_if:action,update_profile|string|max:30',
            'email' => 'required_if:action,update_profile|email|max:80',
            'photo' => 'required_if:action,update_photo|image'
        ]);
        # validate the request
        $action = $request->input('action');
        # get the action
        try {
            switch ($action) {
                case 'update_profile':
                    $resource = $sdk->createProfileService();
                    $data = $request->only(['firstname', 'lastname', 'phone', 'gender']);
                    foreach ($data as $key => $value) {
                        $resource->addBodyParam($key, $value);
                    }
                    $response = $resource->send('put');

                    $query = $sdk->createCompanyService()
                                    ->addBodyParam('email', $request->input('email', ''))
                                    ->send('PUT');
                    # send the request
                    if (!$query->isSuccessful()) {
                        throw new \RuntimeException('Failed while updating your support email. Please try again.');
                    }

                    break;
                case 'update_photo':
                    $resource = $sdk->createProfileService();
                    $file = $request->file('photo');
                    $resource->addMultipartParam('photo', file_get_contents($file->getRealPath(), false), $file->getClientOriginalName());
                    $response = $resource->send('post');
                    break;
                default:
                    break;
            }
            if (empty($response)) {
                throw new \RuntimeException('Could not process the request.');
            }
            if (!$response->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while updating your profile information. '.$message);
            }
            $response = toast('Successfully saved your profile information.');
        } catch (\RuntimeException $e) {
            $response = toast($e->getMessage());
        }
        return redirect(url()->current())->with('UiToast', $response->json());
    }
}
