<?php

namespace App\Http\Controllers\Ajax;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccessGrantRequests extends Controller
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
        $search = $request->query('search');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $resource = $sdk->createCompanyService();
        $resource = $resource->addQueryArgument('limit', $limit)
                                ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $resource->addQueryArgument('search', $search);
        }
        if ($request->has('statuses')) {
            $resource->addQueryArgument('statuses', $request->input('statuses'));
        }
        $response = $resource->send('get', ['access-grant-requests']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching requests.');
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteRequest(Request $request, Sdk $sdk, string $id)
    {
        $resource = $sdk->createCompanyService();
        $response = $resource->send('delete', ['access-grant-requests/' . $id]);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find delete the requests.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByUser(Request $request, Sdk $sdk)
    {
        $search = $request->query('search');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $resource = $sdk->createProfileService();
        $resource = $resource->addQueryArgument('limit', $limit)
                                ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $resource->addQueryArgument('search', $search);
        }
        if ($request->has('statuses')) {
            $resource->addQueryArgument('statuses', $request->input('statuses'));
        }
        $response = $resource->send('get', ['access-requests']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching requests.');
        }
        $json = json_decode($response->getRawResponse(), true);
        return response()->json($json);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteRequestForUser(Request $request, Sdk $sdk, string $id)
    {
        $resource = $sdk->createProfileService();
        $response = $resource->send('delete', ['access-requests/' . $id]);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find delete the requests.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }
}
