<?php

namespace App\Http\Controllers\Ajax\Crm;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class CustomFields extends Controller
{
    /**
     * ContactFields constructor.
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

        $model = $sdk->createContactFieldResource();
        $model = $model->addQueryArgument('limit', $limit)
                        ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $model = $model->addQueryArgument('search', $search);
        }
        $response = $model->send('get');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException('Could not find any matching contact fields.');
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
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, Sdk $sdk)
    {
        $name = $request->input('name', null);
        $model = $sdk->createContactFieldResource();
        $response = $model->addBodyParam('name', $name)
                            ->send('post');
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while creating the contact field.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.custom-fields.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
}
