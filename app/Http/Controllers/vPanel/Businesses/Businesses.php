<?php

namespace App\Http\Controllers\vPanel\Businesses;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Businesses extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => 'Businesses'],
            'header' => ['title' => 'Businesses'],
            'selectedMenu' => 'businesses',
        ];
    }
    
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setViewUiResponse($request);
        return view('vpanel.businesses.listing', $this->data);
    }
}
