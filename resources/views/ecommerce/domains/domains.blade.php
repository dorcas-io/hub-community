@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="domains">
        <div class="section row">
            <div class="col s12">
                <ul class="tabs tabs-fixed-width z-depth-1">
                    <li class="tab col s6"><a href="#custom-subdomain">Dorcas.ng Domain</a></li>
                    <li class="tab col s6 {{ $isOnPaidPlan ? '' : 'disabled' }}"><a href="#buy-domains">Buy a Domain</a></li>
                </ul>
                <div id="custom-subdomain" class="col s12">
                    <div class="row section">
                        <div class="col s12 m4" v-for="(subdomain, index) in domains" :key="subdomain.id">
                            <div class="card darken-1 hoverable">
                                <div class="card-content">
                                    <p class="flow-text">
                                        https://@{{ subdomain.prefix }}.@{{ subdomain.domain.data.domain }}
                                    </p>
                                </div>
                                <div class="card-action">
                                    <a class="red-text right" href="#" v-on:click.prevent="releaseDomain(index)">Release</a>
                                    <a target="_blank" v-bind:href="'https://' + subdomain.prefix + '.' + subdomain.domain.data.domain">Visit</a>
                                </div>
                            </div>
                        </div>
                        <div class="col s12" v-if="domains.length === 0">
                            @component('layouts.slots.empty-fullpage')
                                @slot('icon')
                                    public
                                @endslot
                                Get your custom [business].dorcas.ng sub-domain.
                                @slot('buttons')
                                    <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger"
                                       href="#get-dorcas-ng-domain">
                                        Reserve SubDomain
                                    </a>
                                @endslot
                            @endcomponent
                        </div>
                    </div>
                </div>
                <div id="buy-domains" class="col s12" v-bind:class="{'disabled': is_on_paid_plan}">
                    <div class="row section">
                        <div class="col s12 m4" v-for="(domain, index) in domains" :key="domain.id">
                            <div class="card darken-1 hoverable">
                                <div class="card-content">
                                    <p class="flow-text">
                                        @{{ domain.domain }}
                                    </p>
                                </div>
                                <div class="card-action">
                                    <a target="_blank" v-bind:href="'http://www.' + domain.domain">Visit</a>
                                    <a class="right red-text" href="#" v-on:click.prevent="removeDomain(index)">Remove</a>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m8" v-if="domains.length > 0">
                            @component('layouts.slots.empty-fullpage')
                                @slot('icon')
                                    settings_input_antenna
                                @endslot
                                @if (!$isHostingSetup)
                                    To complete your domain configuration, you'll need to take the following action:
                                @elseif (!empty($nameservers))
                                    If you added a domain you already own, you will need to set the following NameServer
                                        entries in your domain's DNS.<br>
                                    @foreach ($nameservers as $ns)
                                        <span>{{ $ns }}</span><br>
                                    @endforeach
                                    <br>
                                    If you bought a domain here, you can simply ignore the above instruction since it
                                    has already been done for you.
                                @endif
                                @slot('buttons')
                                    @if (!$isHostingSetup && !empty($domains) && $domains->count() > 0)
                                        <form action="" method="post">
                                            {{ csrf_field() }}
                                            <div class="progress" v-if="is_setting_up_hosting">
                                                <div class="indeterminate"></div>
                                            </div>
                                            <input type="hidden" name="domain" value="{{ $domains->first()->domain }}" />
                                            <button v-if="!is_setting_up_hosting" class="btn-flat grey darken-3 white-text waves-effect waves-light"
                                                    name="setup_hosting" value="setup_hosting" v-on:click="setRequestingHosting">
                                                Setup Hosting
                                            </button>
                                        </form>
                                    @endif
                                @endslot
                            @endcomponent
                        </div>
                        <div class="col s12" v-if="domains.length === 0">
                            @component('layouts.slots.empty-fullpage')
                                @slot('icon')
                                    public
                                @endslot
                                {{ !empty($claimComNgDomain) ? 'Reserve your free .com.ng domain.' : 'Purchase a new domain.' }}
                                @slot('buttons')
                                    <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger"
                                       href="#buy-domain-modal">
                                        Buy Domain
                                    </a>
                                    <a class="btn-flat grey darken-3 white-text waves-effect waves-light modal-trigger"
                                       href="#add-domain-modal">
                                        Add a Domain you Own
                                    </a>
                                @endslot
                            @endcomponent
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('ecommerce.modals.get-dorcas-ng-domain')
    @include('ecommerce.modals.buy-domain')
    @include('ecommerce.modals.add-domain')
@endsection
@section('help_modal_content')
    @component('layouts.slots.video-embed')
        //www.youtube.com/embed/wlZfFi9FsOk?rel=0
    @endcomponent
@endsection
@section('body_js')
    <script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}"></script>
    <script type="text/javascript">
        new Vue({
            el: '#get-dorcas-ng-domain',
            data: {
                domain: '',
                is_available: false,
                is_queried: false,
                is_querying: false,
            },
            computed: {
                actual_domain: function () {
                    return this.domain.replace(' ', '').toLowerCase().trim();
                }
            },
            methods: {
                removeStatus: function () {
                    this.is_available = false;
                    this.is_queried = false;
                    this.is_querying = false
                },
                checkAvailability: function () {
                    var context = this;
                    this.is_querying =  true;
                    axios.get("/xhr/ecommerce/domains/issuances/availability", {
                        params: {id: context.actual_domain}
                    }).then(function (response) {
                        console.log(response);
                        context.is_querying = false;
                        context.is_queried = true;
                        context.is_available = response.data.is_available;
                        Materialize.toast(context.is_available ? 'The subdomain is available' : 'The subdomain is unavailable', 4000);
                    })
                        .catch(function (error) {
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
                            context.is_querying = false;
                            Materialize.toast('Error: '+message, 4000);
                        });
                }
            }
        });
        new Vue({
            el: '#custom-subdomain',
            data: {
                domains: {!! json_encode($subdomains) !!}
            },
            methods: {
                releaseDomain: function (index) {
                    var subdomain = this.domains[index];
                    console.log(subdomain, index);
                    var context = this;
                    var name = subdomain.prefix + '.' + subdomain.domain.data.domain;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to release the subdomain " + name,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, release it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/ecommerce/domains/issuances/" + subdomain.id)
                            .then(function (response) {
                                console.log(response);
                                context.domains.splice(index, 1);
                                return swal("Released!", "The domain was successfully released.", "success");
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
                                return swal("Release Failed", message, "warning");
                            });
                    });
                }
            }
        });

        new Vue({
            el: '#buy-domains',
            data: {
                domains: {!! json_encode(!empty($domains) ? $domains : []) !!},
                is_setting_up_hosting: false,
                is_on_paid_plan: {!! json_encode($isOnPaidPlan) !!}
            },
            methods: {
                removeDomain: function (index) {
                    let context = this;
                    let domain = this.domains[index] !== 'undefined' ? this.domains[index] : {};
                    if (typeof domain.id === 'undefined') {
                        return;
                    }
                    swal({
                        title: "Remove Domain?",
                        text: "You are about to remove domain " + domain.domain + ", and associated hosting from your "+
                            "account. For domains purchased via your account, this does not mean the domain will be " +
                            "deleted from the registrar.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/ecommerce/domains/" + domain.id)
                            .then(function (response) {
                                console.log(response);
                                context.domains.splice(index, 1);
                                return swal("Deleted!", "The domain was successfully deleted from your account.", "success");
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
                setRequestingHosting: function () {
                    this.is_setting_up_hosting = true;
                },
            }
        });

        new Vue({
            el: '#buy-domain-modal',
            data: {
                domain: '',
                extension: 'com',
                is_available: false,
                is_queried: false,
                is_querying: false
            },
            computed: {
                actual_domain: function () {
                    return this.domain.replace(' ', '').toLowerCase().trim() + '.' + this.extension;
                }
            },
            watch: {
                extension: function (oldExt, newExt) {
                    if (oldExt !== newExt) {
                        this.is_available = false;
                    }
                }
            },
            methods: {
                checkAvailability: function () {
                    var context = this;
                    this.is_querying =  true;
                    axios.get("/xhr/ecommerce/domains/availability", {
                        params: {domain: context.domain, extension: context.extension}
                    }).then(function (response) {
                        console.log(response);
                        context.is_querying = false;
                        context.is_queried = true;
                        context.is_available = response.data.is_available;
                        Materialize.toast(context.is_available ? 'The domain is available' : 'The domain is not available', 4000);
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
                            context.is_querying = false;
                            Materialize.toast('Error: '+message, 4000);
                        });
                },
                removeStatus: function () {
                    this.is_available = false;
                    this.is_queried = false;
                    this.is_querying = false
                },
            }
        });
    </script>
@endsection