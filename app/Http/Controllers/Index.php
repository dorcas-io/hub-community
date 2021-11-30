<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Hostville\Dorcas\Sdk;
use Carbon\Carbon;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;

class Index extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['dorcasEdition'] = env('DORCAS_EDITION', "business");
        // partner logo: $appUiSettings['product_logo'] = !empty($this->partner->logo) ? $this->partner->logo : null;
        // individual business logo either $dorcasUser OR $company = $dorcasUser->company();
        $this->data['header']['logo'] = cdn('images/logo/login-logo_dorcas.png');
    }
    
    public function index(Request $request)
    {
        //die("hello");
        $domainInfo = $request->session()->get('domainInfo');
        //dd($domainInfo);

        $homeRoute = "index-" . $this->data['dorcasEdition'];

        $approvedHomeRoutes = ["index-business", "index-community", "index-ennterprise", "index-cloud"];

        if (in_array($homeRoute, $approvedHomeRoutes)) {
            return redirect(route($homeRoute));
        }

        # get the resolved domainInfo, if any
        if (empty($domainInfo) || $domainInfo->getService() === null) {
            if (Auth::check()) {
                return redirect(route('dashboard'));
            }
            return redirect(route('login'));
        }
        /*if (Auth::check()) {
            return redirect(route('dashboard'));
        }*/
    }

    public function indexBusiness(Request $request)
    {
        $this->data['header']['title'] = 'Dorcas Business :: Welcome';
        $this->setViewUiResponse($request);
        $sdk = app(Sdk::class);
        $authIndexes = \Dorcas\ModulesAuth\Http\Controllers\ModulesAuthController::getAuthIndex($request, $sdk, "business");
        $this->data['authIndexes'] = $authIndexes;
        return view('modules-auth::index-business', $this->data);

    }

    public function indexCommunity(Request $request)
    {
        $this->data['header']['title'] = 'Dorcas Community :: Welcome';
        $this->setViewUiResponse($request);
        $sdk = app(Sdk::class);
        $authIndexes = \Dorcas\ModulesAuth\Http\Controllers\ModulesAuthController::getAuthIndex($request, $sdk, "community");
        $this->data['authIndexes'] = $authIndexes;
        return view('modules-auth::index-community', $this->data);

    }

    public function indexEnterprise(Request $request)
    {
        $this->data['header']['title'] = 'Dorcas Enterprise :: Welcome';
        $this->setViewUiResponse($request);
        $sdk = app(Sdk::class);
        $authIndexes = \Dorcas\ModulesAuth\Http\Controllers\ModulesAuthController::getAuthIndex($request, $sdk, "enterprise");
        $this->data['authIndexes'] = $authIndexes;
        return view('modules-auth::index-enterprise', $this->data);

    }

    public function indexCloud(Request $request)
    {
        $this->data['header']['title'] = 'Dorcas Cloud :: Welcome';
        $this->setViewUiResponse($request);
        $sdk = app(Sdk::class);
        $authIndexes = \Dorcas\ModulesAuth\Http\Controllers\ModulesAuthController::getAuthIndex($request, $sdk, "cloud");
        $this->data['authIndexes'] = $authIndexes;
        return view('modules-auth::index-cloud', $this->data);

    }

}
