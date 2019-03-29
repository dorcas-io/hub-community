<?php

namespace App\Http\Controllers\WebStore;

use Carbon\Carbon;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use App\Dorcas\Support\CartManager;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ECommerce\OnlineStore;

class Cart extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['page']['title'] = 'Cart';
        $this->data['page']['header'] = ['title' => 'Shopping Cart'];
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $storeOwner = $this->getCompanyViaDomain();
        # get the store owner
        $this->data['storeSettings'] = OnlineStore::getStoreSettings((array) $storeOwner->extra_data);
        # our store settings container
        if (empty($storeOwner)) {
            abort(404, 'Could not find a store at this URL.');
        }
        $this->data['storeOwner'] = $storeOwner;
        $this->data['page']['title'] = $storeOwner->name . ' ' . $this->data['page']['title'];
        $this->data['cart'] = Home::getCartContent($request);
        return view('webstore.cart', $this->data);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromCartXhr(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|string'
        ]);
        # validate the request
        $cart = new CartManager($request);
        # create the cart manager
        $cart->remove($request->id);
        # remove the product from the cart
        return response()->json($cart->getCart());
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkoutXhr(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'firstname' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'email' => 'required|email|max:80',
            'phone' => 'required'
        ]);
        # validate the request
        $storeOwner = $this->getCompanyViaDomain();
        # get the store owner
        $cartManager = new CartManager($request);
        $cart = (object) $cartManager->getCart();
        # get the cart
        $storeService = $sdk->createStoreService();
        # create the store service
        /**
         * Step 1: check for customer with this email
         * Step 2: create the customer
         */
        $customer = (clone $storeService)->addBodyParam('firstname', $request->firstname)
                                        ->addBodyParam('lastname', $request->lastname)
                                        ->addBodyParam('email', $request->email)
                                        ->addBodyParam('phone', $request->phone)
                                        ->send('POST', [$storeOwner->id, 'customers']);
        # we put step 1 & 2 in one call
        if (!$customer->isSuccessful()) {
            throw new \RuntimeException('Failed while checking your customer account... Please try again later.');
        }
        $customer = $customer->getData(true);
        $orderData = [
            'title' => 'Order #'.($customer->orders_count + 1).' for '.$customer->firstname.' '.$customer->lastname,
            'description' => 'Order placed on web store at '.Carbon::now()->format('D jS M, Y h:i a'),
            'currency' => $cart->currency,
            'amount' => $cart->total['raw'],
            'products' => [],
            'customers' => [$customer->id],
            'enable_reminder' => 0
        ];
        foreach ($cart->items as $cartItem) {
            $orderData['products'][] = ['id' => $cartItem['id'], 'quantity' => $cartItem['quantity'], 'price' => $cartItem['unit_price']];
        }
        $checkoutQuery = (clone $storeService);
        foreach ($orderData as $key => $value) {
            $checkoutQuery = $checkoutQuery->addBodyParam($key, $value);
        }
        # Step 3: create order
        $checkout = $checkoutQuery->send('POST', [$storeOwner->id, 'checkout']);
        # send the checkout query
        if (!$checkout->isSuccessful()) {
            throw new \RuntimeException('Could not add your order to the record. Please try again later.');
        }
        Cache::forget('crm.customers.'.$storeOwner->id);
        # clear the cache
        $cartManager->clear();
        # clear the cart
        $data = $checkout->getData();
        if (!empty($checkout->meta) && !empty($checkout->meta['payment_url'])) {
            $data['payment_url'] = $checkout->meta['payment_url'];
        }
        return response()->json($data, 202);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCartXhr(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|string',
            'name' => 'required|string',
            'quantity' => 'nullable|numeric|min:1',
            'photo' => 'nullable|string|url',
            'unit_price' => 'required|numeric'
        ]);
        # validate the request
        $cart = new CartManager($request);
        # create the cart manager
        $cart->addToCart($request->id, $request->name, $request->unit_price, $request->input('quantity', 1), $request->photo);
        # adds the product to the cart
        return response()->json($cart->getCart());
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCartQuantitiesXhr(Request $request)
    {
        $this->validate($request, [
            'quantities' => 'required|array',
            'quantities.*.id' => 'required|string',
            'quantities.*.quantity' => 'required|numeric',
        ]);
        # validate the request
        $cart = new CartManager($request);
        # create the cart manager
        foreach ($request->quantities as $quantity) {
            $cart = $cart->updateQuantity($quantity['id'], $quantity['quantity']);
        }
        return response()->json($cart->commit()->getCart());
    }
}
