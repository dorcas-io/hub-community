@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="online-store-profile">
        <div class="row">
            <div class="col s12">
                <div class="row">
                    <div class="col s12 m4">
                        <div class="card">
                            <div class="card-content {{ empty($subdomain) ? 'red' : 'green' }} darken-3 white-text center-align">
                                <h4 class="card-stats-number">{{ empty($subdomain) ? 'InActive' : 'Active' }}</h4>
                                <p class="card-stats-compare">Store Status</p>
                            </div>
                            <div class="card-action {{ empty($subdomain) ? 'red' : 'green' }} darken-1">
                                <div id="clients-bar" class="center-align"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="card">
                            <div class="card-content blue darken-3 white-text center-align">
                                <h4 class="card-stats-number">{{ $productCount ? number_format($productCount) : 'No Products' }}</h4>
                                <p class="card-stats-compare">Products</p>
                            </div>
                            <div class="card-action blue darken-1">
                                <div id="clients-bar" class="center-align"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="card">
                            <div class="card-content blue darken-3 white-text center-align">
                                <h4 class="card-stats-number">Store Domain</h4>
                                <p class="card-stats-compare">{{ !empty($subdomain) ? $subdomain . '/store' : 'Not Reserved' }}</p>
                            </div>
                            <div class="card-action blue darken-1">
                                <div id="clients-bar" class="center-align"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if (!empty($subdomain))
                        <div class="col s12 m6">
                            <form action="" method="post" class="col s12">
                                {{ csrf_field() }}
                                @component('layouts.slots.empty-fullpage')
                                    @slot('icon')
                                        local_mall
                                    @endslot
                                    <div class="row black-text">
                                        <div class="input-field col s6">
                                            <input id="store_instagram_id" name="store_instagram_id" type="text"
                                                   class="validate" v-model="store_settings.store_instagram_id">
                                            <label for="store_instagram_id">Store Instagram ID</label>
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="store_twitter_id" name="store_twitter_id" type="text"
                                                   class="validate" v-model="store_settings.store_twitter_id">
                                            <label for="store_twitter_id">Store Twitter ID</label>
                                        </div>
                                    </div>
                                    <div class="row black-text">
                                        <div class="input-field col s12">
                                            <input id="store_facebook_page" name="store_facebook_page" type="url"
                                                   class="validate" v-model="store_settings.store_facebook_page">
                                            <label for="store_facebook_page">Facebook Page</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="store_homepage" name="store_homepage" type="url"
                                                   class="validate" v-model="store_settings.store_homepage">
                                            <label for="store_homepage">Homepage URL</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="store_terms_page" name="store_terms_page" type="url"
                                                   class="validate" v-model="store_settings.store_terms_page">
                                            <label for="store_terms_page">Terms of Service URL</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <input id="store_ga_tracking_id" name="store_ga_tracking_id" type="text"
                                                   class="validate" v-model="store_settings.store_ga_tracking_id">
                                            <label for="store_ga_tracking_id" v-bind:class="{'active': typeof store_settings.store_ga_tracking_id !== 'undefined' && store_settings.store_ga_tracking_id.length > 0}">Google Analytics Tracking ID (UA-XXXXXXXXX-X)</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <textarea class="materialize-textarea black-text" id="store_custom_js"
                                                      name="store_custom_js" v-model="store_settings.store_custom_js"></textarea>
                                            <label for="store_custom_js">Custom Javascript (Paste the codes you were given)</label>
                                            <small>This allows you to add popular tools you use to your store site. e.g. Drift, Drip, Intercom, Tawk.to</small>
                                        </div>
                                    </div>
                                    @slot('buttons')
                                        <button type="submit" class="btn-flat blue darken-3 white-text waves-effect waves-light">
                                            Save Settings
                                        </button>
                                    @endslot
                                @endcomponent
                            </form>
                        </div>
                    @endif
                    <div class="col s12 m6">
                        @component('layouts.slots.empty-fullpage')
                            @slot('icon')
                                credit_card
                            @endslot
                            To integrate online payment for your store, you need to integrate one of our payment partners.<br>
                            You need to create a vendor account, and install the appropriate integration from the "Integration" section.
                            @slot('buttons')
                                <a class="btn-flat grey darken-3 white-text waves-effect waves-light"
                                   href="https://dorcas.ravepay.co/auth/" target="_blank">
                                    Create Vendor Account
                                </a>
                                &nbsp;
                                <a class="btn-flat blue darken-3 white-text waves-effect waves-light"
                                   href="{{ route('integrations.install') }}">
                                    Add Integration
                                </a>
                            @endslot
                        @endcomponent
                    </div>
                    @if (empty($subdomain))
                        <div class="col s12 m6">
                            @component('layouts.slots.empty-fullpage')
                                @slot('icon')
                                    public
                                @endslot
                                Get your custom [business].dorcas.ng sub-domain to enable your store.
                                @slot('buttons')
                                    <a class="btn-flat blue darken-3 white-text waves-effect waves-light"
                                       href="{{ route('apps.ecommerce.domains') }}">
                                        Reserve SubDomain
                                    </a>
                                @endslot
                            @endcomponent
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('help_modal_content')
    @component('layouts.slots.video-embed')
        //www.youtube.com/embed/wlZfFi9FsOk?rel=0
    @endcomponent
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#online-store-profile',
            data: {
                store_owner: {!! json_encode($business) !!},
                store_settings: {!! json_encode($storeSettings) !!}
            }
        });
    </script>
@endsection