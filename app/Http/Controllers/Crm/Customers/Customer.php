<?php

namespace App\Http\Controllers\Crm\Customers;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Exceptions\RecordNotFoundException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Customer extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Customer';
        $this->data['page']['header'] = ['title' => 'Customer'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'CRM Basic', 'href' => route('apps.crm')],
                ['text' => 'Customers', 'href' => route('apps.crm.customers')],
                ['text' => 'Customer Details', 'href' => route('apps.crm.customers'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'crm';
        $this->data['selectedSubMenu'] = 'customers';
    }

    public function index(Request $request, Sdk $sdk, string $id)
    {
        $this->setViewUiResponse($request);
        $response = $sdk->createCustomerResource($id)->send('get');
        if (!$response->isSuccessful()) {
            abort(404, 'Could not find the customer at this URL.');
        }
        $this->data['groups'] = $this->getGroups($sdk);
        $this->data['customer'] = $customer = $response->getData(true);
        $customerContacts = !empty($customer->contacts) ? $customer->contacts['data'] : [];
        $customerContacts = collect($customerContacts)->map(function ($contact) { return $contact['id']; })->all();
        $contactFields = $this->getContactFields($sdk);
        $this->data['availableFields'] = $contactFields->filter(function ($field) use ($customerContacts) {
            return !in_array($field->id, $customerContacts);
        });
        return view('crm.customers.customer', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Request $request, Sdk $sdk, string $id)
    {
        $action = strtolower($request->input('action', 'save_contact_fields'));
        # get the requested action
        $this->validate($request, [
            'fields' => 'nullable|array',
            'fields.*' => 'nullable|string',
            'values' => 'nullable|array',
            'values.*' => 'nullable|string',
            'name' => 'required_if:action,save_deal|string|max:80',
            'value_currency' => 'required_if:action,save_deal|string|size:3',
            'value_amount' => 'required_if:action,save_deal|numeric',
            'note' => 'required_if:action,save_deal|string',
        ]);
        # validate the request
        try {
            switch ($action) {
                case 'save_deal':
                    $dealId = $request->input('deal_id');
                    # check if there's a deal ID in the request
                    $resource = empty($dealId) ? $sdk->createCustomerResource($id) : $sdk->createDealResource($dealId);
                    # create the resource
                    $data = $request->only(['name', 'value_currency', 'value_amount', 'note']);
                    foreach ($data as $key => $value) {
                        $resource->addBodyParam($key, $value);
                    }
                    $response = $resource->send(empty($dealId) ? 'post' : 'put', empty($dealId) ? ['deals'] : []);
                    if (!$response->isSuccessful()) {
                        // do something here
                        throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while saving the deal.');
                    }
                    $response = (material_ui_html_response(['Successfully saved the customer deal.']))->setType(UiResponse::TYPE_SUCCESS);
                    break;
                default:
                    $contacts = [];
                    foreach ($request->fields as $index => $fieldId) {
                        if (empty($request->values[$index])) {
                            continue;
                        }
                        $contacts[] = ['id' => $fieldId, 'value' => $request->values[$index]];
                    }
                    $query = $sdk->createCustomerResource($id);
                    $query = $query->addBodyParam('fields', $contacts)->send('post', ['contacts']);
                    # the query
                    if (!$query->isSuccessful()) {
                        $message = $response->errors[0]['title'] ?? '';
                        throw new \RuntimeException('Failed while saving the contact information. '.$message);
                    }
                    $response = (material_ui_html_response(['Successfully updated contact information.']))->setType(UiResponse::TYPE_SUCCESS);
            }
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
