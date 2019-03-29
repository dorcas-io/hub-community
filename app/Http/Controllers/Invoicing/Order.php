<?php

namespace App\Http\Controllers\Invoicing;

use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Order extends Controller
{
    /**
     * Order constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Order';
        $this->data['page']['header'] = ['title' => 'Order'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Invoicing', 'href' => route('apps.inventory')],
                ['text' => 'Orders', 'href' => route('apps.invoicing.orders')],
                ['text' => 'Order', 'href' => route('apps.invoicing.orders'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'invoicing';
        $this->data['selectedSubMenu'] = 'orders';
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Sdk $sdk, string $id)
    {
        $this->setViewUiResponse($request);
        $response = $sdk->createOrderResource($id)->addQueryArgument('include', 'customers:limit(10000|0)')
                                                    ->send('get');
        if (!$response->isSuccessful()) {
            abort(404, 'Could not find the order at this URL.');
        }
        $this->data['dorcasUrlGenerator'] = $sdk->getUrlRegistry();
        $this->data['order'] = $order = $response->getData(true);
        $this->data['page']['title'] .= ' - Invoice #' . $order->invoice_number;
        return view('invoicing.order', $this->data);
    }
}
