<?php

namespace App\Dorcas\Hub\Utilities\DomainManager;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class WhmApiClient
{
    /** @var Client  */
    private $apiClient;
    
    /** @var string  */
    private $endpoint;
    
    /** @var string  */
    private $username;
    
    /** @var string  */
    private $token;
    
    /** @var array  */
    protected $payload = ['api.version' => 1];
    
    /** @var string|null */
    private $sessionId = null;
    
    const CPANEL_UAPI_VERSION = 3;
    
    const PKG_TYPE_ALL = 'all';
    const PKG_TYPE_CREATABLE = 'creatable';
    const PKG_TYPE_EDITABLE = 'editable';
    const PKG_TYPE_VIEWABLE = 'viewable';
    
    const CPANEL_THEME_PAPER_LANTERN = 'paper_lantern';
    
    /**
     * WhmApiClient constructor.
     *
     * @param string $endpoint
     * @param string $username
     * @param string $token
     */
    public function __construct(string $endpoint, string $username, string $token)
    {
        $this->endpoint = $endpoint;
        $this->username = $username;
        $this->token = $token;
        $this->apiClient = new Client([
            'base_uri' => $this->endpoint,
            RequestOptions::CONNECT_TIMEOUT => 30.0,
            RequestOptions::VERIFY => false,
            RequestOptions::HEADERS => [
                'User-Agent' => 'Dorcas WHM/UAPI Client/1.0',
                'Authorization' => 'whm ' . $this->username . ':' . $this->token
            ]
        ]);
    }
    
    /**
     * A static interface to create a new instance.
     *
     * @param string $endpoint
     * @param string $username
     * @param string $token
     *
     * @return WhmApiClient
     */
    public static function newInstance(string $endpoint, string $username, string $token): WhmApiClient
    {
        return new static($endpoint, $username, $token);
    }
    
    /**
     * Sets the session id on the client.
     *
     * @param string $sessionId
     *
     * @return WhmApiClient
     */
    public function setSessionId(string $sessionId): WhmApiClient
    {
        $this->sessionId = $sessionId;
        return $this;
    }
    
    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->apiClient;
    }
    
    /**
     * Sends a WHM API version 1 request.
     *
     * @param string $function
     * @param array  $payload
     * @param bool   $processResponse
     * @param bool   $throwExceptionOnFail
     *
     * @return array|string
     * @see https://documentation.cpanel.net/display/DD/WHM+API+1+-+Return+Data
     */
    protected function sendJsonApiPost(string $function, array $payload, bool $processResponse = true, bool $throwExceptionOnFail = true)
    {
        $payload = array_merge($this->payload, $payload);
        # we merge both data sources
        if (!array_key_exists('api.version', $payload)) {
            $payload['api.version'] = 1;
        }
        $path = !empty($this->sessionId) ? '/' . $this->sessionId : '';
        $path .= '/json-api/' . $function;
        # set up the request path
        try {
            $response = $this->apiClient->post($path, [
                RequestOptions::FORM_PARAMS => $payload
            ]);
            $string = (string) $response->getBody();
            $json = json_decode($string, true);
            # parse the response
        } catch (ClientException $e) {
            $json = json_decode((string) $e->getResponse()->getBody(), true);
            Log::debug('cpanel(' . $function . '): ', $json);
            $json = $json['cpanelresult'] ?? $json;
        } catch (ConnectException $e) {
            Log::debug('cpanel(' . $function . '): ', ['error' => $e->getMessage()]);
            $json = ['errors' => ['Service unreachable']];
        }
        if (!$processResponse) {
            # we do not need to process the response
            return $json;
        }
        $metadata = $json['metadata'] ?? [];
        if ($throwExceptionOnFail && (empty($metadata['result']) || (int) $metadata['result'] === 0 || !empty($json['error']))) {
            if (!empty($json['error'])) {
                $message = $json['error'];
            } elseif (!empty($metadata['reason'])) {
                $message = $metadata['reason'];
            }
            throw new \RuntimeException(!empty($message) ? $message : 'The request could not be completed!');
        }
        return $json['data'] ?? $metadata;
    }
    
    /**
     * Sends the request to the API using the UAPI methods.
     *
     * @param string      $module
     * @param string      $function
     * @param string|null $user
     * @param string|null $sessionId
     * @param array       $payload
     * @param bool        $processResponse
     *
     * @return array|string
     * @see https://documentation.cpanel.net/display/DD/Use+WHM+API+to+Call+cPanel+API+and+UAPI
     */
    protected function sendUApiPost(
        string $module,
        string $function,
        string $user = null,
        string $sessionId = null,
        array $payload = [],
        bool $processResponse = true
    ) {
        if (empty($sessionId) && empty($this->sessionId)) {
            # we need a session id
            $sessionId = $this->startSession($user);
            if (empty($sessionId)) {
                throw new \RuntimeException('Could not start a cPanel session for the account.');
            }
            $this->setSessionId($sessionId);
        }
        $uApiParams = [
            'cpanel_jsonapi_user' => !empty($user) ? $user : $this->username,
            'cpanel_jsonapi_module' => $module,
            'cpanel_jsonapi_func' => $function,
            'cpanel_jsonapi_apiversion' => self::CPANEL_UAPI_VERSION,
        ];
        $payload = array_merge($payload, $uApiParams);
        # we merge both data sources
        $json = $this->sendJsonApiPost('cpanel', $payload, false);
        # we get the unprocessed JSON
        if (!$processResponse) {
            # we do not need to process the response
            return $json;
        }
        $result = $json['result'] ?? [];
        if (!empty($result['errors'])) {
            throw new \RuntimeException($result['errors'][0] ?? 'The request could not be completed!');
        }
        return $result['data'] ?? [];
    }
    
    /**
     * Starts a new session for the specified username.
     *
     * @param string|null $username
     *
     * @return string
     */
    public function startSession(string $username = null): string
    {
        $payload = ['service' => 'cpaneld', 'user' => !empty($username) ? $username : $this->username];
        $json = $this->sendJsonApiPost('create_user_session', $payload);
        $id = $json['cp_security_token'] ?? '';
        return strpos($id, '/') === 0 ? substr($id, 1) : $id;
    }
    
    /**
     * Generates a password of the set length.
     *
     * @param int $length
     *
     * @return string
     * @throws \Exception
     */
    public static function generatePassword(int $length = 10): string
    {
        $byteLength = (int) ceil($length / 2);
        $bytes = random_bytes($byteLength);
        return bin2hex($bytes);
    }
    
    /**
     * Suggests a hosting account username based on the domain.
     * It keeps the username limited to 8 characters.
     *
     * @param string $domain
     *
     * @return string
     */
    public function suggestHostingAccountUsername(string $domain): string
    {
        $processedDomain = preg_replace(['/[0-9]+/', '/\-/'], '', $domain);
        $prefix = substr($processedDomain, 0, 3);
        $remainder = str_split(substr(str_replace(['.', '-'], '', $processedDomain), 3));
        shuffle($remainder);
        return $prefix . substr(implode('', $remainder), 0, 5);
    }
    
    /**
     * Checks if the account username is available.
     *
     * @param string $username
     *
     * @return bool
     * @see https://documentation.cpanel.net/display/DD/WHM+API+1+Functions+-+verify_new_username
     */
    public function verifyAccountUsername(string $username): bool
    {
        $payload = ['user' => $username];
        $response = $this->sendJsonApiPost('verify_new_username', $payload, true, false);
        return (int) $response['result'] === 1;
    }
    
    /**
     * Creates a new hosting account on the server.
     *
     * @param string $username
     * @param string $domain
     * @param string $plan
     * @param string $password
     * @param string $contactEmail
     *
     * @return array|string
     * #see https://documentation.cpanel.net/display/DD/WHM+API+1+Functions+-+createacct
     */
    public function createAccount(string $username, string $domain, string $plan, string $password, string $contactEmail)
    {
        $payload = ['username' => $username, 'domain' => $domain, 'plan' => $plan, 'password' => $password, 'contactemail' => $contactEmail];
        return $this->sendJsonApiPost('createacct', $payload);
    }
    
    /**
     * Removes a cPanel account.
     *
     * @param string $username
     * @param bool   $keepDns
     *
     * @return bool
     * @see https://documentation.cpanel.net/display/DD/WHM+API+1+Functions+-+removeacct
     */
    public function removeAccount(string $username, bool $keepDns = false): bool
    {
        $payload = ['username' => $username, 'keepdns' => $keepDns ? 1 : 0];
        $json = $this->sendJsonApiPost('removeacct', $payload, false);
        $metadata = $json['metadata'] ?? [];
        if (!empty($metadata['result'])) {
            return $metadata['result'] === 1;
        }
        return false;
    }
    
    /**
     * List the available hosting packages on the system.
     *
     * @param string $type  One of the WhmApiClient::PKG_TYPE_* constants
     *
     * @return array|null
     * @see https://documentation.cpanel.net/display/DD/WHM+API+1+Functions+-+listpkgs
     */
    public function listHostingPackages(string $type = self::PKG_TYPE_CREATABLE): ?array
    {
        $payload = ['want' => $type];
        return $this->sendJsonApiPost('listpkgs', $payload);
    }
    
    /**
     * Adds a new hosting package on WHM.
     *
     * @param string $name
     * @param int    $diskQuotaMb
     * @param int    $bandwidthQuota
     * @param int    $maxEmailAccountQuota
     * @param string $cpanelTheme
     * @param string $language
     * @param int    $maxFtpAccounts
     * @param int    $maxEmails
     * @param int    $maxSubdomains
     * @param int    $maxParkedDomains
     *
     * @return array|string
     * @see https://documentation.cpanel.net/display/DD/WHM+API+1+Functions+-+addpkg
     */
    public function addHostingPackage(
        string $name,
        int $diskQuotaMb = 5120,
        int $bandwidthQuota = 5120,
        int $maxEmailAccountQuota = 1024,
        string $cpanelTheme = self::CPANEL_THEME_PAPER_LANTERN,
        string $language = 'en',
        int $maxFtpAccounts = 0,
        int $maxEmails = 0,
        int $maxSubdomains = 0,
        int $maxParkedDomains = 0
    ) {
        $payload = [
            'name' => $name,
            'quota' => $diskQuotaMb,
            'cpmod' => $cpanelTheme,
            'language' => $language,
            'maxftp' => $maxFtpAccounts <= 0 ? 'unlimited' : $maxFtpAccounts,
            'maxpop' => $maxEmails <= 0 ? 'unlimited' : $maxEmails,
            'maxsub' => $maxSubdomains <= 0 ? 'unlimited' : $maxSubdomains,
            'maxpark' => $maxParkedDomains <= 0 ? 'unlimited' : $maxParkedDomains,
            'bwlimit' => $bandwidthQuota,
            'max_emailacct_quota' => $maxEmailAccountQuota
        ];
        return $this->sendJsonApiPost('addpkg', $payload);
    }
    
    /**
     * Creates a new email account.
     *
     * @param string      $email
     * @param string      $password
     * @param string      $domain
     * @param int         $diskSpaceMb
     * @param int         $skipUpdateDb
     * @param int         $sendWelcome
     * @param string|null $user
     *
     * @return array
     * @see https://documentation.cpanel.net/display/DD/UAPI+Functions+-+Email%3A%3Aadd_pop
     */
    public function createEmail(
        string $email,
        string $password,
        string $domain,
        int $diskSpaceMb = 25,
        int $skipUpdateDb = 0,
        int $sendWelcome = 0,
        string $user = null)
    {
        $payload = [
            'email' => $email,
            'password' => $password,
            'domain' => $domain,
            'quota' => $diskSpaceMb,
            'skip_update_db' => $skipUpdateDb,
            'send_welcome_email' => $sendWelcome
        ];
        return $this->sendUApiPost('Email', 'add_pop', $user, null, $payload);
    }
    
    /**
     * Removes an email account.
     *
     * @param string      $email
     * @param string      $domain
     * @param string      $flag
     * @param string|null $user
     *
     * @return array|string
     * @see https://documentation.cpanel.net/display/DD/UAPI+Functions+-+Email%3A%3Adelete_pop
     */
    public function deleteEmail(string $email, string $domain, string $flag = 'remove', string $user = null)
    {
        $payload = ['email' => $email, 'domain' => $domain, 'flags' => $flag];
        $json = $this->sendUApiPost('Email', 'delete_pop', $user, null, $payload, false);
        return intval($json['result']) === 1;
    }
    
    /**
     * Lists the email addresses.
     *
     * @param string      $domain
     * @param string|null $user
     * @param int         $maxAccounts
     *
     * @return array|null
     * @see https://documentation.cpanel.net/display/DD/UAPI+Functions+-+Email%3A%3Alist_pops_with_disk
     */
    public function listEmails(string $domain, string $user = null, int $maxAccounts = 200): ?array
    {
        $payload = ['domain' => $domain, 'maxaccounts' => $maxAccounts];
        return $this->sendUApiPost('Email', 'list_pops_with_disk', $user, null, $payload);
    }
}