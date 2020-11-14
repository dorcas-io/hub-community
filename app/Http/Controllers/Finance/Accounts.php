<?php

namespace App\Http\Controllers\Finance;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Accounts extends Controller
{
    /**
     * Accounts constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Finance: Accounts';
        $this->data['page']['header'] = ['title' => 'Accounts'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Finance', 'href' => route('apps.finance')],
                ['text' => 'Accounts', 'href' => route('apps.finance'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'finance';
        $this->data['selectedSubMenu'] = 'accounts';
    }
    
    /**
     * @param Request   $request
     * @param Sdk       $sdk
     * @param string    $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk, string $id = null)
    {
        $this->setViewUiResponse($request);
        $accounts = $this->getFinanceAccounts($sdk);
        $mode = 'topmost';
        if (!empty($id)) {
            $mode = 'sub_accounts';
            $baseAccount = $accounts->where('id', $id)->first();
            # get the base account
            if (empty($baseAccount)) {
                abort(500, 'Something went wrong while loading the page.');
            }
            $accounts = $accounts->filter(function ($account) use ($id) {
                if (empty($account->parent_account) || empty($account->parent_account['data'])) {
                    return false;
                }
                return $account->parent_account['data']['id'] === $id;
            });
            $this->data['baseAccount'] = $baseAccount;
            $this->data['page']['header']['title'] .= ' - ' . $baseAccount->display_name;
            $this->data['breadCrumbs']['crumbs'][1]['isActive'] = false;
            $this->data['breadCrumbs']['crumbs'][] = ['text' => 'Sub-Accounts', 'href' => '#', 'isActive' => true];
            
        } elseif (!empty($accounts)) {
            $accounts = $accounts->filter(function ($account) {
                return empty($account->parent_account) || empty($account->parent_account['data']);
            });
        }
        $this->data['mode'] = $mode;
        $this->data['accounts'] = !empty($accounts) ? $accounts->values() : collect([]);
        return view('finance.accounts', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(Request $request, Sdk $sdk)
    {
        $company = $request->user()->company(true, true);
        # get the company
        try {
            $resource = $sdk->createFinanceResource();
            # the resource
            $payload = $request->request->all();
            foreach ($payload as $key => $value) {
                $resource = $resource->addBodyParam($key, $value);
            }
            $response = $resource->send('post', ['accounts']);
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the new account. '.$message);
            }
            Cache::forget('finance.accounts.'.$company->id);
            $response = (material_ui_html_response(['Successfully added the new account.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
