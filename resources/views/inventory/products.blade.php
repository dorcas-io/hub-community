@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn dropdown-settings waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#!" data-activates="dropdown1" style="">
        <i class="material-icons hide-on-med-and-up">settings</i>
        <span class="hide-on-small-onl">Manage Products</span>
        <i class="material-icons right">arrow_drop_down</i>
    </a>
    <ul id="dropdown1" class="dropdown-content">
        <li>
            <a href="#add-product" class="grey-text text-darken-2 modal-trigger">Add Product</a>
        </li>
        @if (!empty($subdomain))
            <li>
                <a href="{{ $subdomain . '/store' }}" class="grey-text text-darken-2" target="_blank">Open Web Store</a>
            </li>
        @endif
        <!--<li>
            <a href="{{ route('apps.inventory.import') }}" class="grey-text text-darken-2">Import from CSV</a>
        </li>-->
    </ul>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section" id="products-list" v-on:click="clickAction($event)">
            <table class="bootstrap-table responsive-table" v-if="productsCount > 0"
                   data-url="{{ url('/xhr/inventory/products') }}"
                   data-page-list="[10,25,50,100,200,300,500]"
                   data-row-attributes="Hub.Table.formatProducts"
                   data-side-pagination="server"
                   data-show-refresh="true"
                   data-sort-class="sortable"
                   data-pagination="true"
                   data-search="true"
                   data-unique-id="id"
                   data-search-on-enter-key="true"
                   id="products-table">
                <thead>
                <tr>
                    <th data-field="name" data-width="25%">Product</th>
                    <th data-field="description_info" data-width="25%">Description</th>
                    <th data-field="inventory" data-width="10%">Stock</th>
                    <th data-field="unit_prices" data-width="15%">Unit Price(s)</th>
                    <th data-field="created_at" data-width="10%">Added On</th>
                    <th data-field="buttons" data-width="15%">&nbsp;</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <div class="col s12" v-if="productsCount === 0">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        assistant
                    @endslot
                    Add products to be able to manage stock levels, and orders with ease.
                    @slot('buttons')
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger" href="#add-product">
                            Add Product
                        </a>
                    <!--<a class="btn-flat grey darken-3 white-text waves-effect waves-light" href="{{ route('apps.inventory.import') }}">
                            Import from CSV
                        </a>-->
                    @endslot
                @endcomponent
            </div>
        </div>
        @include('inventory.modals.new')
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
        $(function() {
            $('input[type=checkbox].check-all').on('change', function () {
                var className = $(this).parent('div').first().data('item-class') || '';
                if (className.length > 0) {
                    $('input[type=checkbox].'+className).prop('checked', $(this).prop('checked'));
                }
            });
        });
        new Vue({
            el: '#products-list',
            data: {
                productsCount: {{ $productsCount }}
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
                    var id = attributes['data-id'] || null;
                    if (id === null) {
                        return false;
                    }
                    var context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to delete product " + name + " from your inventory.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/inventory/products/" + id)
                            .then(function (response) {
                                console.log(response);
                                context.visible = false;
                                context.contactsCount -= 1;
                                $('#products-table').bootstrapTable('removeByUniqueId', response.data.id);
                                return swal("Deleted!", "The product was successfully deleted.", "success");
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
                                return swal("Delete Failed", message, "warning");
                            });
                    });
                }
            }
        });
    </script>
@endsection