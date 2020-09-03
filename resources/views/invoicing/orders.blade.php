@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="{{ route('apps.invoicing.orders.new') }}">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">New Order</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section" id="orders-list" v-on:click="clickAction($event)">
            <table class="bootstrap-table responsive-table" v-if="ordersCount > 0"
                   data-url="{{ url('/xhr/inventory/orders') }}"
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
                    <th data-field="invoice_number" data-width="5%">Invoice #</th>
                    <th data-field="title" data-width="15%">Title</th>
                    <th data-field="description" data-width="25%">Description</th>
                    <th data-field="currency" data-width="5%">Currency</th>
                    <th data-field="amount.formatted" data-width="10%">Amount</th>
                    <th data-field="cart_content" data-width="10%">Product(s)</th>
                    <th data-field="reminder_on" data-width="5%">Reminder?</th>
                    <th data-field="due_at" data-width="10%">Due At</th>
                    <th data-field="created_at" data-width="10%">Created</th>
                    <th data-field="buttons" data-width="5%">&nbsp;</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <div class="col s12" v-if="ordersCount === 0">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        monetization_on
                    @endslot
                    Create orders to automatically generate invoices for your customers.
                    @slot('buttons')
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="{{ route('apps.invoicing.orders.new') }}">
                            New Order
                        </a>
                    @endslot
                @endcomponent
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
            el: '#orders-list',
            data: {
                ordersCount: {{ $ordersCount }}
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
                    context = this;
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