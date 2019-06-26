<?php

namespace App\Http\Controllers;


use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use Dorcas\ModulesLibrary\Models\ModulesLibraryResources;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\RecordNotFoundException;

class HubController extends Controller
{
    

    /**
     * Create a new Dorcas Hub controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @param Sdk|null $sdk
     *
     * @return Collection|null
     */
    public function getLibraryResources($resource_type): ?Collection
    {
        $partner_id = !empty($partner->name) ? $partner->id : 0;
        //$company = $this->getCompany();
        //$company->id = 999999999;
        $company_id = 100006;
        # get the user
        $resources = Cache::remember('mli_resources.'.$company_id, 30, function () use ($resource_type) {
            $response = ModulesLibraryResources::where([
            	['partner_id', '=', '0'],
            	['resource_type', '=', $resource_type]
            ])->get()->toArray();
            //if (!$response->isSuccessful()) {
            //    return null;
            //}
            return collect($response)->map(function ($resource) {
                return (object) $resource;
            });
        });
        return $resources;
    }


    public function getWallet()
    {
        $wallet = [];
        /*$wallet = [
            'balance' => 250
        ];*/

        $config = $this->getCompany()->extra_data;

        if (empty($config['wallet'])) {
            $config['wallet']['NGN'] = [
                'balance' => 0
            ];
        }
        $sdk = app(Sdk::class);
        $query = $sdk->createCompanyService()->addBodyParam('extra_data', $config)->send('PUT');
        if (!$query->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($query->errors[0]['title'] ?? 'Something went wrong while setting up your wallet.');
        }

        # get the company configuration data
        if (!empty($config) && !empty($config['wallet'])) {
            # we actually have some wallet data
            $wallet = $config['wallet']['NGN'];
        }
        return response()->json($wallet);

    }

    public function incrementWallet(Request $request, Sdk $sdk, $amount)
    {
        try {
            $extraData = (array) $this->getCompany()->extra_data;
            $old_wallet_balance = $extraData['wallet']['NGN']["balance"];
            $extraData['wallet']['NGN'] = [
                'balance' => $request->balance + $amount
            ];
            # add a wallet entry
            $query = $sdk->createCompanyService()->addBodyParam('extra_data', $extraData)->send('PUT');
            # set updates to the account
            if (!$query->isSuccessful()) {
                // do something here
                throw new RecordNotFoundException($query->errors[0]['title'] ?? 'Something went wrong while adding to your wallet.');
            }
        } catch (\Exception $e) {
            throw new \Exception("Error Incrementing Wallet:  ". $e->getMessage());
        }
        return response()->json($query->data);

    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verifyTransaction(Request $request, Sdk $sdk)
    {
        $company = $request->user()->company(true, true);
        # get the company
        $channel = $request->input('channel', 'paystack');
        # get the provider channel
        $transaction = null;
        # the transaction resource
        try {
            switch($channel) {
                case 'paystack':
                    $paystack = new \Yabacon\Paystack(config('services.paystack.secret_key'));
                    $txn = $paystack->transaction->verify([
                        'reference'=> $request->reference
                    ]);
                    $transaction = [
                        'currency' => $txn->data->currency,
                        'amount' => $txn->data->amount / 100,
                        'extra_data' => [
                            'gateway_response' => $txn->data->gateway_response,
                            'channel' => $txn->data->channel,
                            'source_ip' => $txn->data->ip_address,
                            'custom_data' => $txn->data->metadata->custom_fields,
                            'card' => [
                                'auth_code' => $txn->data->authorization->authorization_code,
                                'last4' => $txn->data->authorization->last4,
                                'exp_month' => $txn->data->authorization->exp_month,
                                'exp_year' => $txn->data->authorization->exp_year,
                                'card_type' => $txn->data->authorization->card_type,
                            ]
                        ],
                        'is_successful' => $txn->data->status === 'success' ? 1 : 0
                    ];
                    break;
            }
        } catch (\Yabacon\Paystack\Exception\ApiException $e) {
            Log::error($e->getMessage());
            throw new \RuntimeException('Paystack Error: '.$e->getMessage());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
        $transaction['reference'] = $request->reference;
        $transaction['processor'] = $channel;
        $transaction['plan_id'] = $company->plan['data']['id'] ?? '';
        # set the transaction data
        $billsQuery = $sdk->createCompanyResource($company->id)->addBodyParam('transaction', $transaction)
                                                                ->send('post');

        //set some additional data
        //if ()
        //Cache::put('key', 'value');


        if (!$billsQuery->isSuccessful()) {
            $message = $billsQuery->getErrors()[0]['title'] ?? 'Failed while trying to save the billing record.';
            if ($transaction['is_successful'] === 1) {
                $message .= 'Kindly report the issue to customer support, your transaction reference is: '.$transaction['reference'];
            }
            throw new \RuntimeException($message);
        }
        # next up - we need to update the company information

        //send email to payment admin
        
        return response()->json($billsQuery->getData());
    }

}
