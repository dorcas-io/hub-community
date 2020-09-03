<?php

namespace App\Dorcas\Hub\Utilities\DomainManager;


use afbora\ResellerClub\ResellerClub;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUser;

class ResellerClubClient implements DomainManagerInterface
{
    /** @var ResellerClub  */
    private $apiClient;
    
    /** @var array  */
    protected $nameServers = [
        'ns1.hostvillenigeria.website'
    ];
    
    /**
     * ResellerClub constructor.
     *
     * @param string $env
     */
    public function __construct(string $env = null)
    {
        $isTestMode = app()->environment() !== 'production';
        $this->apiClient = new ResellerClub(
            config('services.reseller_club.id'),
            config('services.reseller_club.api_key'),
            $isTestMode
        );
    }
    
    /**
     * @return ResellerClub
     */
    public function getClient(): ResellerClub
    {
        return $this->apiClient;
    }
    
    /**
     * Returns an array of the TLDs supported by the manager.
     *
     * @return array
     */
    public function getSupportedExtensions(): array
    {
        return ['com'];
    }
    
    /**
     * Checks for the availability of the provided domain, with the specified extensions.
     *
     * @param string $domain
     * @param array  $extensions
     *
     * @return array
     */
    public function checkAvailability(string $domain, array $extensions = []): array
    {
        if (empty($domain)) {
            throw new \InvalidArgumentException('The domain name should not be empty.');
        }
        $extensions = empty($extensions) ? $this->getSupportedExtensions() : $extensions;
        $extensions = array_intersect($extensions, $this->getSupportedExtensions());
        # remove all unsupported extensions
        $json = $this->apiClient->domains()->available([$domain], $extensions);
        $status = [];
        foreach ($json as $domain => $info) {
            $status[$domain] = ['is_available' => $info['status'] === 'available', 'domain' => $domain];
        }
        return $status;
    }
    
    /**
     * @param DorcasUser $user
     * @param \stdClass  $company
     *
     * @return mixed|\SimpleXMLElement
     */
    public function registerCustomer(DorcasUser $user, \stdClass $company)
    {
        $extraData = (array) $company->extra_data;
        if (!empty($extraData['reseller_club_customer_id'])) {
            return $extraData['reseller_club_customer_id'];
        }
        $customerId = $this->apiClient->customers()->signup(
            $user->email,
            '#' . str_replace(' ', '', title_case($company->name)) . '1',
            $user->firstname . ' ' . $user->lastname,
            $company->name,
            '1 Dorcas Hub',
            'Ikeja',
            'Lagos',
            'NG',
            100212,
            234,
            $user->phone,
            'en'
        );
        # we try to register the customer
        return $customerId;
    }
    
    /**
     * @param DorcasUser $user
     * @param \stdClass  $company
     * @param            $customerId
     *
     * @return mixed|\SimpleXMLElement
     */
    public function registerContact(DorcasUser $user, \stdClass $company, $customerId)
    {
        $extraData = (array) $company->extra_data;
        if (!empty($extraData['reseller_club_contact_id'])) {
            return $extraData['reseller_club_contact_id'];
        }
        $contactId = $this->apiClient->contacts()->add(
            $user->firstname . ' ' . $user->lastname,
            $company->name,
            $user->email,
            '1 Dorcas Hub',
            'Ikeja',
            'NG',
            100212,
            234,
            $user->phone,
            $customerId,
            'Contact'
        );
        # we try to register the customer
        return $contactId;
    }
    
    /**
     * Registers a new domain.
     *
     * @param string $domain
     * @param array  $config
     *
     * @return array
     */
    public function registerDomain(string $domain, array $config): array
    {
        $extension = $config['extension'] ?: $this->getSupportedExtensions()[0];
        # get the extension
        if (!in_array($extension, $this->getSupportedExtensions())) {
            throw new \InvalidArgumentException('You supplied an invalid domain TLD to the registrar.');
        }
        $domain .= '.' . $extension;
        # our now fully-defined domain
        $nameserver = is_array($config['ns']) ? $config['ns'][0] : 'ns1.bh-73.westhostbox.net';
        $response = $this->apiClient->domains()->register(
            $domain,
            1,
            $nameserver,
            $config['customer_id'] ?? '',
            $config['contact_id'] ?? '',
            $config['contact_id'] ?? '',
            $config['contact_id'] ?? '',
            $config['contact_id'] ?? '',
            $config['invoice_option'] ?? 'KeepInvoice'
        );
        return $response;
    }
}