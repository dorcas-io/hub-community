<?php

namespace App\Http\Controllers\Invoicing;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Carbon\Carbon;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class NewOrder extends Controller
{
    /**
     * NewOrder constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'New Invoice';
        $this->data['page']['header'] = ['title' => 'New Invoice'];
        $this->data['breadCrumbs'] = [
            'showHome' => true,
            'crumbs' => [
                ['text' => 'Invoicing', 'href' => '#'],
                ['text' => 'Orders', 'href' => route('apps.invoicing.orders')],
                ['text' => 'Create Order', 'href' => route('apps.invoicing.orders.new'), 'isActive' => true]
            ]
        ];
        $this->data['currentPage'] = 'invoicing';
        $this->data['selectedSubMenu'] = 'invoice';
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
        $this->data['products'] = $this->getProducts($sdk);
        $this->data['customers'] = $this->getCustomers($sdk);
        return view('invoicing.new', $this->data);
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
        $company = $this->getCompany();
        # get the company
        $this->validate($request, [
            'title' => 'required|string|max:80',
            'description' => 'nullable|string',
            'currency' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0',
            'due_at' => 'nullable|date_format:"d F, Y"',
            'reminders_on' => 'nullable',
            'is_quote' => 'nullable',
            'customer' => 'required|string',
            'product_name' => 'required_without:products|string|max:80',
            'product_quantity' => 'required_without:products|numeric|min:1',
            'product_price' => 'required_without:products|numeric|min:0',
            'products' => 'required_without:product_name|array',
            'products.*' => 'string',
            'quantities' => 'required_with:products|array',
            'quantities.*' => 'numeric|min:1',
            'unit_prices' => 'required_with:products|array',
            'unit_prices.*' => 'numeric|min:0',
            'customer_email' => 'required_if:customer,add_new|email',
            'customer_firstname' => 'required_if:customer,add_new|string|max:30',
            'customer_lastname' => 'required_if:customer,add_new|string|max:30',
            'customer_phone' => 'required_if:customer,add_new|string|max:30',
        ]);
        # validate the request
        try {
            $customerId = $request->customer;
            # the default customer ID
            if (strtolower($customerId) === 'add_new') {
                # check the customer entry mode
                $storeService = $sdk->createStoreService();
                # create the store service
                $customer = (clone $storeService)->addBodyParam('firstname', $request->customer_firstname)
                                                ->addBodyParam('lastname', $request->customer_lastname)
                                                ->addBodyParam('email', $request->customer_email)
                                                ->addBodyParam('phone', $request->customer_phone)
                                                ->send('POST', [$company->id, 'customers']);
                # we put step 1 & 2 in one call
                if (!$customer->isSuccessful()) {
                    throw new \RuntimeException('Failed while creating the new customer account...Please try again later.');
                }
                $customerId = $customer->getData()['id'];
                # set the new customer ID
                Cache::forget('crm.customers.'.$company->id);
                # clear the cache
            }
            $query = $sdk->createOrderResource()->addBodyParam('title', $request->title)
                                                ->addBodyParam('description', $request->description ?: '')
                                                ->addBodyParam('currency', $request->currency)
                                                ->addBodyParam('amount', $request->amount)
                                                ->addBodyParam('customers', [$customerId]);
            if ($request->has('due_at')) {
                $date = Carbon::createFromFormat('d F, Y', $request->due_at);
                if (!empty($date)) {
                    $query = $query->addBodyParam('due_date', $date->format('Y-m-d'))
                                    ->addBodyParam('enable_reminder', (int) $request->has('reminders_on'));
                }
            }
            if ($request->has('is_quote')) {
                $query = $query->addBodyParam('is_quote', (int) $request->has('is_quote'));
            }
            if ($request->has('products') && !empty($request->products)) {
                $products = [];
                foreach ($request->products as $index => $productId) {
                    $quantity = $request->quantities[$index] ?? 0;
                    $price = $request->unit_prices[$index] ?? -1;
                    # set the values
                    if ($quantity === 0 || $price === -1) {
                        throw new \UnexpectedValueException(
                            'There is a problem in your form, one of your quantities or prices is invalid.'
                        );
                    }
                    $products[] = ['id' => $productId, 'quantity' => $quantity, 'price' => $price];
                }
                $query = $query->addBodyParam('products', $products);
            } else {
                $product = [
                    'name' => $request->product_name,
                    'quantity' => $request->product_quantity,
                    'price' => $request->product_price
                ];
                $query = $query->addBodyParam('product', $product);
            }
            $query = $query->send('post');
            # send the request
            if (!$query->isSuccessful()) {
                $message = $query->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while creating the order. '.$message);
            }
            $response = (material_ui_html_response(['Successfully created invoice.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
}
