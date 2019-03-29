<?php

namespace App\Http\Controllers\ECommerce;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ECommerce extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'eCommerce';
        $this->data['page']['header'] = ['title' => 'eCommerce'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'eCommerce', 'href' => route('apps.ecommerce'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'ecommerce';
        $this->data['selectedSubMenu'] = 'home';
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
        return view('ecommerce.landing', $this->data);
    }
}
