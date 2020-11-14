@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn dropdown-settings waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#!" data-activates="dropdown1" style="">
        <i class="material-icons hide-on-med-and-up">settings</i>
        <span class="hide-on-small-onl">Manage Product</span>
        <i class="material-icons right">arrow_drop_down</i>
    </a>
    <ul id="dropdown1" class="dropdown-content">
        @if ($subdomains->count() > 0)
            <li>
                <a href="#add-product-image" class="grey-text text-darken-2 modal-trigger">Add Product Image</a>
            </li>
        @else
            <li>
                <a href="{{ route('apps.ecommerce.domains') }}" class="grey-text text-darken-2">Reserve a Dorcas Store SubDomain</a>
            </li>
        @endif
        <li>
            <a href="#manage-inventory" class="grey-text text-darken-2 modal-trigger">Manage Inventory</a>
        </li>
    </ul>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="details-view">
        <div class="section row">
            <div class="col s12 m4">
                <div class="card">
                    <div class="card-image waves-effect waves-block waves-light">
                        <img class="activator" src="{{ cdn('images/gallery/imani-clovis-547617-unsplash.jpg') }}">
                    </div>
                    <div class="card-content">
                        <span class="card-title activator grey-text text-darken-3">@{{ product.name }}<i class="material-icons right">edit</i></span>
                        <p class="mb-4">
                            <strong>In Stock: @{{ product.inventory }}</strong>
                        </p>
                        <p>@{{ product.description }}</p>
                        <div class="pt-4" v-if="product.categories.data.length > 0">
                            <strong>Categories</strong>
                            <div class="chip" v-for="(category, index) in product.categories.data" :key="category.id">
                                @{{ category.name }}
                                <i class="close material-icons" data-ignore-click="true" v-bind:data-index="index"
                                   v-on:click.prevent="removeCategory(index)">close</i>
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">Edit Details<i class="material-icons right">close</i></span>
                        <form method="post" action="">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="name" type="text" v-model="product.name" name="name" maxlength="80" required>
                                    <label for="name">Product Name</label>
                                </div>
                                <div class="input-field col s12">
                                    <textarea id="description" name="description" class="materialize-textarea" v-model="product.description">@{{ product.description }}</textarea>
                                    <label for="description">Description</label>
                                </div>
                                <div class="col s12" v-if="typeof product.prices.data !== 'undefined'">
                                    <product-price-control v-for="(price, index) in product.prices.data" :key="price.id"
                                       :id_currency="price.id" :id_price="index + price.id"
                                       :opening_price="price.unit_price.raw" :opening_currency="price.currency"
                                       :index="index" v-on:remove="removeEntry"></product-price-control>
                                </div>

                                <a href="#" class="waves-effect waves-light grey-text btn-flat text-darken-3 mb-1"
                                   v-on:click.prevent="addPriceField">Add Currency Price</a>

                                <div class="input-field col s12">
                                    <input id="default_price" type="number" name="default_price" v-model="product.default_unit_price.raw" min="0">
                                    <label for="default_price">Default Fallback Unit Price (for other currencies)</label>
                                </div>
                                <div class="col s12 mb-2">
                                    {{ method_field('PUT') }}
                                    <div class="progress" v-if="updating">
                                        <div class="indeterminate"></div>
                                    </div>
                                    <button class="btn waves-effect waves-light" type="submit" name="action"
                                            v-if="!updating">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col s12">
                    <h4>Add Categories</h4>
                    <form method="post" action="{{ route('apps.inventory.single.categories', [$product->id]) }}">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="categories[]" id="categories" multiple>
                                    <option disabled="">Select one or more Categories</option>
                                    <option v-for="category in categories" :key="category.id"
                                            v-if="productCategories.indexOf(category.id) === -1"
                                            v-bind:value="category.id">@{{ category.name }}</option>
                                </select>
                            </div>
                            <div class="col s12 mb-2">
                                <button class="btn waves-effect waves-light" type="submit" name="action"
                                        v-if="!updating">Add Categories</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="progress" v-if="updating">
                    <div class="indeterminate"></div>
                </div>
            </div>
            <div class="col s12 m8">
                <ul class="tabs tabs-fixed-width z-depth-1">
                    <li class="tab col s6"><a href="#sales">Sales</a></li>
                    <li class="tab col s6"><a href="#stocks">Stock Manager</a></li>
                    <li class="tab col s6"><a href="#images">Product Images</a></li>
                </ul>
                <div id="sales" class="col s12">
                    <div class="row section" id="customers-list" v-on:click="clickAction($event)">
                        <table class="bootstrap-table responsive-table" v-if="showOrders"
                               data-url="{{ url('/xhr/inventory/orders') }}?product={{ $product->id }}"
                               data-page-list="[10,25,50,100,200,300,500]"
                               data-row-attributes="Hub.Table.formatOrders"
                               data-side-pagination="server"
                               data-show-refresh="true"
                               data-sort-class="sortable"
                               data-pagination="true"
                               data-search="true"
                               data-unique-id="id"
                               data-search-on-enter-key="true"
                               id="orders-table">
                            <thead>
                            <tr>
                                <th data-field="title" data-width="25%">Title</th>
                                <th data-field="description" data-width="35%">Description</th>
                                <th data-field="currency" data-width="5%">Currency</th>
                                <th data-field="amount.formatted" data-width="10%">Amount</th>
                                <th data-field="due_at" data-width="10%">Due At</th>
                                <th data-field="reminder_on" data-width="10%">Reminder On?</th>
                                <th data-field="created_at" data-width="10%">Created</th>
                                <th data-field="buttons" data-width="5%">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="col s12" v-if="!showOrders">
                            @component('layouts.slots.empty-fullpage')
                                @slot('icon')
                                    monetization_on
                                @endslot
                                Add customer orders to generate invoices, and keep track of your sales.
                                @slot('buttons')
                                    <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="{{ route('apps.invoicing.orders.new') }}">
                                        New Order
                                    </a>
                                @endslot
                            @endcomponent
                        </div>
                    </div>
                </div>
                <div id="stocks" class="col s12">
                    <div class="row section" id="stock-list" v-on:click="clickAction($event)">
                        <table class="bootstrap-table responsive-table" v-if="product.stocks.data.length > 0"
                               data-url="{{ url('/xhr/inventory/products', [$product->id, 'stocks']) }}"
                               data-page-list="[10,25,50,100,200,300,500]"
                               data-row-attributes="Hub.Table.formatStocks"
                               data-side-pagination="server"
                               data-show-refresh="true"
                               data-sort-class="sortable"
                               data-pagination="true"
                               data-search="false"
                               data-unique-id="id"
                               data-search-on-enter-key="true"
                               id="stocks-table">
                            <thead>
                            <tr>
                                <th data-field="activity" data-width="20%">Activity</th>
                                <th data-field="quantity" data-width="10%">Quantity</th>
                                <th data-field="comment" data-width="50%">Comment</th>
                                <th data-field="date" data-width="20%">Date</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="col s12" v-if="product.stocks.data.length === 0">
                            @component('layouts.slots.empty-fullpage')
                                @slot('icon')
                                    local_shipping
                                @endslot
                                Manage product stock, and see the log of all these activities here.
                                @slot('buttons')
                                    <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger" href="#manage-inventory">
                                        Manage Inventory
                                    </a>
                                @endslot
                            @endcomponent
                        </div>
                    </div>
                </div>
                <div id="images" class="col s12">
                    <div class="row section" id="images-list" v-on:click="clickAction($event)">
                        <table class="bootstrap-table responsive-table" v-if="product.images.data.length > 0"
                               data-page-list="[10,25,50,100,200,300,500]"
                               data-show-refresh="true"
                               data-sort-class="sortable"
                               data-pagination="true"
                               data-search="false"
                               data-search-on-enter-key="true"
                               id="images-table">
                            <thead>
                            <tr>
                                <th data-field="title" data-width="20%">ID</th>
                                <th data-field="image" data-width="40%">Image</th>
                                <th data-field="created_at" data-width="20%">Date</th>
                                <th data-field="menu" data-width="20%">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($product->images['data']) > 0)
                                @foreach ($product->images['data'] as $image)
                                    <tr>
                                        <td>Product #{{ $loop->iteration }}</td>
                                        <td>
                                            <img src="{{ $image['url'] }}" class="materialboxed responsive-img"
                                                 title="Product #{{ $product->id }}" width="500" />
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($image['created_at'])->format('D jS M, Y') }}</td>
                                        <td>
                                            <a class="waves-effect btn-flat red-text text-darken-3 btn-small remove"
                                               data-id="{{ $image['id'] }}" data-index="{{ $loop->index }}"
                                               data-name="Product #{{ $loop->iteration }}">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <div class="col s12" v-if="product.images.data.length === 0">
                            @component('layouts.slots.empty-fullpage')
                                @slot('icon')
                                    local_mall
                                @endslot
                                {{ $subdomains->count() === 0 ? 'To add product images, you need to enable your Dorcas store, visit Domains and reserve your subdomain' : 'Upload images for your product to be displayed in your store.' }}
                                @slot('buttons')
                                    @if ($subdomains->count() === 0)
                                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light"
                                           href="{{ route('apps.ecommerce.domains') }}">
                                            Reserve your Domain
                                        </a>
                                    @else
                                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger"
                                           href="#add-product-image">
                                            Add an Image
                                        </a>
                                    @endif
                                @endslot
                            @endcomponent
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('inventory.modals.manage-inventory')
        @include('inventory.modals.add-product-image')
    </div>
@endsection
@section('help_modal_content')
    @component('layouts.slots.video-embed')
        //www.youtube.com/embed/UdXldTFenQk?rel=0
    @endcomponent
@endsection
@section('body_js')
    <script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}"></script>
    <script type="text/javascript">

        $(function(){
            $('.materialboxed').materialbox();
        });

        new Vue({
            el: '#details-view',
            data: {
                product: {!! json_encode($product) !!},
                subdomains: {!! json_encode($subdomains ?: []) !!},
                updating: false,
                categories: {!! json_encode($categories ?: []) !!}
            },
            computed: {
                productCategories: function () {
                    var selected = [];
                    for (var i = 0; i < this.product.categories.data.length; i++) {
                        selected.push(this.product.categories.data[i].id);
                    }
                    return selected;
                },
                showOrders: function () {
                    return typeof this.product.orders !== 'undefined' && typeof this.product.orders.data !== 'undefined' &&
                            this.product.orders.data.length > 0;
                }
            },
            methods: {
                clickAction: function (event) {
                    console.log(event.target);
                    var target = event.target.tagName.toLowerCase() === 'i' ? event.target.parentNode : event.target;
                    var attrs = Hub.utilities.getElementAttributes(target);
                    // get the attributes
                    var classList = target.classList;
                    if (classList.contains('view')) {
                        return true;
                    } else if (classList.contains('remove')) {
                        this.delete(attrs);
                    }
                },
                delete: function (attributes) {
                    console.log(attributes);
                    var name = attributes['data-name'] || '';
                    var index = attributes['data-index'] || '';
                    var id = attributes['data-id'] || null;
                    if (id === null) {
                        return false;
                    }
                    context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to delete image " + name + " from this product.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/inventory/products/" + context.product.id + "/images", {
                            params: {id: id}
                        }).then(function (response) {
                                console.log(response);
                                context.visible = false;
                                context.contactsCount -= 1;
                                $('#images-table').bootstrapTable('remove', {field: 'title', values: [name]});
                                context.product.images.data.splice(index, 1);
                                // remove the image that was just deleted
                                return swal("Deleted!", "The product was successfully deleted.", "success");
                            }).catch(function (error) {
                                var message = '';
                                console.log(error);
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
                                return swal("Delete Failed", message, "warning");
                            });
                    });
                },
                addPriceField: function () {
                    var price = {currency: 'NGN', id: Hub.utilities.randomId(10), unit_price: {formatted: 0, raw: 0}};
                    if (typeof this.product.prices === 'undefined' || typeof this.product.prices.data === 'undefined') {
                        this.product.prices = {data: []};
                    }
                    this.product.prices.data.push(price);
                },
                removeEntry: function (index) {
                    this.product.prices.data.splice(index, 1);
                },
                removeCategory: function (index) {
                   var category = this.product.categories.data[index];
                   // get the category to be removed
                    if (this.updating) {
                        Materialize.toast('Please wait till the current activity completes...', 4000);
                        return;
                    }
                    this.updating = true;
                    var context = this;
                    axios.delete("/xhr/inventory/products/" + context.product.id + "/categories", {
                        data: {categories: [category.id]}
                    }).then(function (response) {
                        console.log(response);
                        console.log(index);
                        if (index !== null) {
                            context.product.categories.data.splice(index, 1);
                        }
                        context.updating = false;
                        Materialize.toast('Category '+category.name+' removed.', 2000);
                    })
                        .catch(function (error) {
                            var message = '';
                            console.log(error);
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
                            context.updating = false;
                            return swal("Delete Failed", message, "warning");
                        });
                }
            }
        });
    </script>
@endsection