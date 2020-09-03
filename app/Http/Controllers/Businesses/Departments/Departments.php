<?php

namespace App\Http\Controllers\Businesses\Departments;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Departments extends Controller
{
    /**
     * Departments constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Departments';
        $this->data['page']['header'] = ['title' => 'Departments'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Business', 'href' => route('business')],
                ['text' => 'Departments', 'href' => route('business.departments'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'hr';
        $this->data['selectedSubMenu'] = 'departments';
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
        return view('business.departments.departments', $this->data);
    }

}
