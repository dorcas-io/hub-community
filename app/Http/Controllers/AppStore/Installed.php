<?php

namespace App\Http\Controllers\AppStore;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Installed extends Controller
{
    /**
     * Installed constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'My Installed Apps';
        $this->data['page']['header'] = ['title' => 'My Installed Apps'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'App Store', 'href' => route('app-store')],
                ['text' => 'My Apps', 'href' => route('app-store.installed'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'app-store';
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk)
    {
        $this->data['filter'] = 'installed_only';
        $this->data['authToken'] = $sdk->getAuthorizationToken();
        return view('app-store.listing', $this->data);
    }
}
