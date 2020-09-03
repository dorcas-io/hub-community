<?php

namespace App\Http\Controllers\Ajax\Billing;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class Billing extends Controller
{
    /**
     * Billing constructor.
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
                                                                ->send('post', ['extend-plan']);
        if (!$billsQuery->isSuccessful()) {
            $message = $billsQuery->getErrors()[0]['title'] ?? 'Failed while trying to save the billing record.';
            if ($transaction['is_successful'] === 1) {
                $message .= 'Kindly report the issue to customer support, your transaction reference is: '.$transaction['reference'];
            }
            throw new \RuntimeException($message);
        }
        # next up - we need to update the company information
        
        return response()->json($billsQuery->getData());
    }
}
