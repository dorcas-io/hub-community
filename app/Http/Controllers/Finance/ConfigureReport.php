<?php

namespace App\Http\Controllers\Finance;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ConfigureReport extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Finance: Configure Report';
        $this->data['page']['header'] = ['title' => 'Configure a Report'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Finance', 'href' => route('apps.finance')],
                ['text' => 'Reports', 'href' => route('apps.finance.reports')],
                ['text' => 'Reports', 'href' => route('apps.finance.reports.configure'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'finance';
        $this->data['selectedSubMenu'] = 'reports';
    }
    
    public function index(Request $request, Sdk $sdk, string $id = null)
    {
        $this->setViewUiResponse($request);
        $accounts = $this->getFinanceAccounts($sdk);
        if (empty($accounts) || $accounts->count() === 0) {
            return redirect(route('apps.finance'));
        }
        $this->data['accounts'] = $accounts->filter(function ($account) {
            return empty($account->parent_account) || empty($account->parent_account['data']);
        })->values();
        # set the accounts to be displayed for selection
        $configured = $this->getFinanceReportConfigurations($sdk);
        if (!empty($id)) {
            $report = $configured->where('id', $id)->first();
            $this->data['report'] = $report ?: null;
        }
        return view('finance.configure-report', $this->data);
    }
    
    /**
     * @param Request     $request
     * @param Sdk         $sdk
     * @param string|null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function configure(Request $request, Sdk $sdk, string $id = null)
    {
        $this->validate($request, [
            'report' => 'required|string|in:balance_sheet,income_statement',
            'accounts' => 'required|array',
            'accounts.*' => 'required|string'
        ]);
        # validate the request
        try {
            $company = $request->user()->company(true, true);
            # get the company information
            $query = $sdk->createFinanceResource()->addBodyParam('report_name', $request->report)
                                                    ->addBodyParam('accounts', $request->accounts);
            if (empty($id)) {
                $query = $query->send('POST', ['reports', 'configure']);
            } else {
                $query = $query->send('PUT', ['reports', 'configure', $id]);
            }
            # send the request
            if (!$query->isSuccessful()) {
                throw new \RuntimeException('Failed while saving the report configuration. Please try again.');
            }
            Cache::forget('finance.report_configurations.'.$company->id);
            # forget the cache data
            $message = ['Successfully saved the configuration for '.$request->report];
            $response = (material_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
