<?php

namespace App\Http\Controllers\Crm\ContactFields;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomField extends Controller
{
    /**
     * ContactFields constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Custom Fields';
        $this->data['page']['header'] = ['title' => 'Custom Fields'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'CRM', 'href' => route('apps.crm')],
                ['text' => 'Custom Fields', 'href' => route('apps.crm.custom-fields'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'crm';
        $this->data['selectedSubMenu'] = 'contact-fields';
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
        return view('crm.contact-fields.contact-fields', $this->data);
    }
}
