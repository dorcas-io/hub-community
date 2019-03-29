<?php

namespace App\Http\Controllers\Crm\Customers;

use App\Http\Controllers\Controller;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;

class Customers extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Customers';
        $this->data['page']['header'] = ['title' => 'Customers'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'CRM Basic', 'href' => route('apps.crm')],
                ['text' => 'Customers', 'href' => route('apps.crm.customers'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'crm';
        $this->data['selectedSubMenu'] = 'customers';
    }


    public function index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $customerCount = 0;
        if ($request->has('groups')) {
            $this->data['groupFilters'] = $request->input('groups');
        }
        $response = $sdk->createCustomerResource()->addQueryArgument('limit', 1)->send('get');
        if ($response->isSuccessful()) {
            $customerCount = $response->meta['pagination']['total'] ?? 0;
        }
        $contactFields = $this->getContactFields($sdk);
        $this->data['customFields'] = [];
        if (!empty($contactFields)) {
            foreach ($contactFields as $contactField) {
                $this->data['customFields'][] = [
                    'label' => str_replace(' ', '_', strtolower($contactField->name)),
                    'title' => $contactField->name
                ];
            }
        }
        $this->data['customersCount'] = $customerCount;
        return view('crm.customers.customers', $this->data);
    }
}