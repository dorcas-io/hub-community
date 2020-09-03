<?php

namespace App\Http\Controllers;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Carbon\Carbon;
use GuzzleHttp\Exception\ServerException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;

class Subscription extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Subscription';
        $this->data['page']['header'] = ['title' => 'Subscription'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Subscription', 'href' => route('subscription'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'subscription';
    }

    public function index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $plans = config('dorcas.plans');
        # get the plans configuration
        $dorcasPlans = $this->getPricingPlans($sdk);
        # get the plans from Dorcas
        $pricingPlans = [];
        # the pricing plans
        foreach ($plans as $name => $plan) {
            $live = $dorcasPlans->where('name', $name)->first();
            # get the plan
            if (empty($live)) {
                continue;
            }
            $temp = array_merge($plan, ['name' => $name]);
            $temp['profile'] = $live;
            $pricingPlans[] = $temp;
        }
        $this->data['plans'] = collect($pricingPlans)->map(function ($plan) {
            return (object) $plan;
        });
        return view('subscription', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'coupon' => 'required_with:redeem_coupon|string|max:30'
        ]);
        # validate the request
        $response = null;
        try {
            
            if ($request->has('redeem_coupon')) {
                # to reserve a subdomain
                $response = $sdk->createCouponResource($request->input('coupon'))
                                ->addBodyParam('select_using', 'code')
                                ->send('post', ['redeem']);
                # send the request
                if (!$response->isSuccessful()) {
                    # it failed
                    $message = $response->errors[0]['title'] ?? '';
                    throw new \RuntimeException('Failed while redeeming the coupon. ' . $message);
                }
                $response = (material_ui_html_response(['Successfully performed upgrade/extension on plan.']))->setType(UiResponse::TYPE_SUCCESS);
            
            }
        } catch (ServerException $e) {
            $message = json_decode((string) $e->getResponse()->getBody(), true);
            $response = (material_ui_html_response([$message['message']]))->setType(UiResponse::TYPE_ERROR);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
