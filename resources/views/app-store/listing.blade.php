@extends('layouts.app')
@if (!empty($showAppsButton))
    @section('body_main_content_header_button')
        <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
           href="{{ route('app-store.installed') }}">
            <i class="material-icons hide-on-med-and-up">assignment_turned_in</i>
            <span class="hide-on-small-onl">My Installed Apps</span>
        </a>
    @endsection
@endif
@section('body_main_content_body')
    <div class="container" id="integrations">
        <div class="row">
            <div class="progress" v-if="is_fetching">
                <div class="indeterminate"></div>
            </div>
            <app-store-app class="m4" v-for="(app, index) in apps" :key="app.id" :app="app" :index="index"
                           v-on:install-app="installApp"
                           v-on:uninstall-app="uninstallApp"></app-store-app>

            <ul class="pagination" v-if="apps.length > 0 && !is_fetching && typeof meta.pagination !== 'undefined' && meta.pagination.total_pages > 1">
                <!--TODO: Handle situations where the number of pages > 10; we need to limit the pages displayed in those cases -->
                <li class="waves-effect"><a href="#!" v-on:click.prevent="changePage(1)"><i class="material-icons">chevron_left</i></a></li>
                <li v-for="n in meta.pagination.total_pages" v-bind:class="{active: n === page_number}">
                    <a href="#" v-on:click.prevent="changePage(n)" v-if="n !== page_number">@{{ n }}</a>
                    <a href="#" v-else>@{{ n }}</a>
                </li>
                <li class="waves-effect"><a href="#!" v-on:click.prevent="changePage(meta.pagination.total_pages)"><i class="material-icons">chevron_right</i></a></li>
            </ul>

            <div class="col s12" v-if="apps.length === 0 && !is_fetching">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        device_hub
                    @endslot
                    There are no applications to be shown at the moment. <br>You can check your installed apps section for
                        apps you have installed.
                    @slot('buttons')
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="{{ route('app-store.installed') }}">
                            Installed Apps
                        </a>
                    @endslot
                @endcomponent
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#integrations',
            data: {
                apps: [],
                filter: '{{ $filter or 'all' }}',
                is_fetching: false,
                meta: [],
                page_number: 1,
                limit: 12,
                search_term: '',
                category_slug: '',
                authorization_token: '{{ $authToken or '' }}'
            },
            mounted: function () {
                this.searchAppStore();
            },
            methods: {
                changePage: function (number) {
                    this.page_number = parseInt(number, 10);
                    this.searchAppStore();
                },
                searchAppStore: function (page, limit) {
                    let context = this;
                    if (typeof page !== 'undefined' && !isNaN(page)) {
                        this.page_number = page;
                    }
                    if (typeof limit !== 'undefined' && !isNaN(limit)) {
                        this.limit = limit;
                    }
                    this.is_fetching = true;
                    axios.get("/xhr/app-store", {
                        params: {
                            search: context.search_term,
                            limit: context.limit,
                            page: context.page_number,
                            category_slug: context.category_slug,
                            filter: context.filter
                        }
                    }).then(function (response) {
                        context.is_fetching = false;
                        context.apps = response.data.data;
                        context.meta = response.data.meta;
                    }).catch(function (error) {
                        var message = '';
                        context.is_fetching = false;
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
                installApp: function (index) {
                    let context = this;
                    if (this.is_fetching) {
                        // currently processing something
                        swal('Please Wait', 'Your previous request is still processing...', 'info');
                        return;
                    }
                    let app = typeof this.apps[index] !== 'undefined' ? this.apps[index] : {};
                    if (typeof app.id === 'undefined') {
                        return;
                    }
                    swal({
                        title: "Install Application?",
                        text: "You are about to install an application " + app.name + ". Would you like to continue?",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonText: "Continue.",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        context.is_fetching = true;
                        axios.post("/xhr/app-store/" + app.id).then(function (response) {
                            console.log(response);
                            context.is_fetching = false;
                            //Materialize.toast('Updated the settings for the ' + context.display_name + ' integration.', 4000);
                            Vue.set(context.apps[index], 'is_installed', true);
                            // set the app as installed
                            return swal("Installed!", "The application was successfully installed.", "success");
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
                            context.is_fetching = false;
                            //Materialize.toast("Oops!" + message, 4000);
                            return swal("Install Failed", message, "warning");
                        });
                    });
                },
                uninstallApp: function (index) {
                    let context = this;
                    if (this.is_fetching) {
                        // currently processing something
                        swal('Please Wait', 'Your previous request is still processing...', 'info');
                        return;
                    }
                    let app = typeof this.apps[index] !== 'undefined' ? this.apps[index] : {};
                    if (typeof app.id === 'undefined') {
                        return;
                    }
                    swal({
                        title: "Uninstall Application?",
                        text: "You are about to uninstall application " + app.name + ". This could result in loss of data.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, uninstall it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        context.is_fetching = true;
                        axios.delete("/xhr/app-store/" + app.id)
                            .then(function (response) {
                                console.log(response);
                                context.is_fetching = false;
                                Vue.set(context.apps[index], 'is_installed', false);
                                // set the app as installed
                                return swal("Uninstalled!", "The application was successfully uninstalled.", "success");
                            })
                            .catch(function (error) {
                                context.is_fetching = false;
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
                                return swal("Uninstall Failed", message, "warning");
                            });
                    });
                }
            }
        });
    </script>
@endsection
