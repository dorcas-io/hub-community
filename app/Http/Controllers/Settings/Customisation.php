<?php

namespace App\Http\Controllers\Settings;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Customisation extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Customisation Settings';
        $this->data['page']['header'] = ['title' => 'Customisation'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Settings', 'href' => route('settings')],
                ['text' => 'Customisation', 'href' => route('settings.customise'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'settings';
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['company'] = $company = $request->user()->company(true, true);
        # get the company information
        return view('settings.customisation', $this->data);
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
            'logo' => 'required_if:action,customise_logo|image',
        ]);
        # validate the request
        try {
            if ($request->action === 'customise_logo') {
                # update the business information
                $file = $request->file('logo');
                $query = $sdk->createCompanyService()
                                ->addMultipartParam('logo', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                                ->send('post');
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while updating your business logo. Please try again.');
                }
                $message = ['Successfully updated your customisation preference'];
            }
            $response = (material_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
