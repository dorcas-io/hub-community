<?php

namespace App\Http\Controllers\Ajax\Crm;


use App\Exceptions\RecordNotFoundException;
use App\Http\Controllers\Controller;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;

class Customers extends Controller
{
    /**
     * Customers constructor.
     */
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
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $groups = $request->input('groups');

        $model = $sdk->createCustomerResource();
        $model = $model->addQueryArgument('limit', $limit)
                            ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($groups)) {
            $model->addQueryArgument('groups', $groups);
        }
        if (!empty($search)) {
            $model = $model->addQueryArgument('search', $search);
        }
        $response = $model->send('get');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching customer records.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }
}