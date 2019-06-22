<?php

namespace App\Providers;


use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use Carbon\Carbon;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Money\Currencies\ISOCurrencies;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $currencies = collect(config('currencies', []));
            $view->with('isoCurrencies', $currencies->values()->sortBy('currency'));
            $request = app()->make('request');
            # the request instance
            $dorcasUser = $request->user();
            # get the authenticated user, if any
            $usingGrant = !empty($dorcasUser->meta);
            # probably using grants

//Try and factor in USer Role for $dorcasUserRole being currently used in tabler layout

            if (!empty($dorcasUser)) {
                if (!empty($dorcasUser->partner) && !empty($dorcasUser->partner['data'])) {
                    $isPartnerAdmin = false;
                    $userRoles = !empty($dorcasUser->roles) ? $dorcasUser->roles['data'] : [];
                    if (!empty($userRoles)) {
                        $partnerRole = collect($userRoles)->filter(function ($r) {
                            return $r['name'] === 'partner';
                        });
                        $isPartnerAdmin = $partnerRole->count() > 0;
                        if ($isPartnerAdmin) {
                            $request->session()->put('isPartnerAdministrator', true);
                        }
                    }
                    $partner = (object) $dorcasUser->partner['data'];
                    $partnerConfig = (array) $partner->extra_data;
                    $hubConfig = $partnerConfig['hubConfig'] ?? [];
                    $defaultProductName = !empty($partner) ? $partner->name : 'Hub';
                    $hubConfig['product_logo'] = !empty($partner->logo) ? $partner->logo : null;
                    $hubConfig['product_name'] = $hubConfig['product_name'] ?? $defaultProductName;
                    $view->with('partner', $partner);
                    $view->with('partnerHubConfig', $hubConfig);
                    $view->with('appUiSettings', $hubConfig);
                    $view->with('partnerHubProductName', $hubConfig['product_name'] ?? $defaultProductName);
                    $view->with('isPartnerAdministrator', $isPartnerAdmin);
                    if ($isPartnerAdmin) {
                        $vPanelUrl = generate_partner_url($partner, 'vpanel', ['token' => $dorcasUser->getDorcasSdk()->getAuthorizationToken()]);
                        $view->with('vPanelUrl', $vPanelUrl);
                    }
                }
                $company = $dorcasUser->company(true, true);
                $view->with('dorcasUser', $dorcasUser);
                $extraConfig = (array) $company->extra_data;
                $defaults = [
                    'business_size' => 1,
                    'business_type' => 'sole proprietorship',
                    'business_sector' => 'others',
                    'currency' => 'NGN',
                    'country_id' => '1f139cdb-b95e-11e7-8bef-a8e06b771503',
                    'state_id' => 'non-nigerian'
                ];
                foreach ($defaults as $key => $defaultValue) {
                    if (!empty($extraConfig[$key])) {
                        continue;
                    }
                    $extraConfig[$key] = $defaultValue;
                }
                $company->extra_data = $extraConfig;

                // add wallet data
                if (empty($extraConfig['wallet'])) {
                    $extraConfig['wallet'] = ['NGN' => ['balance' => 0 ]];
                }
                $wallet = $extraConfig['wallet']['NGN'];
                $company->extra_data = $extraConfig;


                $view->with('business', $company);
                
                $defaultUiConfiguration = collect(HomeController::SETUP_UI_COMPONENTS)->map(function ($ui) {
                    return $ui['id'];
                })->all();
                # the default UI configuration for user accounts - everything visible
                
                if (empty($dorcasUser->meta['granted_for'])) {
                    # not using grants -- regular users
                    $userConfigurations = (array) $dorcasUser->extra_configurations;
                    $userUiSetup = $userConfigurations['ui_setup'] ?? [];
                    $configurations = (array) $company->extra_data;
                    $view->with('UiUsesGrant', false);
                    if (!empty($userUiSetup)) {
                        $effectiveUiConfiguration = $userUiSetup;
                        $view->with('showUiModalAccessMenu', false);
                    } else {
                        $effectiveUiConfiguration = $configurations['ui_setup'] ?? $defaultUiConfiguration;
                        $effectiveUiConfiguration[] = 'settings';
                    }
                    
                } else {
                    $grant = $dorcasUser->meta['granted_for']['data'];
                    $effectiveUiConfiguration = $grant['extra_json']['modules'] ?? [];
                    $view->with('UiUsesGrant', true);
                }
                $view->with('UiConfiguration', $effectiveUiConfiguration);
                # set the UI configuration
                $accessExpires = Carbon::parse($company->access_expires_at);
                # the expiry date
                $planPrice = (float) $company->plan['data']['price_monthly']['raw'];
                $allowSwitch = $planPrice === 0 || Carbon::now()->greaterThan($accessExpires) || ($planPrice > 0 && Carbon::now()->greaterThan($accessExpires));
                $view->with('allowPlanSwitch', $allowSwitch);
                $view->with('isOnPaidPlan', $planPrice > 0);
                $view->with('isOnPremiumPlan', $company->plan['data']['name'] === 'premium');

                $view->with('dorcasSubdomain', get_dorcas_subdomain($dorcasUser->getDorcasSdk()));
                # set the dorcas.ng subdomain for the authenticated user
            }
            if ($request->hasSession() && $request->session()->has('pageMode')) {
                $view->with('pageMode', $request->session()->get('pageMode'));
            }
        });
        View::composer('crm.*', function ($view) {
            if (Auth::check()) {
                $company = Auth::user()->company(true, true);
                # get the company
                $contactFields = Cache::remember('business.custom-fields.'.$company->id, 30, function () {
                    $sdk = app(Sdk::class);
                    $response = $sdk->createContactFieldResource()->addQueryArgument('limit', 100)->send('get');
                    if (!$response->isSuccessful()) {
                        return null;
                    }
                    return collect($response->getData())->map(function ($customField) {
                        return (object) $customField;
                    });
                });
                $view->with('contactFields', $contactFields);
            }
        });
        View::composer('webstore.*', function ($view) {
            $company = (new Controller())->getCompanyViaDomain();
            $sdk = app(Sdk::class);
            $categories = Cache::remember('business.product-categories.'.$company->id, 30, function () use ($sdk, $company) {
                $query = $sdk->createStoreService()->send('GET', [$company->id, 'categories']);
                # get the response
                if (!$query->isSuccessful() || empty($query->getData())) {
                    return null;
                }
                return collect($query->getData())->map(function ($category) {
                    return (object) $category;
                });
            });
            $view->with('productCategories', $categories);
        });
        View::composer('blog.*', function ($view) {
            $company = (new Controller())->getCompanyViaDomain();
            $sdk = app(Sdk::class);
            $categories = Cache::remember('business.blog-categories.'.$company->id, 30, function () use ($sdk, $company) {
                $query = $sdk->createBlogResource()->send('GET', [$company->id, 'categories']);
                # get the response
                if (!$query->isSuccessful() || empty($query->getData())) {
                    return null;
                }
                return collect($query->getData())->map(function ($category) {
                    return (object) $category;
                });
            });
            $view->with('blogCategories', $categories);
        });
    }
}