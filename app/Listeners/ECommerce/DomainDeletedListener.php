<?php

namespace App\Listeners\ECommerce;

use App\Events\ECommerce\DomainDelete;
use App\Http\Controllers\ECommerce\Website;
use Hostville\Dorcas\Sdk;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class DomainDeletedListener implements ShouldQueue
{
    use InteractsWithQueue;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * Handle the event.
     *
     * @param DomainDelete $event
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \League\Flysystem\UnreadableFileException
     */
    public function handle(DomainDelete $event)
    {
        $sdk = app(Sdk::class);
        # get the sdk
        if (empty($event->token)) {
            return;
        }
        $sdk->setAuthorizationToken($event->token);
        $company = $this->getCompany($sdk);
        if (empty($company)) {
            return;
        }
        $domain = $event->domain;
        $companyConfig = (array) $company->extra_data;
        $hosting = $companyConfig['hosting'] ?? [];
        if (empty($hosting)) {
            # no hosting entries
            return;
        }
        $hostingAccount = collect($hosting)->filter(function ($d) use ($domain) {
            return $d['domain'] === $domain['domain'];
        })->first();
        # try to find the hosting account
        if (empty($hostingAccount)) {
            return;
        }
        $whmClient = Website::getWhmClient($hostingAccount['hosting_box_id']);
        if (!$whmClient->removeAccount($hostingAccount['username'])) {
            return;
        }
        $companyConfig['hosting'] = collect($hosting)->filter(function ($d) use ($domain) {
            return $d['domain'] !== $domain['domain'];
        })->all();
        # current hosting accounts
        $sdk->createCompanyService()->addBodyParam('extra_data', $companyConfig)->send('put');
        # we do a fire-and-forget to update the hosting information
    }
    
    /**
     * @param Sdk $sdk
     *
     * @return null|\stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getCompany(Sdk $sdk): ?\stdClass
    {
        $response = $sdk->createCompanyService()->send('get');
        # get the company data
        if (!$response->isSuccessful()) {
            return null;
        }
        return $response->getData(true);
    }
}
