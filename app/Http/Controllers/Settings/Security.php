<?php

namespace App\Http\Controllers\Settings;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Security extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Account Security';
        $this->data['page']['header'] = ['title' => 'Account Security'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Settings', 'href' => route('settings')],
                ['text' => 'Account Security', 'href' => route('settings.security'), 'isActive' => true],
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
        return view('settings.security', $this->data);
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
            'password' => 'required|string|confirmed'
        ]);
        # validate the request
        try {
            $query = $sdk->createProfileService()
                            ->addBodyParam('password', $request->password)
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
