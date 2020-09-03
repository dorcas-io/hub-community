<?php

namespace App\Http\Controllers\Ajax\Crm;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Deals extends Controller
{
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, Sdk $sdk, string $id)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        
        $resource = $sdk->createCustomerResource($id);
        $resource = $resource->addQueryArgument('limit', $limit)
                                ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $resource = $resource->addQueryArgument('search', $search);
        }
        $response = $resource->send('get', ['deals']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException('Could not find any matching deals for the customer.');
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
    public function create(Request $request, Sdk $sdk, string $id)
    {
        $resource = $sdk->createCustomerResource($id);
        $data = $request->only(['name', 'value_currency', 'value_amount', 'note']);
        foreach ($data as $key => $value) {
            $resource->addBodyParam($key, $value);
        }
        $response = $resource->send('post', ['deals']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while creating the deal.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }
}
