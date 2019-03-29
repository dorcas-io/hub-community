<?php

namespace App\Http\Controllers\Settings;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Billing extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Billing Settings';
        $this->data['page']['header'] = ['title' => 'Billing Settings'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Settings', 'href' => route('settings')],
                ['text' => 'Billing', 'href' => route('settings.billing'), 'isActive' => true],
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
        $configuration = !empty($company->extra_data) ? $company->extra_data : [];
        $this->data['billing'] = $configuration['billing'] ?? [];
        if (!empty($configuration['paystack_authorization_code']) && !isset($this->data['billing']['auto_billing'])) {
            $this->data['billing']['auto_billing'] = true;
        } elseif (!isset($this->data['billing']['auto_billing'])) {
            $this->data['billing']['auto_billing'] = false;
        }
        $this->data['billing']['auto_billing'] = (int) $this->data['billing']['auto_billing'];
        return view('settings.billing', $this->data);
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
            'auto_billing' => 'required|numeric|in:0,1',
        ]);
        # validate the request
        $company = $request->user()->company(true, true);
        $action = strtolower($request->input('action'));
        try {
            if ($action === 'save_billing') {
                # update the business information
                $configuration = !empty($company->extra_data) ? $company->extra_data : [];
                if (empty($configuration['billing'])) {
                    $configuration['billing'] = [];
                }
                $configuration['billing']['auto_billing'] = (bool) intval($request->input('auto_billing'));
                $query = $sdk->createCompanyService()->addBodyParam('extra_data', $configuration)
                                                    ->send('post');
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while updating your billing preferences. Please try again.');
                }
                $message = ['Successfully updated your billing preferences'];
            }
            $response = (material_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
