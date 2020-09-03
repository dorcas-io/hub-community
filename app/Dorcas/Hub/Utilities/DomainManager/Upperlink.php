<?php

namespace App\Dorcas\Hub\Utilities\DomainManager;


use App\Dorcas\Hub\Utilities\DomainManager\DomainManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use Hostville\Dorcas\LaravelCompat\Auth\DorcasUser;
use Illuminate\Support\Facades\Log;

class Upperlink implements DomainManagerInterface
{
    const API_ENDPOINT = 'https://domains.upperlink.ng';
    
    /** @var Client  */
    private $apiClient;
    
    /** @var array  */
    protected $payload = [];
    
    /**
     * Upperlink constructor.
     *
     * @param string|null $env
     */
    public function __construct(string $env = null)
    {
        $this->apiClient = new Client([
            'base_uri' => self::API_ENDPOINT,
            RequestOptions::CONNECT_TIMEOUT => 30.0,
            RequestOptions::VERIFY => false,
            RequestOptions::HEADERS => [
                'User-Agent' => 'Dorcas Upperlink Client/1.0'
            ]
        ]);
        $this->payload = [
            'username' => config('services.upperlink.secret'),
            'password' => config('services.upperlink.identifier'),
            'response_type' => 'json',
        ];
    }
    
    /**
     * @return Client
     */
    public function getClient(): Client
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
        return ['com.ng'];
    }
    
    /**
     * Sends the request to the API.
     *
     * @param string $action
     * @param array  $payload
     *
     * @return array|bool
     */
    protected function sendPost(string $action, array $payload)
    {
        $payload = array_merge($this->payload, $payload);
        # we merge both data sources
        $payload['action'] = $action;
        # set the action
        try {
            $response = $this->apiClient->post('/clients/includes/uplregistrar.php', [
                RequestOptions::FORM_PARAMS => $payload
            ]);
            $string = (string) $response->getBody();
            
        } catch (ClientException $e) {
            $string = (string) $e->getResponse()->getBody();
            Log::error('Upperlink::sendPost(' . $action . '): ClientError ' . $e->getResponse()->getStatusCode() . ' : ' . $e->getMessage(), ['response' => $string]);
        } catch (ServerException $e) {
            $string = (string) $e->getResponse()->getBody();
            Log::error('Upperlink::sendPost(' . $action . '): ServerError ' . $e->getResponse()->getStatusCode() . ' : ' . $e->getMessage(), ['response' => $string]);
        }
        $json = json_decode($string, true);
        if (!empty($json['error']) || $json['result'] !== 'success') {
            throw new \RuntimeException($json['message'] ?? 'The request could not be completed!');
        }
        return $json;
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
        $domainName = $domain . '.' . ($extensions[0] ?? $this->getSupportedExtensions()[0]);
        # our request domain
        $response = $this->sendPost('CheckAvailability', ['domain' => $domainName]);
        # send the request
        return [$domain => ['is_available' => $response['status'] === 'available', 'domain' => $domainName]];
    }
    
    /**
     * Creates a new client/customer account on the hosting manager.
     *
     * @param DorcasUser $user
     * @param \stdClass  $company
     *
     * @return string|int
     */
    public function registerCustomer(DorcasUser $user, \stdClass $company)
    {
        $customer = [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'companyname' => $company->name,
            'email' => $user->email,
            'password2' => '#' . str_replace(' ', '', title_case($company->name)) . '1',
            'address1' => '1 Dorcas Hub',
            'city' => 'Ikeja',
            'state' => 'Lagos',
            'country' => 'NG',
            'postcode' => '100212',
            'phonenumber' => !empty($user->phone) ? $user->phone : '08123456789',
            'language' => 'english'
        ];
        $response = $this->sendPost('AddClient', $customer);
        return $response['clientid'] ?? '';
    }
    
    /**
     * Registers a new contact account on the hosting manager.
     *
     * @param DorcasUser $user
     * @param \stdClass  $company
     * @param            $customerId
     *
     * @return string|int
     */
    public function registerContact(DorcasUser $user, \stdClass $company, $customerId)
    {
        $contact = [
            'clientid' => $customerId,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email
        ];
        $response = $this->sendPost('AddContact', $contact);
        return $response['contactid'] ?? '';
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
        $order = [
            'years' => 1,
            'domain' => $domain,
            'testmode' => $config['test_mode'] ?? false,
            'dnsmanagement' => true,
            'emailforwarding' => $config['email_forwarding'] ?? false,
            'idprotection' => $config['id_protection'] ?? true,
            'nameservers' => [],
            'contacts' => [
                'registrant' => [
                    'firstname' => $config['user']->firstname,
                    'lastname' => $config['user']->lastname,
                    'companyname' => $config['company']->name,
                    'email' => $config['user']->email,
                    'address1' => '1 Dorcas Hub',
                    'address2' => '',
                    'city' => 'Magodo',
                    'state' => 'LA',
                    'zipcode' => '100212',
                    'country' => 'NG',
                    'phonenumber' => '+2348185977165',
                ],
                'tech' => array(
                    'firstname' => 'Bolaji',
                    'lastname' => 'Olawoye',
                    'companyname' => 'Dorcas Ltd.',
                    'email' => $config['user']->email,
                    'address1' => '1 Dorcas Hub',
                    'address2' => '',
                    'city' => 'Magodo',
                    'state' => 'LA',
                    'zipcode' => '100212',
                    'country' => 'NG',
                    'phonenumber' => '+2348185977165',
                ),
            ]
            
        ];
        foreach ($config['ns'] as $id => $nameserver) {
            $order['nameservers']['ns' . ($id + 1)] = $nameserver;
        }
        # our order data
        return $this->sendPost('Register', $order);
    }
}