@extends('webstore.layouts.shop')
@section('body_main_content_container_body')
    <div class="table-responsive bottommargin" id="cart-container">
        <table class="table cart">
            <thead>
            <tr>
                <th class="cart-product-remove">&nbsp;</th>
                <th class="cart-product-thumbnail">&nbsp;</th>
                <th class="cart-product-name">Product</th>
                <th class="cart-product-price">Unit Price</th>
                <th class="cart-product-quantity">Quantity</th>
                <th class="cart-product-subtotal">Total</th>
            </tr>
            </thead>
            <tbody>
                <tr class="cart_item" v-for="(cartItem, index) in cart.items" :key="cartItem.id">
                    <td class="cart-product-remove">
                        <a href="#" class="remove" title="Remove this item" v-on:click.prevent="removeItem(index)">
                            <i class="icon-trash2"></i>
                        </a>
                    </td>
                    <td class="cart-product-thumbnail">
                        <a href="#">
                            <img width="64" height="64" v-bind:src="cartItem.photo" v-bind:alt="cartItem.name">
                        </a>
                    </td>
                    <td class="cart-product-name">
                        <a href="#">@{{ cartItem.name }}</a>
                    </td>
                    <td class="cart-product-price">
                        <span class="amount">@{{ cart.currency + '' + cartItem.unit_price }}</span>
                    </td>
                    <td class="cart-product-quantity">
                        <div class="quantity clearfix">
                            <input type="button" value="-" class="minus" v-on:click.prevent="decrementQuantity(index)">
                            <input type="text" name="quantity" value="" v-model="cartItem.quantity" class="qty">
                            <input type="button" value="+" class="plus" v-on:click.prevent="incrementQuantity(index)">
                        </div>
                    </td>
                    <td class="cart-product-subtotal">
                        <span class="amount">@{{ cart.currency }} @{{ cartItem.total.formatted }}</span>
                    </td>
                </tr>
                <tr v-if="(typeof cart.items === 'undefined' || cart.items.length === 0) && payment_url.length === 0">
                    <td colspan="6">There are no product in your cart <a href="{{ route('webstore') }}">Continue Shopping</a> </td>
                </tr>
                <tr v-if="payment_url.length > 0">
                    <td colspan="6">Your order has been placed, you can also <a v-bind:href="payment_url" class="button button-3d nomargin button-black">Pay Now</a> to complete your order.</td>
                </tr>
                <tr class="cart_item">
                    <td colspan="6">
                        <div class="row clearfix">
                            <div class="col-md-4 col-xs-4 nopadding">
                                <!--<div class="col-md-8 col-xs-7 nopadding">
                                    <input type="text" value="" class="sm-form-control" placeholder="Enter Coupon Code..">
                                </div>
                                <div class="col-md-4 col-xs-5">
                                    <a href="#" class="button button-3d button-black nomargin">Apply Coupon</a>
                                </div>-->
                                &nbsp;
                                <div class="progress" v-if="is_processing">
                                    <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar"
                                         aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                        <span class="sr-only">Processing...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-xs-8 nopadding" v-if="!is_processing && typeof cart.items !== 'undefined' && cart.items.length > 0">
                                <a href="#" class="button button-3d nomargin fright" v-on:click.prevent="updateQuantities">Update Cart</a>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row clearfix" v-if="typeof cart.items !== 'undefined' && cart.items.length > 0">
        <div class="col-md-6 clearfix">
            <h4>Checkout</h4>
            <form method="post" action="" v-on:submit.prevent="checkout()">
                <div class="col_half">
                    <input type="text" class="sm-form-control" required placeholder="First Name" v-model="checkout_form.firstname">
                </div>
                <div class="col_half col_last">
                    <input type="text" class="sm-form-control" required placeholder="Lastname" v-model="checkout_form.lastname">
                </div>
                <div class="col_half">
                    <input type="email" class="sm-form-control" required placeholder="Email address" v-model="checkout_form.email">
                </div>
                <div class="col_half col_last">
                    <input type="text" class="sm-form-control" required placeholder="Phone number" v-model="checkout_form.phone">
                </div>
                <button type="submit" class="button button-3d nomargin button-black">Place Order</button>
            </form>
        </div>
        <div class="col-md-6 clearfix">
            <div class="table-responsive">
                <h4>Cart Totals</h4>
                <table class="table cart">
                    <tbody>
                        <tr class="cart_item">
                            <td class="cart-product-name">
                                <strong>Total</strong>
                            </td>
                            <td class="cart-product-name">
                                <span class="amount color lead"><strong>@{{ cart.currency + '' + cart.total.formatted }}</strong></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script>
        var cartView = new Vue({
            el: '#main_content_container',
            data: {
                cart: {!! json_encode($cart) !!},
                store_settings: {!! json_encode($storeSettings) !!},
                is_processing: false,
                checkout_form: {
                    firstname: '',
                    lastname: '',
                    email: '',
                    phone: ''
                },
                payment_url: ''
            },
            methods: {
                checkout: function () {
                    var context =  this;
                    swal({
                        title: "Continue Checkout?",
                        text: "You are about to checkout the cart, do you want to continue?",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonText: "Checkout",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.post("/xhr/cart/checkout", context.checkout_form)
                            .then(function (response) {
                                context.cart = headerView.cart = [];
                                // remove the deleted item
                                console.log(response.data);
                                if (typeof response.data.payment_url !== 'undefined') {
                                    context.payment_url = response.data.payment_url;
                                }
                                return swal("Order Placed", "Your order has been submitted, you should get an invoice in your email inbox soon.", "success");
                            }).catch(function (error) {
                                var message = '';
                                if (error.response) {
                                    // The request was made and the server responded with a status code
                                    // that falls out of the range of 2xx
                                    var e = error.response.data.errors[0];
                                    message = e.title;
                                } else if (error.request) {
                                    // The request was made but no response was received
                                    // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                    // http.ClientRequest in node.js
                                    message = 'The request was made but no response was received';
                                } else {
                                    // Something happened in setting up the request that triggered an Error
                                    message = error.message;
                                }
                                return swal("Checkout Failed", message, "warning");
                            });
                    });
                },
                decrementQuantity: function (index) {
                    if (index >= this.cart.items.length || parseInt(this.cart.items[index].quantity, 10) === 0) {
                        return;
                    }
                    this.cart.items[index].quantity -= 1;
                },
                incrementQuantity: function (index) {
                    if (index >= this.cart.items.length) {
                        return;
                    }
                    this.cart.items[index].quantity += 1;
                },
                removeItem: function (index) {
                    console.log(index);
                    var item = this.cart.items[index];
                    // get the actual item
                    var context = this;
                    // set the context
                    if (this.is_processing) {
                        return swal({
                            title: "Please Wait...",
                            text: "Your previous request is still processing.",
                            type: "info"
                        });
                    }
                    swal({
                        title: "Are you sure?",
                        text: "You are about to remove product "+item.name+" from your cart.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, remove it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        context.is_processing = true;
                        axios.delete("/xhr/cart", {
                            params: {id: item.id}
                        }).then(function (response) {
                                context.is_processing = false;
                                context.cart.items.splice(index, 1);
                                // remove the deleted item
                                return swal("Done", "Cart item removed.", "success");
                            }).catch(function (error) {
                                var message = '';
                                if (error.response) {
                                    // The request was made and the server responded with a status code
                                    // that falls out of the range of 2xx
                                    var e = error.response.data.errors[0];
                                    message = e.title;
                                } else if (error.request) {
                                    // The request was made but no response was received
                                    // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                    // http.ClientRequest in node.js
                                    message = 'The request was made but no response was received';
                                } else {
                                    // Something happened in setting up the request that triggered an Error
                                    message = error.message;
                                }
                                context.is_processing = false;
                                return swal("Removed Failed", message, "warning");
                            });
                    });
                },
                updateQuantities: function () {
                    var quantities = [];
                    for (var i = 0; i < this.cart.items.length; i++) {
                        quantities.push({id: this.cart.items[i].id, quantity: this.cart.items[i].quantity})
                    }
                    var context = this;
                    this.is_processing = true;
                    axios.put("/xhr/cart/update-quantities", {
                        quantities: quantities
                    }).then(function (response) {
                        context.cart = headerView.cart = response.data;
                        context.is_processing = false;
                    }).catch(function (error) {
                        var message = '';
                        context.is_processing = false;
                        if (error.response) {
                            // The request was made and the server responded with a status code
                            // that falls out of the range of 2xx
                            var e = error.response.data.errors[0];
                            message = e.title;
                        } else if (error.request) {
                            // The request was made but no response was received
                            // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                            // http.ClientRequest in node.js
                            message = 'The request was made but no response was received';
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            message = error.message;
                        }
                        return swal("Oops!", message, "warning");
                    });
                }
            }
        });

        var headerView = new Vue({
            el: '#header',
            data: {
                search_term: '',
                is_cart_opened: false,
                cart: cartView.cart
            },
            methods: {
                searchProducts: function () {
                    window.location = '/?q=' + encodeURIComponent(this.search_term);
                },
                toggleCartOpen: function () {
                    this.is_cart_opened = !this.is_cart_opened;
                }
            }
        });
    </script>
@endsection