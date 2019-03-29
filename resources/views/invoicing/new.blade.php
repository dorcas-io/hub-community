@extends('layouts.app')
@section('head_css')
    <style type="text/css">

    </style>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="create-order">
        <div class="row">
            <div class="col s12">
                <div class="card-panel">
                    <h4 class="header2">New Invoice <span class="right">Products</span></h4>
                    <div class="row">
                        <form class="col s12" method="post" action="">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col s12 m6">
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="title" name="title" type="text" maxlength="80" required>
                                            <label for="title">Title</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12 m6">
                                            <select name="currency" v-model="currency" class="browser-default" required>
                                                <option value="" disabled>Select Currency</option>
                                                <option value="NGN">Nigerian Naira (NGN)</option>
                                            </select>
                                        </div>
                                        <div class="input-field col s12 m6">
                                            <input id="amount" name="amount" type="number" step="0.01" min="0" v-model="total_amount" required>
                                            <label for="amount">Total Amount</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12" v-bind:class="{'m6': due_date !== null && due_date.length > 0}">
                                            <input type="text" class="custom-datepicker" name="due_at" id="due_at" v-model="due_date">
                                            <label for="due_at">Due Date</label>
                                        </div>
                                        <div class="col s12 m6" v-if="due_date !== null && due_date.length > 0">
                                            <p>Invoice Reminders</p>
                                            <div class="switch">
                                                <label>
                                                    Off
                                                    <input type="checkbox" name="reminders_on">
                                                    <span class="lever"></span>
                                                    On
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <textarea id="description" name="description" class="materialize-textarea" v-model="description"></textarea>
                                            <label for="description">Description</label>
                                        </div>
                                    </div>
                                    <div class="row" v-if="customer_mode === 'add_new'">
                                        <div class="input-field col s12 m6">
                                            <input id="customer_firstname" name="customer_firstname" type="text" maxlength="30">
                                            <label for="customer_firstname">Customer's Firstname</label>
                                        </div>
                                        <div class="input-field col s12 m6">
                                            <input id="customer_lastname" name="customer_lastname" type="text" maxlength="30">
                                            <label for="customer_lastname">Customer's Lastname</label>
                                        </div>
                                    </div>
                                    <div class="row" v-if="customer_mode === 'add_new'">
                                        <div class="input-field col s12 m6">
                                            <input id="customer_email" name="customer_email" type="email" maxlength="80">
                                            <label for="customer_email">Customer's Email</label>
                                        </div>
                                        <div class="input-field col s12 m6">
                                            <input id="customer_phone" name="customer_phone" type="text" maxlength="30">
                                            <label for="customer_phone">Customer's Phone</label>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="input-field col s12">
                                            <select name="customer" id="customer" class="browser-default"
                                                    v-model="customer_mode" required>
                                                <option value="" disabled selected>Add a customer to this invoice</option>
                                                <option value="add_new">Add a new customer</option>
                                                <optgroup label="Select an existing customer">
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}">{{ $customer->firstname . ' ' . $customer->lastname }}</option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col s12 m6">
                                    <!--<div class="input-field col s12 mb-9">
                                        <select v-model="product_style" class="browser-default" v-on:change="checkCurrency">
                                            <option value="" disabled>How do you want to add products?</option>
                                            <option value="inline">Add a product here directly</option>
                                            <option value="select">Select from your products list</option>
                                        </select>
                                    </div>-->
                                    <div class="row" v-if="product_style === 'inline'">
                                        <div class="col s12">
                                            <div class="row">
                                                <div class="input-field col s12">
                                                    <input id="product_name" name="product_name" type="text" maxlength="80">
                                                    <label for="product_name">Product Name</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="input-field col s12 m6">
                                                    <input id="product_quantity" name="product_quantity"
                                                           type="number" min="1" value="1" v-model="inline.qty"
                                                           v-on:keyup="updateInlineTotal"
                                                           v-on:change="updateInlineTotal">
                                                    <label for="product_quantity">Quantity</label>
                                                </div>
                                                <div class="input-field col s12 m6">
                                                    <input id="product_price" name="product_price" type="number"
                                                           step="0.01" min="0" v-model="inline.unit_price"
                                                           v-on:keyup="updateInlineTotal"
                                                           v-on:change="updateInlineTotal">
                                                    <label for="product_price">Unit Price</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col s12">
                                                    <p>Is this a Proforma/Quote</p>
                                                    <div class="switch">
                                                        <label>
                                                            No
                                                            <input type="checkbox" name="is_quote" value="1">
                                                            <span class="lever"></span>
                                                            Yes
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" v-if="product_style === 'select' && currency.length === 3">
                                        <div class="col s12">
                                            <cart-item v-for="(item, index) in cart" :key="index"
                                                       :quantity_id="'quantity' + index"
                                                       :unit_price_id="'price' + index"
                                                       :index="index"
                                                       v-on:sync-cart="syncCart"
                                                       v-on:remove-item="removeItem"></cart-item>
                                        </div>
                                        <a href="#" class="waves-effect waves-light grey-text btn-flat text-darken-3 mt-6 mb-6"
                                           v-on:click.prevent="addProductField">Add Product</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button class="btn blue waves-effect waves-light right" type="submit" name="action">Submit
                                        <i class="material-icons right">send</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('help_modal_content')
    @component('layouts.slots.video-embed')
        //www.youtube.com/embed/UdXldTFenQk?rel=0
    @endcomponent
@endsection
@section('body_js')
    <script type="text/javascript">
        $(function() {
            $('.custom-datepicker').pickadate({
                today: 'Today',
                clear: 'Clear',
                close: 'Ok',
                closeOnSelect: false,
                onClose: function() {
                    console.log('onClose');
                    vm.due_date = this.get();
                }
            });
        });

        var vm = new Vue({
            el: '#create-order',
            data: {
                products: {!! json_encode($products) !!},
                currency: '{{ old('currency', '') }}',
                total_amount: {{ old('amount', 0) }},
                due_date: '{{ old('due_at', '') }}',
                description: '{{ old('description') }}',
                product_style: 'inline',
                inline: {qty: 0, unit_price: 0},
                cart: [],
                customer_mode: ''
            },
            computed: {

            },
            methods: {
                checkCurrency: function () {
                    if (this.product_style === 'select' && this.currency.length !== 3) {
                        Materialize.toast('Please select a currency for the order to continue...', 4000);
                    }
                    if (this.product_style === 'select') {
                        this.updateCartTotal();
                    } else if (this.product_style === 'inline') {
                        this.updateInlineTotal();
                    }
                },
                addProductField: function () {
                    if (this.products.length === 0) {
                        swal({
                            title: "Add a Product",
                            text: "You have no product in your inventory, would you like to add one now?",
                            type: "info",
                            showCancelButton: true,
                            confirmButtonText: "Yes, add one."
                        }, function() {
                            window.location = '/inventory/products';
                        });
                        return;
                    }
                    var item = {quantity: 0, id: Hub.utilities.randomId(10), unit_price: 0};
                    this.cart.push(item);
                },
                updateInlineTotal: function () {
                    this.total_amount = parseInt(this.inline.qty, 10) * parseFloat(this.inline.unit_price);
                },
                updateCartTotal: function () {
                    var total = 0;
                    for (var i = 0; i < this.cart.length; i++) {
                        total += (isNaN(this.cart[i].quantity) ? 0 : this.cart[i].quantity) * (isNaN(this.cart[i].unit_price) ? 0 : this.cart[i].unit_price);
                    }
                    this.total_amount = total;
                },
                syncCart: function (index, quantity, unit_price, id) {
                    this.cart.splice(index, 1, {quantity: parseInt(quantity, 10), unit_price: parseFloat(unit_price), id: id});
                    this.updateCartTotal();
                },
                removeItem: function (index) {
                    this.cart.splice(index, 1);
                    this.updateCartTotal();
                }
            }
        });
    </script>
@endsection