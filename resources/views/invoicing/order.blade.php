@extends('layouts.app')
@section('head_css')
    <style type="text/css">
        .task-cat {
            font-weight: 600 !important;
            font-size: 1rem !important;
        }
    </style>
@endsection
@section('body_main_content_header_button')
    <a class="btn dropdown-settings waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#!" data-activates="dropdown1" style="">
        <i class="material-icons hide-on-med-and-up">settings</i>
        <span class="hide-on-small-onl">Options</span>
        <i class="material-icons right">arrow_drop_down</i>
    </a>
    <ul id="dropdown1" class="dropdown-content">
        <li>
            <a href="{{ route('apps.invoicing.orders.new') }}">New Order</a>
        </li>
        <li>
            <a href="#!" class="red-text" v-on:click.prevent="deleteOrder('{{ $order->id }}')">Delete Order</a>
        </li>
    </ul>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    @include('blocks.ui-response-alert')
    <div class="container" id="details-view">
        <div class="row section">
            <div class="col s12 m3">
                <div class="card gradient-shadow gradient-45deg-light-blue-cyan border-radius-3">
                    <div class="card-content center">
                        <h5 class="white-text lighten-4">@{{ order.invoice_number }}</h5>
                        <p class="white-text lighten-4">Invoice #</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m3">
                <div class="card gradient-shadow gradient-45deg-light-blue-cyan border-radius-3">
                    <div class="card-content center">
                        <h5 class="white-text lighten-4">@{{ order.currency }} @{{ order.amount.formatted }}</h5>
                        <p class="white-text lighten-4">Total Cost</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m3">
                <div class="card gradient-shadow gradient-45deg-light-blue-cyan border-radius-3">
                    <div class="card-content center">
                        <h5 class="white-text lighten-4">@{{ dueDate }}</h5>
                        <p class="white-text lighten-4">Due By</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m3">
                <div class="card gradient-shadow gradient-45deg-light-blue-cyan border-radius-3">
                    <div class="card-content center">
                        <h5 class="white-text lighten-4">@{{ reminderIsOn }}</h5>
                        <p class="white-text lighten-4">Reminder On</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="section row">
            <div class="col s12 m4">
                <div class="card">
                    <div class="card-image waves-effect waves-block waves-light">
                        <img class="activator" src="{{ cdn('images/gallery/ali-yahya-435967-unsplash.jpg') }}">
                    </div>
                    <div class="card-content">
                        <span class="card-title activator grey-text text-darken-3">Order # @{{ order.invoice_number }}<i class="material-icons right">edit</i></span>
                        <p>@{{ order.description }}</p>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">Edit Details<i class="material-icons right">close</i></span>
                        <form method="post" action="">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="name" type="text" v-model="order.title" name="name" maxlength="80" required>
                                    <label for="name">Title</label>
                                </div>
                                <div class="input-field col s12">
                                    <textarea id="description" name="description" class="materialize-textarea" v-model="order.description">@{{ order.description }}</textarea>
                                    <label for="description">Description</label>
                                </div>
                                <div class="input-field col s12">
                                    <input type="text" class="custom-datepicker" name="due_at" id="due_at" v-model="order.due_at">
                                    <label for="due_at">Due Date</label>
                                </div>
                                <div class="col s12 mb-3" v-if="typeof order.due_at !== 'undefined' && order.due_at !== null">
                                    <p>Invoice Reminders</p>
                                    <div class="switch">
                                        <label>
                                            Off
                                            <input type="checkbox" name="reminders_on" v-model="order.has_reminders">
                                            <span class="lever"></span>
                                            On
                                        </label>
                                    </div>
                                </div>
                                <div class="col s12 mb-3">
                                    {{ method_field('PUT') }}
                                    <div class="progress" v-if="updating">
                                        <div class="indeterminate"></div>
                                    </div>
                                    <button class="btn waves-effect waves-light" type="submit" name="action"
                                            v-if="!updating" v-on:click.prevent="updateDetails">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <ul id="issues-collection" class="collection z-depth-1">
                    <li class="collection-item">
                        <h5 class="collection-header m-0">Products</h5>
                    </li>
                    <li class="collection-item" v-if="typeof order.inline_product !== 'undefined'">
                        <div class="row">
                            <div class="col s10">
                                <p class="collections-title">
                                    <strong>@{{ order.inline_product.name }}</strong><br>
                                    <small>@{{ order.currency }} @{{ order.inline_product.unit_price }} / unit = @{{ order.currency }} @{{ order.inline_product.unit_price * order.inline_product.quantity }}</small>
                                </p>
                            </div>
                            <div class="col s2 center-align">
                                <span class="task-cat blue bold">x @{{ order.inline_product.quantity }}</span>
                            </div>
                        </div>
                    </li>
                    <li class="collection-item" v-for="product in order.products.data">
                        <div class="row" style="cursor: pointer;" v-on:click.prevent="showProduct(product.id)">
                            <div class="col s10">
                                <p class="collections-title">
                                    <strong>@{{ product.name }}</strong><br>
                                    <small>@{{ order.currency }} @{{ product.sale.unit_price }} / unit = @{{ order.currency }} @{{ product.sale.unit_price * product.sale.quantity }}</small>
                                </p>
                            </div>
                            <div class="col s2 center-align">
                                <span class="task-cat blue bold">x @{{ product.sale.quantity }}</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col s12 m8">
                <div class="row section" id="customers-list" v-on:click="clickAction($event)">
                    <div class="progress" v-if="deleting">
                        <div class="indeterminate"></div>
                    </div>
                    <ul class="tabs tabs-fixed-width z-depth-1">
                        <li class="tab col s6"><a href="#customers-listing">Customers</a></li>
                        <li class="tab col s6"><a href="#transactions-listing">Transactions</a></li>
                    </ul>
                    <div id="customers-listing" class="col s12">
                        <table class="bootstrap-table responsive-table"
                               data-page-list="[10,25,50,100,200,300,500]"
                               data-sort-class="sortable"
                               data-pagination="true"
                               data-search="true"
                               id="orders-table">
                            <thead>
                            <tr>
                                <th data-sortable="true" data-field="basic_info" data-width="35%">Customer</th>
                                <th data-sortable="true" data-field="phone" data-width="15%">Phone</th>
                                <th data-sortable="true" data-field="currency" data-width="15%">Paid?</th>
                                <th data-sortable="true" data-field="buttons" data-width="35%">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($order->customers['data'] as $customer)
                                <tr>
                                    <td>
                                        <div class="row valign-wrapper">
                                            <div class="col s3">
                                                <img src="{{ $customer['photo'] }}" alt="" class="circle responsive-img">
                                            </div>
                                            <div class="col s9 no-padding">
                                            <span class="black-text">
                                                {{ implode(' ', [$customer['firstname'], $customer['lastname']]) }}<br>
                                                <small>{{ $customer['email'] }}</small>
                                            </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $customer['phone'] }}</td>
                                    <td>
                                        <div class="chip">{{ !empty($customer['sale']) && $customer['sale']['is_paid'] ? 'Yes' : 'No' }}</div>
                                    </td>
                                    <td>
                                        <a class="waves-effect btn-flat remove grey-text text-darken-3 btn-small" target="_blank"
                                           href="{{ (string) $dorcasUrlGenerator->getUrl('invoices/' . $order->id, ['query' => ['customer' => $customer['id']]]) }}">Invoice</a>
                                        @if (!empty($customer['customer_order']['data']) && !$customer['customer_order']['data']['is_paid'])
                                            <a class="waves-effect btn-flat mark-paid grey-text text-darken-3 btn-small" href="#" data-id="{{ $customer['id'] }}"
                                               data-name="{{ implode(' ', [$customer['firstname'], $customer['lastname']]) }}">Mark Paid</a>
                                        @endif
                                        @if (!empty($customer['customer_order']['data']) && !empty($customer['customer_order']['data']['transactions']['data']))
                                            <a class="waves-effect btn-flat transactions grey-text text-darken-3 btn-small" href="#"
                                               data-customer-index="{{ $loop->index }}">TXNs</a>
                                        @endif
                                        <a class="waves-effect btn-flat remove red-text btn-small" href="#" data-id="{{ $customer['id'] }}"
                                           data-name="{{ implode(' ', [$customer['firstname'], $customer['lastname']]) }}">DELETE</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="transactions-listing" class="col s12">
                        <div class="card mt-4 hoverable" v-if="selectedCustomer !== null">
                            <div class="card-content">
                                <span class="card-title">@{{ selectedCustomer.firstname + ' ' + selectedCustomer.lastname }}</span>
                                <p class="flow-text">
                                    <strong>Email: </strong> @{{ selectedCustomer.email }}<br>
                                    <strong>Phone: </strong> @{{ selectedCustomer.phone }}
                                </p>
                            </div>
                        </div>
                        <table class="bordered Highlight responsive-table mt-4 striped" v-if="selectedCustomer !== null">
                            <thead>
                                <tr>
                                    <th>Channel</th>
                                    <th>Ref.</th>
                                    <th>Amount</th>
                                    <th>Successful</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="txn in transactions" :key="txn.reference">
                                    <td>@{{ txn.channel.title_case() }}</td>
                                    <td>@{{ txn.reference }}</td>
                                    <td>@{{ txn.currency }} @{{ txn.amount }}</td>
                                    <td>@{{ txn.is_successful ? "Yes" : "No" }}</td>
                                    <td>@{{ txn.response_description }}</td>
                                    <td>@{{ moment(txn.created_at, 'DD MMM, YYYY HH:mm') }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="card mt-4 hoverable" v-if="selectedCustomer === null">
                            <div class="card-content">
                                <p class="flow-text">
                                    Click the <strong>TXNs</strong> button on a customer in the "Customers" table to see the details of all
                                    payment transactions through your configured Payment gateway.
                                </p>
                            </div>
                        </div>
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
    <script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('.custom-datepicker').pickadate({
                selectMonths: true, // Creates a dropdown to control month
                selectYears: 15, // Creates a dropdown of 15 years to control year,
                today: 'Today',
                clear: 'Clear',
                close: 'Ok',
                closeOnSelect: false,
                container: 'body',

                onClose: function() {
                    vm.order.due_at = this.get();
                }
            });
        });

        new Vue({
            el: '#dropdown1',
            methods: {
                deleteOrder: function (orderId) {
                    swal({
                        title: "Are you sure?",
                        text: "You are about to delete this order.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/inventory/orders/" + orderId)
                            .then(function (response) {
                                console.log(response);
                                window.location = "/apps/invoicing/orders";
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

        var vm = new Vue({
            el: '#details-views',
            data: {
                order: {!! json_encode($order) !!},
                updating: false,
                deleting: false,
                selectedCustomer: null,
                transactions: []
            },
            mounted: function () {
                if (typeof this.order.due_at !== 'undefined' && this.order.due_at !== null) {
                    this.order.due_at = moment(this.order.due_at).format('DD MMMM, YYYY');
                }
            },
            computed: {
                reminderIsOn: function () {
                    return this.order.has_reminders ? 'Yes' : 'No';
                },
                dueDate: function () {
                    if (typeof this.order.due_at === 'undefined' || this.order.due_at === null) {
                        return 'Not Set';
                    }
                    return moment(this.order.due_at).format('DD MMM, YYYY');
                }
            },
            methods: {
                moment: function (dateString, format) {
                    return moment(dateString).format(format);
                },
                showProduct: function (id) {
                    window.location = '/apps/inventory/products/'+id;
                },
                updateDetails: function () {
                    var context = this;
                    context.updating = true;
                    axios.put("/xhr/inventory/orders/" + context.order.id, {
                        title: context.order.title,
                        description: context.order.description,
                        due_at: context.adjusted_due_at,
                        reminders_on: context.order.has_reminders
                    }).then(function (response) {
                        console.log(response);
                        context.updating = false;
                        Materialize.toast("Your changes were successfully saved.", 4000);
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
                        context.updating = false;
                        return swal("Oops!", message, "warning");
                    });
                },
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
                    } else if (classList.contains('mark-paid')) {
                        this.markPaid(attrs);
                    } else if (classList.contains('transactions')) {
                        this.selectedCustomer = null;
                        this.transactions = [];
                        this.showTransactions(attrs);
                    }
                },
                markPaid: function (attributes) {
                    console.log(attributes);
                    if (this.deleting) {
                        Materialize.toast("Wait for the current activity to complete first.", 4000);
                        return;
                    }
                    var name = attributes['data-name'] || '';
                    var id = attributes['data-id'] || null;
                    if (id === null) {
                        return false;
                    }
                    context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to set the order as paid for by customer " + name,
                        type: "warning",
                        showCancelButton: true,

                        confirmButtonText: "Yes, continue.",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        context.deleting = true;
                        axios.put("/xhr/inventory/orders/" + context.order.id + "/customers", {
                            id: id,
                            is_paid: 1
                        }).then(function (response) {
                            console.log(response);
                            window.location = "/apps/invoicing/orders/" + context.order.id;
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
                                return swal("Update Failed", message, "warning");
                            });
                    });
                },
                delete: function (attributes) {
                    console.log(attributes);
                    if (this.deleting) {
                        Materialize.toast("Wait for the current activity to complete first.", 4000);
                        return;
                    }
                    var name = attributes['data-name'] || '';
                    var id = attributes['data-id'] || null;
                    if (id === null) {
                        return false;
                    }
                    var customer = this.order.customers.data.find(function (customer) {
                        return customer.id === id;
                    });
                    if (typeof customer.sale !== 'undefined' && typeof customer.sale.is_paid !== 'undefined' && customer.sale.is_paid) {
                        Materialize.toast("This customer has already paid for the order. You should delete the order instead.", 4000);
                        return;
                    }
                    if (this.order.customers.data.length === 1) {
                        Materialize.toast("There is just one customer left on the order. You should delete the order instead.", 4000);
                        return;
                    }
                    context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to delete customer " + name + " from order #" + context.order.invoice_number,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        context.deleting = true;
                        axios.delete("/xhr/inventory/orders/" + context.order.id + "/customers", {
                            data: {id: id}
                        }).then(function (response) {
                                console.log(response);
                                window.location = "/apps/invoicing/orders/" + context.order.id;
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
                },
                showTransactions: function (attributes) {
                    var index = attributes['data-customer-index'] || null;
                    if (index === null) {
                        return false;
                    }
                    var customer = typeof this.order.customers.data !== 'undefined' ? this.order.customers.data[index] : null;
                    if (customer === null) {
                        return false;
                    }
                    this.selectedCustomer = customer;
                    var transactions = typeof customer.customer_order.data.transactions.data !== 'undefined' ? customer.customer_order.data.transactions.data : null;
                    if (transactions === null) {
                        return false;
                    }
                    this.transactions = transactions;
                    $('ul.tabs').tabs('select_tab', 'transactions-listing');
                }
            }
        });
    </script>
@endsection