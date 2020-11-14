<?php

namespace App\Http\Controllers\Directory;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayVendor extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Pay Vendor';
        $this->data['page']['header'] = ['title' => 'Pay Vendor'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Directory', 'href' => route('directory.vendors')],
                ['text' => 'Pay Vendor', 'href' => route('directory.vendors'), 'isActive' => true],
            ]
        ];
        $this->data['currentPage'] = 'professional_directory';
    }
    
    
    public function index(Request $request, Sdk $sdk, string $id)
    {
        $this->setViewUiResponse($request);
        $contactQuery = $sdk->createCompanyService()->addQueryArgument('include', 'bank_accounts')
                                                    ->send('GET', ['contacts', $id]);
        if (!$contactQuery->isSuccessful()) {
            abort(404, 'Could not find the contact record.');
        }
        $this->data['contact'] = $contact = $contactQuery->getData(true);
        return view('directory.pay-vendor', $this->data);
    }
}
