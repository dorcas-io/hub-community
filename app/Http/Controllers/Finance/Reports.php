<?php

namespace App\Http\Controllers\Finance;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Reports extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Finance: Reports';
        $this->data['page']['header'] = ['title' => 'Accounting Reports'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Finance', 'href' => route('apps.finance')],
                ['text' => 'Reports', 'href' => route('apps.finance.reports'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'finance';
        $this->data['selectedSubMenu'] = 'reports';
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
        $accounts = $this->getFinanceAccounts($sdk);
        if (empty($accounts) || $accounts->count() === 0) {
            return redirect(route('apps.finance'));
        }
        $this->data['configurations'] = $this->getFinanceReportConfigurations($sdk);
        # get the configured reports
        return view('finance.reports', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showReportsManager(Request $request, Sdk $sdk, string $id)
    {
        $this->setViewUiResponse($request);
        $this->data['breadCrumbs']['crumbs'][1]['isActive'] = false;
        $this->data['breadCrumbs']['crumbs'][] = [
            'text' => 'Report Manager', 'href' => route('apps.finance.reports.documents', [$id]), 'isActive' => true
        ];
        $this->data['page']['title'] = 'Finance: Reports Manager';
        $reports = $this->getFinanceReportConfigurations($sdk);
        if (empty($reports)) {
            return redirect()->route('apps.finance.reports');
        }
        $this->data['report'] = $report = $reports->where('id', $id)->first();
        # find the report
        if (empty($report)) {
            abort(404, 'Page not found');
        }
        dd($report);
        $this->data['page']['header']['title'] = $report->display_name;
        return view('finance.reports-manager', $this->data);
    }
}
