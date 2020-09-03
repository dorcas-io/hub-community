<?php

namespace App\Http\Controllers\Invoicing;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Orders extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Orders';
        $this->data['page']['header'] = ['title' => 'Orders'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Invoicing', 'href' => route('apps.inventory')],
                ['text' => 'Orders', 'href' => route('apps.invoicing.orders'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'invoicing';
        $this->data['selectedSubMenu'] = 'orders';
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
        $ordersCount = 0;
        $query = $sdk->createOrderResource()->addQueryArgument('limit', 1)->send('get');
        if ($query->isSuccessful()) {
            $ordersCount = $query->meta['pagination']['total'] ?? 0;
        }
        $this->data['ordersCount'] = $ordersCount;
        return view('invoicing.orders', $this->data);
    }
}
