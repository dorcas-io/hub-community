<?php

namespace App\Http\Controllers\Ajax\Finance;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Accounts extends Controller
{
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
     */
    public function install(Request $request, Sdk $sdk)
    {
        $query = $sdk->createFinanceResource()->send('post', ['install']);
        # make the request
        if (!$query->isSuccessful()) {
            // do something here
            throw new \RuntimeException(
                $query->errors[0]['title'] ?? 'Something went wrong while installing finance for your account.'
            );
        }
        $company = $request->user()->company(true, true);
        # get the company
        Cache::forget('finance.accounts.'.$company->id);
        return response()->json($query->getData());
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Sdk $sdk, string $id)
    {
        $query = $sdk->createFinanceResource();
        $payload = $request->only(['entry_type', 'display_name', 'is_visible']);
        foreach ($payload as $key => $value) {
            $query = $query->addBodyParam($key, $value);
        }
        $response = $query->send('PUT', ['accounts', $id]);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException(
                $query->errors[0]['title'] ?? 'Something went wrong while updating the account information.'
            );
        }
        $company = $request->user()->company(true, true);
        # get the company
        Cache::forget('finance.accounts.'.$company->id);
        return response()->json($response->getData());
    }
}
