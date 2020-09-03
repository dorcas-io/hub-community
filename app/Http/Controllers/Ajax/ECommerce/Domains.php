<?php

namespace App\Http\Controllers\Ajax\ECommerce;

use App\Dorcas\Hub\Utilities\DomainManager\ResellerClubClient;
use App\Dorcas\Hub\Utilities\DomainManager\Upperlink;
use App\Events\ECommerce\DomainDelete;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Domains extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data = [];
    }
    
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAvailability(Request $request)
    {
        $extension = $request->input('extension', 'com');
        $manager = $extension === 'com.ng' ? new Upperlink() : new ResellerClubClient();
        $status = $manager->checkAvailability($request->domain, [$request->extension]);
        $response = [];
        foreach ($status as $domain => $info) {
            $response = $info;
            break;
        }
        return response()->json($response);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function releaseDomain(Request $request, Sdk $sdk, string $id)
    {
        $query = $sdk->createDomainResource($id)->send('delete');
        # make the request
        if (!$query->isSuccessful()) {
            // do something here
            throw new \RuntimeException($query->errors[0]['title'] ?? 'Something went wrong while deleting the domain.');
        }
        $domain = $query->getData();
        event(new DomainDelete($domain, $sdk->getAuthorizationToken()));
        # trigger the event
        $company = $this->getCompany();
        Cache::forget('ecommerce.domains.'.$company->id);
        return response()->json($domain);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function registerCustomer(Request $request, Sdk $sdk)
    {
        $company = $this->getCompany();
        # the company
        $manager = new ResellerClubClient();
        $customerId = $manager->registerCustomer($request->user(), $company);
        $extraData = (array) $company->extra_data;
        if (empty($extraData['reseller_club_customer_id'])) {
            $extraData['reseller_club_customer_id'] = $customerId;
            $sdk->createCompanyService()->addBodyParam('extra_data', $extraData)->send('PUT');
        }
        return response()->json(['customer_id' => $customerId]);
    }
}
