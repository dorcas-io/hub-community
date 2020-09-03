<?php

namespace App\Http\Controllers\Ajax\Crm;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Customer extends Controller
{
    /**
     * Customer constructor.
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
        $model = $sdk->createCustomerResource($id);
        $response = $model->send('delete');
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the customer information.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.customers.'.$company->id);
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
        $model = $sdk->createCustomerResource($id);
        $response = $model->addBodyParam('firstname', $request->input('firstname'))
                            ->addBodyParam('lastname', $request->input('lastname'))
                            ->addBodyParam('email', $request->input('email'))
                            ->addBodyParam('phone', $request->input('phone'))
                            ->send('put');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while saving the customer information.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.customers.'.$company->id);
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
    public function deleteNote(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createCustomerResource($id);
        $response = $model->addBodyParam('id', $request->input('id'))
                            ->send('delete', ['notes']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the customer note.');
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
     */
    public function readNotes(Request $request, Sdk $sdk, string $id)
    {
        $response = $sdk->createCustomerResource($id)->addQueryArgument('limit', 10000)->send('get', ['notes']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while reading the customer note.');
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
     */
    public function addNote(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createCustomerResource($id);
        $response = $model->addBodyParam('note', $request->input('note'))
                            ->send('post', ['notes']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while saving the customer note.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }
}
