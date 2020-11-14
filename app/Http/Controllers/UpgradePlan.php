<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;

class UpgradePlan extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Plans';
        $this->data['page']['header'] = ['title' => 'Plans'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Plans', 'href' => route('plans'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'upgrade-plan';
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk)
    {
        $company = $this->getCompany();
        $accessExpires = Carbon::parse($company->access_expires_at);
        # the expiry date
        if ($company->plan['data']['price_monthly']['raw'] > 0 && Carbon::now()->lessThan($accessExpires)) {
            return redirect()->route('home');
        }
        $plans = config('dorcas.plans');
        # get the plans configuration
        $dorcasPlans = $this->getPricingPlans($sdk);
        # get the plans from Dorcas
        $pricingPlans = [];
        # the pricing plans
        foreach ($plans as $name => $plan) {
            $live = $dorcasPlans->where('name', $name)->first();
            # get the plan
            if (empty($live)) {
                continue;
            }
            $temp = array_merge($plan, ['name' => $name]);
            $temp['profile'] = $live;
            $pricingPlans[] = $temp;
        }
        $this->data['plans'] = collect($pricingPlans)->map(function ($plan) {
            return (object) $plan;
        });
        return view('plans', $this->data);
    }
}
