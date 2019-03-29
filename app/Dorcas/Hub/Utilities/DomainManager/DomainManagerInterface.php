<?php

namespace App\Dorcas\Hub\Utilities\DomainManager;


use Hostville\Dorcas\LaravelCompat\Auth\DorcasUser;

interface DomainManagerInterface
{
    /**
     * Returns an array of the TLDs supported by the manager.
     *
     * @return array
     */
    public function getSupportedExtensions(): array;
    
    /**
     * Checks for the availability of the provided domain, with the specified extensions.
     *
     * @param string $domain
     * @param array  $extensions
     *
     * @return array
     */
    public function checkAvailability(string $domain, array $extensions = []): array;
    
    /**
     * Creates a new client/customer account on the hosting manager.
     *
     * @param DorcasUser $user
     * @param \stdClass  $company
     *
     * @return mixed
     */
    public function registerCustomer(DorcasUser $user, \stdClass $company);
    
    /**
     * Registers a new contact account on the hosting manager.
     *
     * @param DorcasUser $user
     * @param \stdClass  $company
     * @param            $customerId
     *
     * @return mixed
     */
    public function registerContact(DorcasUser $user, \stdClass $company, $customerId);
    
    /**
     * Registers a new domain.
     *
     * @param string $domain
     * @param array  $config
     *
     * @return array
     */
    public function registerDomain(string $domain, array $config): array;
}