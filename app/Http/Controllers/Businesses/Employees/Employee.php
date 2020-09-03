<?php

namespace App\Http\Controllers\Businesses\Employees;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Employee extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Employee Profile';
        $this->data['page']['header'] = ['title' => 'Employee Profile'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Business', 'href' => route('business')],
                ['text' => 'Employees', 'href' => route('business.employees')],
                ['text' => 'Profile', 'href' => route('business.employees'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'hr';
        $this->data['selectedSubMenu'] = 'employees';
    }
    
    /**
     * @param Sdk    $sdk
     * @param string $id
     *
     * @return \stdClass|null
     */
    protected function getEmployee(Sdk $sdk, string $id)
    {
        $query = $sdk->createEmployeeResource($id)->relationships([
                                                        'teams' => ['paginate' => ['limit' => 1000]]
                                                    ])
                                                    ->send('get');
        if (!$query->isSuccessful()) {
            $message = $query->getErrors()[0]['title'] ?? 'Failed while reading the employee information.';
            abort(500, $message);
        }
        return $query->getData(true);
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
        $this->data['employee'] = $employee = $this->getEmployee($sdk, $id);
        if (!empty($employee->user) && !empty($employee->user['data'])) {
            $configurations = (array) $employee->user['data']['extra_configurations'];
            $currentUiSetup = $configurations['ui_setup'] ?? [];
            $this->data['setupUiFields'] = collect(HomeController::SETUP_UI_COMPONENTS)->map(function ($field) use ($currentUiSetup) {
                if (!empty($field['is_readonly'])) {
                    return $field;
                }
                if (empty($currentUiSetup)) {
                    return $field;
                }
                $field['enabled'] = in_array($field['id'], $currentUiSetup);
                return $field;
            });
            # add the UI components
        }
        return view('business.employees.employee', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'email' => 'required_if:action,create_user|email',
            'password' => 'required_if:action,create_user|string',
            'firstname' => 'required_if:action,create_user|string|max:30',
            'lastname' => 'required_if:action,create_user|string|max:30',
            'phone' => 'required_if:action,create_user|string|max:30',
            'selected_apps' => 'required_if:action,update_module_access|array',
            'selected_apps.*' => 'string'
        ]);
        # validate the request
        $action =  $request->input('action');
        $employee = $this->getEmployee($sdk, $id);
        try {
            if ($action === 'create_user') {
                # create a user account
                $service = $sdk->createCompanyService()->addBodyParam('employee_id', $id);
                # send the request
                $data = $request->except(['_token', 'action']);
                foreach ($data as $key => $value) {
                    $service->addBodyParam($key, $value);
                }
                $query = $service->send('post', ['users']);
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException($query->getErrors()[0]['title'] ?? 'Failed while creating user account. Please try again.');
                }
                $message = ['Successfully created the user account for this employee.'];
                
            } else {
                # update address information
                $configurations = (array) $employee->user['data']['extra_configurations'];
    
                $readonlyExtend = collect(HomeController::SETUP_UI_COMPONENTS)->filter(function ($field) {
                    return !empty($field['is_readonly']) && !empty($field['enabled']);
                })->pluck('id');
                # get the enabled-readonly values
    
                $readonlyRemovals = collect(HomeController::SETUP_UI_COMPONENTS)->filter(function ($field) {
                    return !empty($field['is_readonly']) && empty($field['enabled']);
                })->pluck('id');
                # get the disabled-readonly values
    
                $selectedApps = collect($request->input('selected_apps', []))->merge($readonlyExtend);
                # set the selected apps
    
                $selectedApps = $selectedApps->filter(function ($id) use ($readonlyRemovals) {
                    return !$readonlyRemovals->contains($id);
                });
                # remove them
    
                $configurations['ui_setup'] = $selectedApps->unique()->all();
                
                $user = (object) $employee->user['data'];
                
                $query = $sdk->createUserResource($user->id)->addBodyParam('extra_configurations', $configurations, true)
                                                            ->send('PUT');
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while updating The employee\'s module access. Please try again.');
                }
                $message = ['Successfully updated module access for this '.$employee->firstname];
                
            }
            $response = (material_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
