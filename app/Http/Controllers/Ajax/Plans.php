<?php

namespace App\Http\Controllers\Ajax;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Plans extends Controller
{
    /**
     * Plans constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data = [];
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function switch(Request $request, Sdk $sdk)
    {
        $plans = array_keys(config('dorcas.plans'));
        # the allowed keys
        $this->validate($request, [
            'plan' => 'required|string|in:'.implode(',', $plans)
        ]);
        # validate the request
        $company = $request->user()->company(true, true);
        # get the company
        $upgradeQuery = $sdk->createCompanyResource($company->id)->addBodyParam('plan', $request->plan)
                                                                ->send('post', ['update-plan']);
        if (!$upgradeQuery->isSuccessful()) {
            $message = $upgradeQuery->getErrors()[0]['title'] ?? 'Failed while trying to update your account plan.';
            throw new \RuntimeException($message);
        }
        # next up - we need to update the company information
        return response()->json($upgradeQuery->getData());
    }
}
