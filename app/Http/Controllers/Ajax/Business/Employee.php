<?php

namespace App\Http\Controllers\Ajax\Business;

use App\Exceptions\DeletingFailedException;
use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Employee extends Controller
{
    /**
     * Employee constructor.
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
        $query = $sdk->createEmployeeResource()
                        ->addBodyParam('firstname', $request->input('firstname'))
                        ->addBodyParam('lastname', $request->input('lastname'))
                        ->addBodyParam('phone', $request->input('phone'))
                        ->addBodyParam('email', $request->input('email'))
                        ->addBodyParam('staff_code', $request->input('staff_code'))
                        ->addBodyParam('job_title', $request->input('job_title'))
                        ->addBodyParam('salary_amount', $request->input('salary_amount', 0));
        if ($request->has('salary_period') && !empty($request->salary_period)) {
            $query = $query->addBodyParam('salary_period', $request->salary_period);
        }
        if ($request->has('gender') && !empty($request->gender)) {
            $query = $query->addBodyParam('gender', $request->gender);
        }
        if ($request->has('department') && !empty($request->department)) {
            $query = $query->addBodyParam('department', $request->department);
        }
        if ($request->has('location') && !empty($request->location)) {
            $query = $query->addBodyParam('location', $request->location);
        }
        $query = $query->send('POST');
        # send request
        if (!$query->isSuccessful()) {
            $message = $query->getErrors()[0]['title'] ?? 'Failed while trying to add the employee.';
            throw new \RuntimeException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.employees.'.$company->id);
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
        $model = $sdk->createEmployeeResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while deleting the employee.';
            throw new RecordNotFoundException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.employees.'.$company->id);
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
        $model = $sdk->createEmployeeResource($id);
        $response = $model->addBodyParam('firstname', $request->input('firstname'))
                            ->addBodyParam('lastname', $request->input('lastname'))
                            ->addBodyParam('gender', $request->input('gender'))
                            ->addBodyParam('phone', $request->input('phone'))
                            ->addBodyParam('email', $request->input('email'))
                            ->addBodyParam('staff_code', $request->input('staff_code'))
                            ->addBodyParam('job_title', $request->input('job_title'))
                            ->addBodyParam('salary_amount', $request->input('salary_amount', 0))
                            ->send('put');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while updating the employee information.';
            throw new RecordNotFoundException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.employees.'.$company->id);
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
    public function removeTeams(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'teams' => 'required|array',
            'teams.*' => 'string'
        ]);
        # validate the request
        $model = $sdk->createEmployeeResource($id);
        $response = $model->addBodyParam('teams', $request->input('teams', []))
                            ->send('delete', ['teams']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while removing the teams(s) for the employee.';
            throw new DeletingFailedException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.employees.'.$company->id);
        Cache::forget('business.teams.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
}
