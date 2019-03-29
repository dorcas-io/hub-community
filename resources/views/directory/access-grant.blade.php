@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="row" id="access-grant-ui">
        <div class="col s12">
            <ul class="tabs tabs-fixed-width z-depth-1">
                <li class="tab col s3"><a href="#request-access">Request Access</a></li>
                <li class="tab col s3"><a href="#acquired-access">Sent Access Requests</a></li>
            </ul>
            <div id="request-access" class="col s12">
                <div class="row mt-4">
                    <form method="get" action="" v-on:submit.prevent="searchBusinesses">
                        <div class="input-field col s12 m8">
                            <input id="title" type="text" name="title" v-model="search_term" required
                                   v-bind:readonly="searching">
                            <label for="title">Search Business by Name, or Email</label>
                        </div>
                        <div class="input-field col s12 m4">
                            <button type="submit" class="wwaves-effect waves-light btn" name="search_businesses"
                                    value="1" v-bind:class="{'disabled': searching}">Search</button>
                            <div class="progress" v-if="searching">
                                <div class="indeterminate"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row" v-if="businesses.length > 0">
                    <access-grant-company-card class="col s12 m4" v-for="(company, index) in businesses" :key="company.id"
                                               :company="company" :index="index"
                                               v-on:request-modules="showModuleRequestDialog"></access-grant-company-card>
                </div>
            </div>
            <div id="acquired-access" class="col s12">
                <div class="progress" v-if="grants.is_processing">
                    <div class="indeterminate"></div>
                </div>
                <div class="row mt-2" v-if="grants.grants.length > 0">
                    <access-grant-card class="col s12 m4" v-for="(grant, index) in grants.grants" :key="grant.id"
                                               :grant="grant" :index="index"></access-grant-card>
                </div>
                <div class="row" v-else>
                    <div class="col s12">
                        @component('layouts.slots.empty-fullpage')
                            @slot('icon')
                                assistant
                            @endslot
                            To gain access to your clients' modules, you first need to place a request.
                            @slot('buttons')

                            @endslot
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
        @include('directory.modals.request-access')
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#access-grant-ui',
            data: {
                search_term: '',
                searching: false,
                businesses: [],
                available_modules: {!! json_encode($availableModules) !!},
                business: {},
                grants: {
                    grants: [],
                    meta: [],
                    page_number: 1,
                    is_processing: false,
                }
            },
            mounted: function () {
                this.fetchGrants();
            },
            methods: {
                searchBusinesses: function () {
                    let context = this;
                    this.searching = true;
                    axios.get("/xhr/businesses", {
                        params: {search: context.search_term}
                    }).then(function (response) {
                        console.log(response);
                        context.searching = false;
                        if (response.data.total == 0) {
                            return swal("Oops!", 'No matching businesses were found.', "info");
                        } else {
                            context.businesses = response.data.rows;
                        }
                    }).catch(function (error) {
                        let message = '';
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
                        context.searching = false;
                        return swal("Oops!", message, "warning");
                    });
                },
                showModuleRequestDialog: function (index) {
                    let business = this.businesses.length  > 0 && typeof this.businesses[index] !== 'undefined' ?
                        this.businesses[index] : null;
                    if (business === null) {
                        this.business = {};
                        return;
                    }
                    this.business = business;
                    $('#new-access-request').modal('open');
                },
                changePage: function (number) {
                    this.page_number = parseInt(number, 10);
                    this.loadRequests();
                },
                fetchGrants: function () {
                    let context = this;
                    this.grants.is_processing = true;
                    this.grants.grants = [];
                    axios.get("/xhr/access-grants-for-user", {
                        params: {
                            limit: 12,
                            page: context.page_number
                        }
                    }).then(function (response) {
                        console.log(response.data);
                        context.grants.is_processing = false;
                        context.grants.grants = response.data.data;
                        context.grants.meta = response.data.meta;
                    }).catch(function (error) {
                        let message = '';
                        context.grants.is_processing = false;
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
                },
            }
        })
    </script>
@endsection
