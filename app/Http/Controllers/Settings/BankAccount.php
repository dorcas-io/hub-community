<?php

namespace App\Http\Controllers\Settings;

use App\Dorcas\Hub\Enum\Banks;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class BankAccount extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Bank Account';
        $this->data['page']['header'] = ['title' => 'Bank Account'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Settings', 'href' => route('settings')],
                ['text' => 'Bank Account', 'href' => route('settings.bank-account'), 'isActive' => true],
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
        $accounts = $this->getBankAccounts($sdk);
        if (!empty($accounts) && $accounts->count() > 0) {
            $this->data['account'] = $account = $accounts->first();
        } else {
            $this->data['default'] = [
                'account_number' => '',
                'account_name' => $request->user()->firstname . ' ' . $request->user()->lastname,
                'json_data' => [
                    'bank_code' => ''
                ]
            ];
        }
        $this->data['banks'] = collect(Banks::BANK_CODES)->sort()->map(function ($name, $code) {
            return ['name' => $name, 'code' => $code];
        })->values();
        return view('settings.bank-accounts', $this->data);
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
            'bank' => 'required|numeric|max:100',
            'account_number' => 'required|string|max:30',
            'account_name' => 'required|string|max:80'
        ]);
        # validate the request
        try {
            $bankName = Banks::BANK_CODES[$request->bank];
            # we get the name of the specific bank for submission
            $query = $sdk->createProfileService();
            # get the query object
            $payload = $request->only(['account_number', 'account_name']);
            foreach ($payload as $key => $value) {
                $query = $query->addBodyParam($key, $value);
            }
            $query = $query->addBodyParam('json_data', ['bank_code' => $request->bank, 'bank_name' => $bankName]);
            # set the json data for the bank account
            $accounts = $this->getBankAccounts($sdk);
            if (!empty($accounts) && $accounts->count() > 0) {
                $response = $query->send('PUT', ['bank-accounts', $accounts->first()->id]);
            } else {
                $response = $query->send('POST', ['bank-accounts']);
            }
            if (!$response->isSuccessful()) {
                throw new \RuntimeException($response->getErrors()[0]['title'] ?: 'Failed while updating your bank information. Please try again.');
            }
            Cache::forget('user.bank-accounts.'.$request->user()->id);
            # clear the cache
            $message = ['Successfully updated bank account information.'];
            $response = (material_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
