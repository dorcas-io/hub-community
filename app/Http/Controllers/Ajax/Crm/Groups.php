<?php

namespace App\Http\Controllers\Ajax\Crm;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Groups extends Controller
{
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
        $model = $sdk->createGroupResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Failed while deleting the group.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('crm.groups.'.$company->id);
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
    public function deleteCustomers(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createGroupResource($id)->addBodyParam('customers', $request->input('customers', []));
        $response = $model->send('delete', ['customers']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException(
                $response->errors[0]['title'] ?? 'Failed while deleting the '.
                str_plural('customer', count($request->input('customers', []))) .' from the group.'
            );
        }
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
    public function addCustomers(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createGroupResource($id)->addBodyParam('customers', $request->input('customers', []));
        $response = $model->send('post', ['customers']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException(
                $response->errors[0]['title'] ?? 'Failed while adding the '.
                str_plural('customer', count($request->input('customers', []))) .' to the group.'
            );
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }
}
