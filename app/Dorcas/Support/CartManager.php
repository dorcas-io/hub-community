<?php

namespace App\Dorcas\Support;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CartManager
{
    /** @var Request */
    private $request;

    /** @var array|mixed  */
    protected $cart = [];

    /**
     * CartManager constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->cart = $request->session()->get('shopping_cart', []);
    }

    /**
     * Adds the content of the cart to the session.
     *
     * @return $this
     */
    public function commit()
    {
        $this->request->session()->put('shopping_cart', $this->cart);
        return $this;
    }

    /**
     * Clears out the items in the cart.
     *
     * @return CartManager
     */
    public function clear()
    {
        $this->cart = [];
        return $this->commit();
    }

    /**
     * Removes a singular item from the cart.
     *
     * @param string $id
     *
     * @return CartManager
     */
    public function remove(string $id)
    {
        unset($this->cart[$id]);
        return $this->commit();
    }

    /**
     * Depending on the value of the quantity, it could either add a new item, or update the quantity.
     *
     * @param string      $id
     * @param string      $name
     * @param float       $unitPrice
     * @param int         $quantity
     * @param string|null $imageUrl
     *
     * @return CartManager
     */
    public function addToCart(string $id, string $name, float $unitPrice, int $quantity = 1, string $imageUrl = null, string $isShipping = 'no')
    {
        if ($quantity <= 0) {
            return $this->remove($id);
        }
        $imageUrl = !empty($imageUrl) ? $imageUrl : cdn('apps/webstore/images/products/1.jpg');
        $this->cart[$id] = [
            'id' => $id,
            'name' => $name,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'photo' => $imageUrl,
            'isShipping' => $isShipping
        ];
        # adds/replaces the entry to/in the array
        return $this->commit();
    }

    /**
     * @param string $id
     * @param int    $quantity
     *
     * @return $this|CartManager
     */
    public function updateQuantity(string $id, int $quantity)
    {
        if (empty($this->cart[$id])) {
            return $this;
        }
        if ($quantity <= 0) {
            return $this->remove($id);
        }
        $this->cart[$id]['quantity'] = $quantity;
        return $this;
    }

    /**
     * @return array
     */
    public function getCart(): array
    {
        $cart = ['items' => array_values($this->cart), 'total' => ['raw' => 0, 'formatted' => 0], 'currency' => 'NGN'];
        foreach ($cart['items'] as $id => $entry) {
            $total = (int) $entry['quantity'] * (float) $entry['unit_price'];
            $cart['items'][$id]['total'] = ['raw' => $total, 'formatted' => number_format($total, 2)];
            $cart['total']['raw'] += $total;
        }
        $cart['total']['formatted'] = number_format($cart['total']['raw']);
        return $cart;
    }
}