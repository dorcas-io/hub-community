<?php

namespace App\Http\Controllers\Businesses\Employees;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Employees extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Employees';
        $this->data['page']['header'] = ['title' => 'Employees'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Business', 'href' => route('business')],
                ['text' => 'Employees', 'href' => route('business.employees'), 'isActive' => true],
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
        $this->data['employees'] = $this->getEmployees($sdk);
        $this->data['departments'] = $this->getDepartments($sdk);
        $this->data['locations'] = $this->getLocations($sdk);
        return view('business.employees.employees', $this->data);
    }
}
