<?php

namespace App\Http\Controllers\Businesses\Departments;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Department extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Department';
        $this->data['page']['header'] = ['title' => 'Department'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Business', 'href' => route('business')],
                ['text' => 'Departments', 'href' => route('business.departments')],
                ['text' => 'Department', 'href' => route('business.departments'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'hr';
        $this->data['selectedSubMenu'] = 'departments';
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk, string $id)
    {
        $this->setViewUiResponse($request);
        $query = $sdk->createDepartmentResource($id)
                        ->addQueryArgument('include', 'employees:limit(10000|0)')
                        ->send('get');
        # try to get the department information
        $this->data['department'] = $department = $query->getData(true);
        # get the information
        $employees = $this->getEmployees($sdk);
        $this->data['noEmployeesMessage'] = !empty($employees) && $employees->count() > 0 ?
            'All your employees are already in this department.' : 'You can start by adding one or more employees to your records.';
        # a message to display when the employees list is empty after filtering
        if (!empty($employees) && $employees->count() > 0) {
            $employees = $employees->filter(function ($employee) use ($department) {
                if (empty($employee->department['data'])) {
                    return true;
                }
                return $employee->department['data']['id'] !== $department->id;
            });
        }
        $this->data['employees'] = $employees;
        $this->data['page']['title'] .= ' - '.$department->name;
        $this->data['breadCrumbs']['crumbs'][2]['text'] = $department->name;
        return view('business.departments.department', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function post(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'employees' => 'required_with:add_employees|array',
            'employees.*' => 'string'
        ]);
        # validate the request
        $company = $request->user()->company(true, true);
        try {
            if ($request->has('add_employees')) {

                $query = $sdk->createDepartmentResource($id)->addBodyParam('employees', $request->employees)
                    ->send('post', ['employees']);
                # make the request
                if (!$query->isSuccessful()) {
                    $message = $query->getErrors()[0]['title'] ?? 'Failed while adding the employee record.';
                    throw new \RuntimeException($message);
                }
                Cache::forget('business.employees.'.$company->id);
                $response = (material_ui_html_response(['Successfully added the employees to the department.']))->setType(UiResponse::TYPE_SUCCESS);
            }
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
