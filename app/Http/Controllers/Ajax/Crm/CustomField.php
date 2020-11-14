<?php

namespace App\Http\Controllers\Ajax\Crm;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class CustomField extends Controller
{
    /**
     * ContactField constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data = [];
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
        $model = $sdk->createContactFieldResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Failed while deleting the contact field.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.custom-fields.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
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
        $model = $sdk->createContactFieldResource($id);
        $response = $model->addBodyParam('name', $request->input('name'))->send('put');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Failed while updating the custom field.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.custom-fields.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
}
