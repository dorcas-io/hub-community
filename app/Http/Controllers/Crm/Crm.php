<?php

namespace App\Http\Controllers\Crm;


use App\Http\Controllers\Controller;

class Crm extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'CRM';
        $this->data['page']['header'] = ['title' => 'CRM'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'CRM', 'href' => route('apps.crm'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'crm';
    }

    public function index()
    {
        return view('crm.crm', $this->data);
    }
}