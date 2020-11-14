<?php

namespace App\Http\Controllers\Settings;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class Business extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Business Profile';
        $this->data['page']['header'] = ['title' => 'Business Profile'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Settings', 'href' => route('settings')],
                ['text' => 'Business Profile', 'href' => route('settings.business'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'settings';
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['company'] = $company = $request->user()->company(true, true);
        # get the company information
        $location = ['address1' => '', 'address2' => '', 'state' => ['data' => ['id' => '']]];
        # the location information
        $locations = $this->getLocations($sdk);
        $location = !empty($locations) ? $locations->first() : $location;
        $this->data['states'] = Controller::getDorcasStates($sdk);
        # get the states
        $this->data['location'] = $location;
        return view('settings.business', $this->data);
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
            'name' => 'required_if:action,update_business|string|max:100',
            'registration' => 'nullable|string|max:30',
            'phone' => 'required_if:action,update_business|string|max:30',
            'email' => 'required_if:action,update_business|email|max:80',
            'website' => 'nullable|url|max:80',
            'address1' => 'required_if:action,update_location|string|max:100',
            'address2' => 'nullable|string|max:100',
            'city' => 'required_if:action,update_location|string|max:100',
            'state' => 'required_if:action,update_location|string|max:50',
        ]);
        # validate the request
        try {
            $company = $request->user()->company(true, true);
            # get the company information
            if ($request->action === 'update_business') {
                # update the business information
                $query = $sdk->createCompanyService()
                                ->addBodyParam('name', $request->name, true)
                                ->addBodyParam('registration', $request->input('registration', ''))
                                ->addBodyParam('phone', $request->input('phone', ''))
                                ->addBodyParam('email', $request->input('email', ''))
                                ->addBodyParam('website', $request->input('website', ''))
                                ->send('PUT');
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while updating your business information. Please try again.');
                }
                $message = ['Successfully updated business information for '.$request->name];
            } else {
                # update address information

                $locations = $this->getLocations($sdk);
                $location = !empty($locations) ? $locations->first() : null;
                $query = $sdk->createLocationResource();
                # get the query
                $query = $query->addBodyParam('address1', $request->address1)
                                ->addBodyParam('address2', $request->address2)
                                ->addBodyParam('city', $request->city)
                                ->addBodyParam('state', $request->state);
                # add the payload
                if (!empty($location)) {
                    $response = $query->send('PUT', [$location->id]);
                } else {
                    $response = $query->send('POST');
                }
                if (!$response->isSuccessful()) {
                    throw new \RuntimeException('Sorry but we encountered issues while updating your address information.');
                }
                Cache::forget('business.locations.'.$company->id);
                # forget the cache data
                $message = ['Successfully updated your company address information.'];
            }
            $response = (material_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
