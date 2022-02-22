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
use Illuminate\Support\Collection;
use Hostville\Dorcas\Sdk;
use Carbon\Carbon;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Route;


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

        // Get Store URL Settings
        // $defaultUri = new Uri(config('app.url'));
        // $currentHost = $defaultUri->getHost();
        // $currentScheme = $defaultUri->getScheme();
        
        $currentHost = $request->header('host');
        $currentScheme = $request->secure() ? "https" : "http";

        $standardHost = env('STANDARD_HOST', 'dorcas.io'); //DORCAS_BASE_DOMAIN
        
        $storeSubDomain = $currentHost == $standardHost ? $currentScheme . '://' . 'store.' . $currentHost : '#';
        $hubLogin = $currentScheme . '://' . $currentHost . '/login';

        //optionally modify authIndexes item(s)
        $authIndexes = $this->modifyAuthIndex($authIndexes, "login", "title", "Login to Hub");
        $authIndexes = $this->modifyAuthIndex($authIndexes, "login", "image_link", $hubLogin);
        $authIndexes = $this->modifyAuthIndex($authIndexes, "store", "title", "View Your Store");
        $authIndexes = $this->modifyAuthIndex($authIndexes, "store", "image_link", $storeSubDomain);

        
        
        $this->data['authIndexes'] = $authIndexes;
        return view('modules-auth::index-business', $this->data);

    }

    public function indexCommunity(Request $request)
    {
        $this->data['header']['title'] = 'Dorcas Community :: Welcome';
        $this->setViewUiResponse($request);

        $sdk = app(Sdk::class);
        $authIndexes = \Dorcas\ModulesAuth\Http\Controllers\ModulesAuthController::getAuthIndex($request, $sdk, "community");

        $currentHost = $request->header('host');
        $currentScheme = $request->secure() ? "https" : "http";

        $standardHost = env('STANDARD_HOST', 'dorcas.io'); //DORCAS_BASE_DOMAIN

        //check if partner account setup
        
        $marketSubDomain = $currentHost == $standardHost ? $currentScheme . '://' . 'market.' . $currentHost : '#';
        $hubLogin = $currentScheme . '://' . $currentHost . '/login';
        
        //optionally modify authIndexes item(s)
        $authIndexes = $this->modifyAuthIndex($authIndexes, "login", "title", "Login to Hub");
        $authIndexes = $this->modifyAuthIndex($authIndexes, "login", "image_link", $hubLogin);
        $authIndexes = $this->modifyAuthIndex($authIndexes, "marketplace", "title", "View Marketplace");
        $authIndexes = $this->modifyAuthIndex($authIndexes, "marketplace", "image_link", $marketSubDomain);
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

    public function modifyAuthIndex(Collection $authIndexes, string $item, string $key, string $value): ?Collection
    {
        $authIndexesModified = $authIndexes->map(function ($authIndex, $authIndexKey) use($item, $key, $value) {
            if ($authIndexKey == $item) {
                $authIndex->{$key} = $value;
            }
        });
        
        return $authIndexes;

    }
}
