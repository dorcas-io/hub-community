<?php

namespace App\Http\Controllers\Ajax\Finance;

use App\Events\Finance\TranstrakParse;
use App\Exceptions\RecordNotFoundException;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\RequestOptions;
use Hostville\Dorcas\Sdk;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

class Transtrak extends Controller
{
    const TRANSTRAK_BASE = 'https://transtrak-api.dorcas.ng';
    
    /** @var Client  */
    protected $client;
    
    public function __construct()
    {
        parent::__construct();
        $this->data = [];
        $this->client = new Client([
            'base_uri' => self::TRANSTRAK_BASE,
            RequestOptions::TIMEOUT => 120.0,
            RequestOptions::CONNECT_TIMEOUT => 60.0
        ]);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function fetch(Request $request, Sdk $sdk)
    {
        $payload = $request->only(['username', 'password', 'provider', 'user_id']);
        # get the request payload
        if ($payload['provider'] === 'manual') {
            # drop it
            unset($payload['provider']);
            $payload['imap_host'] = $request->input('imap_host');
            $payload['imap_port'] = $request->input('imap_port');
        }
        if ($request->has('mail_since')) {
            $from = Carbon::createFromFormat('d-M-Y', $request->input('mail_since'));
        }
        $from = empty($from) ? Carbon::now()->startOfMonth() : $from;
        # the default from date
        $payload['sender'] = $request->input('sender_address');
        $payload['subject'] = $request->input('mail_subject');
        $payload['from'] = $from->format('d-m-Y');
        # get the request payload
        event(new TranstrakParse($payload, $sdk->getAuthorizationToken()));
        # trigger the event
        return response()->json(['status' => 'queued']);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function login(Request $request, Sdk $sdk)
    {
        $payload = $request->only(['username', 'password', 'provider']);
        # get the request payload
        if ($payload['provider'] === 'manual') {
            # drop it
            unset($payload['provider']);
            $payload['imap_host'] = $request->input('imap_host');
            $payload['imap_port'] = $request->input('imap_port');
        }
        try {
            $response = $this->client->request('GET', '/test-connection', [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . config('dorcas.transtrak_token')
                ],
                RequestOptions::QUERY => $payload
            ]);
            # send the request
            $json = json_decode((string) $response->getBody(), true);
        } catch (ClientException $e) {
            $json = json_decode((string) $e->getResponse()->getBody(), true);
        } catch (ServerException $e) {
            $json = json_decode((string) $e->getResponse()->getBody(), true);
        }
        if (empty($json['settings']) && !empty($json['reason'])) {
            $message = strtolower($json['reason']);
            if (stripos($message, '[authenticationfailed]') !== false) {
                throw new AuthorizationException($json['reason']);
            } elseif (!empty($payload['provider']) && in_array($payload['provider'], ['gmail', 'yahoo'])) {
                throw new AuthorizationException($json['reason']);
            } else {
                throw new \Exception($json['error']);
            }
        }
        try {
            $configurations = (array) $request->user()->extra_configurations;
            if (empty($configurations['transtrak'])) {
                $configurations['transtrak'] = [];
            }
            $configurations['transtrak']['provider'] = [
                'provider' => $request->input('provider'),
                'bank' => '',
                'account_no' => '',
                'username' => $payload['username'],
                'password' => '',
                'sender_email' => '',
                'sender_subject' => '',
                'show_email_instructions' => false,
                'hide_subject_line' => true,
                'auto_processing' => false,
                'imap_url' => $payload['imap_host'] ?? '',
                'imap_port' => $payload['imap_port'] ?? 993,
            ];
            $query = $sdk->createProfileService()->addBodyParam('extra_configurations', $configurations)->send('PUT');
            # send the request
            if (!$query->isSuccessful()) {
                throw new \RuntimeException('Failed while updating your profile information information. Please try again.');
            }
        } catch (\Exception $e) {
            # catch all errors
        }
        return response()->json($json);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function enableAutoProcessing(Request $request, Sdk $sdk)
    {
        $configurations = (array) $request->user()->extra_configurations;
        $account = $request->input('account');
        # get the account to configure auto on
        if (empty($auto)) {
            $account = 'all';
        }
        if (empty($configurations['transtrak']) || empty($configurations['transtrak']['default_config'])) {
            throw new \RuntimeException('You need to enable Transtrak before this can be done.');
        }
        if ($account === 'all') {
            foreach ($configurations['transtrak'] as $key => $configuration) {
                if ($key === 'default_config') {
                    continue;
                }
                $configurations['transtrak'][$key]['transtrak_auto_enabled'] = true;
            }
        } else {
            if (empty($configurations['transtrak'][$account])) {
                throw new RecordNotFoundException('Could not find an account configured for ' . $account);
            }
            $configurations['transtrak'][$account]['transtrak_auto_enabled'] = true;
        }
        $query = $sdk->createProfileService()->addBodyParam('extra_configurations', $configurations)->send('PUT');
        # send the request
        if (!$query->isSuccessful()) {
            throw new \RuntimeException('Failed while updating your profile information information. Please try again.');
        }
        return response()->json($query->getData());
    }
}
