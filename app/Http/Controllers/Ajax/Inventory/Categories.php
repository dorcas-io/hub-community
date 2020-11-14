<?php

namespace App\Http\Controllers\Ajax\Inventory;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Categories extends Controller
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(Request $request, Sdk $sdk)
    {
        $name = $request->input('name', null);
        $response = $sdk->createProductCategoryResource()->addBodyParam('name', $name)->send('POST');
        # send the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while creating the product category.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.product-categories.'.$company->id);
        $this->data = $response->getData();
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
    public function delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createProductCategoryResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Failed while deleting the category.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.product-categories.'.$company->id);
        $this->data = $response->getData();
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
    public function update(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createProductCategoryResource($id);
        $response = $model->addBodyParam('name', $request->input('name'))
                            ->addBodyParam('slug', $request->input('slug'))
                            ->addBodyParam('description', $request->input('description'))
                            ->addBodyParam('update_slug', $request->input('update_slug'))
                            ->send('PUT');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Failed while updating the category.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.product-categories.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
}
