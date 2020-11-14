<?php

namespace App\Http\Controllers\Businesses;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Business extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'My Business';
        $this->data['page']['header'] = ['title' => 'My Business'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Business', 'href' => route('business'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'hr';
    }

    public function index(Request $request)
    {
        $company = $request->user()->company(true, true);
        $this->data['page']['header']['title'] = $company->name;
        return view('business.dashboard', $this->data);
    }
}
