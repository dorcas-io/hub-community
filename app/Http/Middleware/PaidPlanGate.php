<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;

class PaidPlanGate
{
    /**
     * Handle an incoming request.
     *
     * @param         $request
     * @param Closure $next
     * @param mixed   ...$plans
     *
     * @return mixed
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next, ...$plans)
    {
        if (!$request->user()) {
            throw new AuthenticationException();
        }
        $company = $request->user()->company();
        # get the company
        $cost = self::checkPricingOnCompanyPlan($company);
        # checks the cost on the pricing plan for this company
        $pricingTier = $company->plan['data'] ?? null;
        # get the pricing tier data
        if ($cost === null) {
            throw new AuthorizationException('We could not find a subscription for your account.');
        }
        if (empty($plans) && $cost <= 0) {
            # no specific plans specified, we just make sure it's not a free plan
            throw new AuthorizationException('You do not have access to this feature. Please upgrade to a paid plan.');
        }
        if (!empty($plans) && !in_array($pricingTier['name'], $plans, true)) {
            # we check based on the plan names
            throw new AuthorizationException(
                'You need to be on one of the following plans to access this feature: '.
                implode(', ', array_map('title_case', $plans))
            );
        }
        return $next($request);
    }
    
    /**
     * Checks the plan for a company.
     *
     * @param $company
     *
     * @return float|null
     */
    public static function checkPricingOnCompanyPlan($company)
    {
        $company = is_array($company) ? (object) $company : $company;
        # convert it to an stdClass instance
        $pricingTier = $company->plan['data'] ?? null;
        # get the pricing tier data
        if (empty($pricingTier)) {
            return null;
        }
        return (float) $pricingTier['price_monthly']['raw'];
    }
}
