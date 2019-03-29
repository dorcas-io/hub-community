@extends('layouts.app')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ cdn('vendors/morris-chart/morris.css') }}" >
@endsection
@section('body_main_content_header')@endsection
@section('body_main_content_body')
    <div class="container hopscotch-tour-box" data-tour-name="dashboard" id="dashboard">
        <div class="row" v-if="!user.is_verified">
            <div class="col s12">
                @component('layouts.slots.alert-with-buttons')
                    @slot('title')
                        Account Verification Pending
                    @endslot
                    Your account has not yet been verified, would you like to do that now?
                    @slot('buttons')
                        <a class="waves-effect waves-light btn white-text" href="#"
                           v-on:click.prevent="resendVerification">Resend Email</a>
                    @endslot
                @endcomponent
            </div>
        </div>
        <div id="card-stats">
            <div class="row">
                @foreach ($summary as $figures)
                    <div class="col s12 m6 l3">
                        <div class="card hoverable gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text">
                            <div class="padding-4">
                                <div class="col s7 m7">
                                    <i class="material-icons background-round mt-5">{{ $figures['icon'] }}</i>
                                    <p>{{ title_case($figures['name']) }}</p>
                                </div>
                                <div class="col s5 m5 right-align">
                                    <h5 class="mb-0">{{ $figures['count_formatted'] }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="row" id="dashboard-header">
            <div class="col s12" v-if="verifying">
                <div class="progress">
                    <div class="indeterminate"></div>
                </div>
            </div>
            <div class="col s12">
                <h4>{{ \Carbon\Carbon::now()->format('l d, F') }}</h4>
                <p class="flow-text">Good @{{ greeting }}, {{ \Illuminate\Support\Facades\Auth::user()->firstname }}</p>
            </div>
            <div class="col s12">
                <ul class="tabs tabs-fixed-width z-depth-1">
                    <li class="tab col s6"><a href="#orders-graph">Orders</a></li>
                    <li class="tab col s6 disabled"><a href="#coming-soon-graph">Coming Soon</a></li>
                </ul>
                <div id="orders-graph" class="col s12">
                    <div id="revenue-chart" class="card">
                        <div class="card-content">
                            <h4 class="header mt-0">ORDERS
                                <span class="small text-darken-1 ml-1">
                                FOR LAST {{ config('hub.dashboard.graph.days_ago') }} DAYS
                            </span>
                                <a class="waves-effect waves-light btn right" href="{{ route('apps.invoicing.orders') }}" id="goto-orders">More Info</a>
                            </h4>
                            <div class="row">
                                <div class="col s12">
                                    <div id="line-chart" class="graph" style="height: 350px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="coming-soon-graph" class="col s12">
                    Coming Soon...
                </div>
            </div>
        </div>
        @if (!isset($showUiModalAccessMenu) || !empty($showUiModalAccessMenu))
            @include('setup.wizard')
        @endif
    </div>
@endsection
@section('body_js')
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script type="text/javascript" src="{{ cdn('vendors/raphael/raphael-min.js') }}"></script>
    <script type="text/javascript" src="{{ cdn('vendors/morris-chart/morris.min.js') }}"></script>
    <script type="text/javascript">
        function open_configuration_modal() {
            $('#setup-modal').modal('open');
            return false;
        }

        new Vue({
            el: '#dashboard',
            data: {
                message: '{{ $message }}',
                verifying: false,
                user: {!! json_encode($dorcasUser) !!},
                business: {!! json_encode($business) !!},
                subscription: {!! json_encode(!empty($plan) ? $plan : []) !!},
                businessConfiguration: []
            },
            computed: {
                greeting: function () {
                    var hourOfDay = parseInt(moment().format('HH'), 10);
                    if (hourOfDay >= 0 && hourOfDay < 12) {
                        return 'morning';
                    } else if (hourOfDay >= 12 && hourOfDay <= 16) {
                        return 'afternoon';
                    }
                    return 'evening';
                }
            },
            mounted: function () {
                if (this.message !== null && this.message.length > 0) {
                    Materialize.toast(this.message, 4000);
                }
                if (typeof this.subscription.price !== 'undefined' && this.subscription.price > 0) {
                    this.showPaystackDialog();
                }
                if (typeof this.business.extra_data !== 'undefined' && this.business.extra_data !== null) {
                    this.businessConfiguration = this.business.extra_data;
                }
            },
            methods: {
                resendVerification: function () {
                    var context = this;
                    this.verifying = true;
                    axios.post("/xhr/account/resend-verification")
                        .then(function (response) {
                            context.verifying = false;
                            swal('Email Sent', 'A email was just sent to your address. Kindly follow the instructions in it.', 'success');
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
                            context.verifying = false;
                            swal("Oops!", message, "danger");
                        });
                },
                showPaystackDialog: function () {
                    var context = this;
                    var handler = PaystackPop.setup({
                        key: '{{ config('services.paystack.public_key') }}',
                        email: this.user.email,
                        amount: this.subscription.price * 100,
                        channels: ['card'],
                        metadata: {
                            custom_fields: [
                                {
                                    display_name: "Mobile Number",
                                    variable_name: "mobile_number",
                                    value: this.user.phone
                                },
                                {
                                    display_name: "Business",
                                    variable_name: "business",
                                    value: this.business.name
                                },
                                {
                                    display_name: "Plan",
                                    variable_name: "plan",
                                    value: this.business.plan.data.name
                                },
                                {
                                    display_name: "Plan Type",
                                    variable_name: "plan_type",
                                    value: this.business.plan_type
                                }
                            ]
                        },
                        callback: context.verifyTransaction,
                        onClose: function() {

                        }
                    });
                    handler.openIframe();
                },
                verifyTransaction: function (response) {
                    console.log(response);
                    var context = this;
                    this.verifying = true;
                    axios.post("/xhr/billing/verify", {
                        reference: response.reference,
                        channel: 'paystack'
                    }).then(function (response) {
                        context.verifying = false;
                        window.location = "/home";
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
                        context.verifying = false;
                        swal("Oops!", message, "danger");
                    });
                }
            }
        });

        $(function() {
            Morris.Line({
                element: 'line-chart',
                data: {!! json_encode($salesGraph) !!},
                xkey: 'date',
                ykeys: ['count', 'total'],
                labels: ['Orders', 'Total Value'],
                parseTime:false,
                lineColors: ['#0C99D3', '#9C2E9D'],
                hideHover: false,
                preUnits: ''
            });
            @if (empty($isConfigured))
                open_configuration_modal();
            @endif
        });
        // Start the tour!
        if (document.cookie.replace(/(?:(?:^|.*;\s*)showedNavTour\s*\=\s*([^;]*).*$)|^.*$/, "$1") !== "true") {
            showTour();
            document.cookie = "showedNavTour=true; expires=Fri, 31 Dec 9999 23:59:59 GMT";
        }
    </script>
@endsection
