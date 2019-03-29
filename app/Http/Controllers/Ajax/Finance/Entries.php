<?php

namespace App\Http\Controllers\Ajax\Finance;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Entries extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = [];
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, Sdk $sdk)
    {
        $search = $request->query('search', '');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $path = ['entries'];
        if ($request->has('account')) {
            $id = $request->account;
            # get the requested account id
            $path = ['accounts', $id, 'entries'];
        }
        $query = $sdk->createFinanceResource();
        $query = $query->addQueryArgument('include', 'account')
                        ->addQueryArgument('limit', $limit)
                        ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('get', $path);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching entries.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, Sdk $sdk, string $id)
    {
        $query = $sdk->createFinanceResource()->send('DELETE', ['entries', $id]);
        if (!$query->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($query->errors[0]['title'] ?? 'Could not delete the selected entry.');
        }
        return response()->json($query->getData());
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Sdk $sdk, string $id)
    {
        $payload = $request->only(['account']);
        # the data to be sent
        $query = $sdk->createFinanceResource()->relationships(['account']);
        foreach ($payload as $key => $value) {
            $query = $query->addBodyParam($key, $value);
        }
        $response = $query->send('PUT', ['entries', $id]);
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not update the details on the selected entry.');
        }
        return response()->json($response->getData());
    }
}
