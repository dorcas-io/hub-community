<?php

namespace App\Http\Controllers\AppStore;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Listing extends Controller
{
    /**
     * Listing constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Applications Store';
        $this->data['page']['header'] = ['title' => 'App Store'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'App Store', 'href' => route('app-store'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'app-store';
    }
    
    public function index(Request $request, Sdk $sdk)
    {
        $this->data['filter'] = 'without_installed';
        $this->data['showAppsButton'] = true;
        $this->data['authToken'] = $sdk->getAuthorizationToken();
        return view('app-store.listing', $this->data);
    }
}
