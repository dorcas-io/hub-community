<?php

namespace App\Http\Controllers\Businesses\Employees;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class NewEmployee extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Add Employees';
        $this->data['page']['header'] = ['title' => 'Add Employees'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Business', 'href' => route('business')],
                ['text' => 'Employees', 'href' => route('business.employees')],
                ['text' => 'Add Employee', 'href' => route('business.employees.new'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'hr';
        $this->data['selectedSubMenu'] = 'employees';
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['departments'] = $this->getDepartments($sdk);
        $this->data['locations'] = $this->getLocations($sdk);
        return view('business.employees.new', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'firstname' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'gender' => 'nullable|string|in:female,male',
            'salary_amount' => 'nullable|numeric|min:0',
            'staff_code' => 'nullable|string|max:30',
            'job_title' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|min:11',
            'department' => 'nullable|string',
            'location' => 'nullable|string'
        ]);
        # validate the request
        $company = $request->user()->company(true, true);
        try {
            $model = $sdk->createEmployeeResource();
            $response = $model->addBodyParam('firstname', $request->input('firstname'))
                                ->addBodyParam('lastname', $request->input('lastname'))
                                ->addBodyParam('phone', $request->input('phone', ''))
                                ->addBodyParam('email', $request->input('email', ''))
                                ->addBodyParam('gender', $request->input('gender', 'male'))
                                ->addBodyParam('staff_code', $request->input('staff_code'))
                                ->addBodyParam('job_title', $request->input('job_title'))
                                ->addBodyParam('salary_amount', $request->input('salary_amount', 0))
                                ->addBodyParam('department', $request->input('department', ''))
                                ->addBodyParam('location', $request->input('location', ''))
                                ->send('post');
            # make the request
            if (!$response->isSuccessful()) {
                $message = $response->getErrors()[0]['title'] ?? 'Failed while adding the employee record.';
                throw new \UnexpectedValueException($message);
            }
            Cache::forget('business.employees.'.$company->id);
            $employee = $response->getData(true);
            $name = $employee->firstname . ' ' . $employee->lastname;
            $response = (material_ui_html_response(['Successfully added employee '.$name]))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
