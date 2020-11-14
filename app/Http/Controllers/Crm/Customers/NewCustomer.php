<?php

namespace App\Http\Controllers\Crm\Customers;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewCustomer extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Add Customer';
        $this->data['page']['header'] = ['title' => 'Add a Customers'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'CRM Basic', 'href' => route('apps.crm')],
                ['text' => 'Customers', 'href' => route('apps.crm.customers')],
                ['text' => 'Add Customer', 'href' => route('apps.crm.customers.new'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'crm';
        $this->data['selectedSubMenu'] = 'customers';
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setViewUiResponse($request);
        return view('crm.customers.new', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'firstname' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'phone' => 'required_without:email|numeric',
            'email' => 'required_without:phone|email|max:80',
            'contact_ids' => 'nullable|array',
            'contacts' => 'required_with:contact_ids|array'
        ]);
        # validate the request
        try {
            $contacts = [];
            if ($request->has('contact_ids')) {
                foreach ($request->contact_ids as $index => $fieldId) {
                    if (empty($request->contacts[$index])) {
                        continue;
                    }
                    $contacts[] = ['id' => $fieldId, 'value' => $request->contacts[$index]];
                }
            }
            $resource = $sdk->createCustomerResource();
            $resource = $resource->addBodyParam('firstname', $request->firstname)
                                    ->addBodyParam('lastname', $request->lastname)
                                    ->addBodyParam('phone', $request->input('phone', ''))
                                    ->addBodyParam('email', $request->input('email', ''));
            if (!empty($contacts)) {
                $resource = $resource->addBodyParam('fields', $contacts);
            }
            # the resource
            $response = $resource->send('post');
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while saving the customer information. '.$message);
            }
            $response = (material_ui_html_response(['Successfully added customer.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
