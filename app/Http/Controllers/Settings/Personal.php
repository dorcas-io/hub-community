<?php

namespace App\Http\Controllers\Settings;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Personal extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Personal Profile';
        $this->data['page']['header'] = ['title' => 'Personal Profile'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Settings', 'href' => route('settings')],
                ['text' => 'Personal Profile', 'href' => route('settings.personal'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'settings';
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setViewUiResponse($request);
        return view('settings.personal', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'firstname' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'phone' => 'required|string|max:30',
            'email' => 'required|email|max:80',
            'gender' => 'nullable|string|in:female,male'
        ]);
        # validate the request
        try {
            $query = $sdk->createProfileService()
                            ->addBodyParam('firstname', $request->firstname)
                            ->addBodyParam('lastname', $request->lastname)
                            ->addBodyParam('phone', $request->phone)
                            ->addBodyParam('email', $request->email)
                            ->addBodyParam('gender', (string) $request->gender)
                            ->send('PUT');
            # send the request
            if (!$query->isSuccessful()) {
                throw new \RuntimeException($query->getErrors()[0]['title']);
            }
            $response = (material_ui_html_response(['Successfully updated profile information']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
