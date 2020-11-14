<?php

namespace App\Http\Controllers\Ajax\Business;

use App\Exceptions\DeletingFailedException;
use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Department extends Controller
{
    /**
     * Department constructor.
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
    public function create(Request $request, Sdk $sdk)
    {
        $query = $sdk->createDepartmentResource()->addBodyParam('name', $request->input('name'))
                                                    ->send('POST');
        # send request
        if (!$query->isSuccessful()) {
            $message = $query->getErrors()[0]['title'] ?? 'Failed while trying to create the department.';
            throw new \RuntimeException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.departments.'.$company->id);
        return response()->json($query->getData());
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
        $model = $sdk->createDepartmentResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while deleting the department.';
            throw new RecordNotFoundException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.departments.'.$company->id);
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
        $model = $sdk->createDepartmentResource($id);
        $response = $model->addBodyParam('name', $request->input('name', ''))
                            ->addBodyParam('description', $request->input('description', ''))
                            ->send('put');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while updating the department.';
            throw new RecordNotFoundException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.departments.'.$company->id);
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
    public function removeEmployees(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'employees' => 'required|array',
            'employees.*' => 'string'
        ]);
        # validate the request
        $model = $sdk->createDepartmentResource($id);
        $response = $model->addBodyParam('employees', $request->input('employees', []))
                            ->send('delete', ['employees']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while removing the employee(s) from the department.';
            throw new DeletingFailedException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.departments.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
}
