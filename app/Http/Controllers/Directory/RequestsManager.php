<?php

namespace App\Http\Controllers\Directory;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestsManager extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Service Requests';
        $this->data['page']['header'] = ['title' => 'Service Request Manager'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Requests', 'href' => route('directory.requests'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'professional_requests';
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
        return view('directory.requests', $this->data);
    }
}
