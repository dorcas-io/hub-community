<?php

namespace App\Http\Controllers\Finance;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Csv\Reader;

class Entries extends Controller
{
    /**
     * Entries constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Finance: Entries';
        $this->data['page']['header'] = ['title' => 'Account Entries'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Finance', 'href' => route('apps.finance')],
                ['text' => 'Entries', 'href' => route('apps.finance.entries'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'finance';
        $this->data['selectedSubMenu'] = 'entries';
    }
    
    /**
     * @param Request     $request
     * @param Sdk         $sdk
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
        $entriesCount = 0;
        $path = ['entries'];
        $this->data['args'] = $request->query->all();
        if ($request->has('account')) {
            # return only the sub-accounts of the selected parent account
            $id = $request->account;
            # get the requested account id
            $baseAccount = $accounts->where('id', $id)->first();
            # get the base account
            if (empty($baseAccount)) {
                abort(500, 'Something went wrong while loading the page.');
            }
            $this->data['addEntryModalTitle'] = '';
            $path = ['accounts', $id, 'entries'];
            $accounts = collect([$baseAccount]);
            $appendName = $baseAccount->display_name;
            if (!empty($baseAccount->parent_account)) {
                $this->data['addEntryModalTitle'] .= $baseAccount->parent_account['data']['display_name'] . ' > ';
                $appendName .= ' (' . $baseAccount->parent_account['data']['display_name'].')';
            }
            $this->data['page']['header']['title'] .= ' - ' . $appendName;
            $this->data['breadCrumbs']['crumbs'][1][''] = false;
            $this->data['addEntryModalTitle'] .= $baseAccount->display_name;
            
        }
        $query = $sdk->createFinanceResource()->addQueryArgument('limit', 1)->send('get', $path);
        if ($query->isSuccessful()) {
            $entriesCount = $query->meta['pagination']['total'] ?? 0;
        }
        $this->data['entriesCount'] = $entriesCount;
        $this->data['accounts'] = $accounts->values();
        return view('finance.entries', $this->data);
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
        $this->validate($request, [
            'account' => 'required|string',
            'import_file' => 'required_if:action,save_entries|file|max:5120',
            'currency' => 'required_if:action,save_entry|string|size:3',
            'amount' => 'required_if:action,save_entry|numeric',
            'memo' => 'nullable|string',
            'created_at' => 'nullable|date_format:Y-m-d',
        ]);
        # validate the request
        $action = $request->input('action');
        try {
            $resource = $sdk->createFinanceResource();
            # the resource
            switch ($action) {
                case 'save_entries':
                    $file = $request->file('import_file');
                    if (empty($file)) {
                        throw new \RuntimeException('You need to upload a CSV containing the entries.');
                    }
                    $csv = Reader::createFromPath($file->getRealPath(), 'r');
                    $csv->setHeaderOffset(0);
                    $records = $csv->getRecords(['currency', 'amount', 'memo', 'source_type', 'source_info', 'created_at']);
                    $entries = [];
                    foreach ($records as $record) {
                        $entries[] = $record;
                    }
                    $resource->addBodyParam('account', $request->input('account'));
                    $resource->addBodyParam('entries', $entries);
                    $response = $resource->send('post', ['entries', 'bulk']);
                    # send the request
                    if (!$response->isSuccessful()) {
                        # it failed
                        $message = $response->errors[0]['title'] ?? '';
                        throw new \RuntimeException('Failed while adding the accounting entries. '.$message);
                    }
                    $response = (material_ui_html_response(['Successfully added new accounting entries.']))->setType(UiResponse::TYPE_SUCCESS);
                    break;
                case 'save_entry':
                default:
                    $payload = $request->request->all();
                    foreach ($payload as $key => $value) {
                        $resource = $resource->addBodyParam($key, $value);
                    }
                    $response = $resource->send('post', ['entries']);
                    # send the request
                    if (!$response->isSuccessful()) {
                        # it failed
                        $message = $response->errors[0]['title'] ?? '';
                        throw new \RuntimeException('Failed while adding the accounting entry. '.$message);
                    }
                    $response = (material_ui_html_response(['Successfully added new accounting entry.']))->setType(UiResponse::TYPE_SUCCESS);
                    break;
            }
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        $args = $request->query->all();
        return redirect(url()->current() . '?' . http_build_query($args))->with('UiResponse', $response);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function showEntry(Request $request, Sdk $sdk, string $id)
    {
        $this->data['breadCrumbs']['crumbs'][1]['isActive'] = false;
        $this->data['breadCrumbs']['crumbs'][] = [
            'text' => 'Confirm Entry',
            'href' => route('apps.finance.entry.confirmation', [$id]),
            'isActive' => true
        ];
        # adjust the breadcrumbs for the page
        $accounts = $this->getFinanceAccounts($sdk);
        # get all accounts - first
        if (empty($accounts) || $accounts->count() === 0) {
            return redirect(route('apps.finance'));
        }
        $this->data['accounts'] = $accounts->filter(function ($account) {
            return empty($account->parent_account) && empty($account->parent_account['data']);
        });
        $query = $sdk->createFinanceResource()->addQueryArgument('include', 'account')
                                                ->send('GET', ['entries', $id]);
        # get the response
        if (!$query->isSuccessful()) {
            return redirect()->route('apps.finance.entries');
        }
        $this->data['entry'] = $entry = $query->getData(true);
        # get the entry information
        return view('finance.entry-confirmation', $this->data);
    }
}
