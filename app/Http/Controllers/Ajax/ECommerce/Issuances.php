<?php

namespace App\Http\Controllers\Ajax\ECommerce;

use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Issuances extends Controller
{
    /**
     * Issuances constructor.
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
     */
    public function checkAvailability(Request $request, Sdk $sdk)
    {
        $id = $request->query('id');
        if (empty($id)) {
            throw new \UnexpectedValueException('You need to provide the subdomain name');
        }
        # get the request parameters
        $query = $sdk->createDomainResource()->addQueryArgument('id', $id);
        if ($request->has('domain_id')) {
            $query = $query->addQueryArgument('domain_id', $request->query('domain_id'));
        }
        $query = $query->send('get', ['issuances/availability']);
        # make the request
        if (!$query->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($query->errors[0]['title'] ?? 'Something went wrong while checking availability of the subdomain.');
        }
        return response()->json($query->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function releaseSubdomain(Request $request, Sdk $sdk, string $id)
    {
        $query = $sdk->createDomainResource()->send('delete', ['issuances/'.$id]);
        # make the request
        if (!$query->isSuccessful()) {
            // do something here
            throw new \RuntimeException($query->errors[0]['title'] ?? 'Something went wrong while releasing the subdomain.');
        }
        $company = $this->getCompany();
        Cache::forget('ecommerce.subdomains.'.$company->id);
        return response()->json($query->getData());
    }
}
