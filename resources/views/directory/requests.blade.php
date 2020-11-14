@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.ui-response-alert')
    <div class="row" id="service-requests">
        <div class="col s12">
            <div class="progress" v-if="is_processing">
                <div class="indeterminate"></div>
            </div>
            <professional-service-request class="m6" v-for="(request, index) in requests" :key="request.id"
                                          :index="index" :request="request"
                                          v-on:request-marked="marked"></professional-service-request>
        </div>
        <div class="col s12" v-if="requests.length > 0 && typeof meta.pagination !== 'undefined' && meta.pagination.total_pages >= 1">
            <ul class="pagination">
                <li>
                    <a href="#" v-on:click.prevent="changePage(1)">
                        <i class="material-icons">chevron_left</i>
                    </a>
                </li>
                <li v-for="n in meta.pagination.total_pages" v-bind:class="{active: n === page_number}">
                    <a href="#" v-on:click.prevent="changePage(n)" v-if="n !== page_number">@{{ n }}</a>
                    <a href="#!" v-else>@{{ n }}</a>
                </li>
                <li>
                    <a href="#" v-on:click.prevent="changePage(meta.pagination.total_pages)">
                        <i class="material-icons">chevron_right</i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="col s12" v-if="requests.length === 0 && !is_processing">
            @component('layouts.slots.empty-fullpage')
                @slot('icon')
                    inbox
                @endslot
                There are no service requests in your inbox at the moment.
                @slot('buttons')@endslot
            @endcomponent
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#service-requests',
            data: {
                requests: [],
                meta: [],
                page_number: 1,
                is_processing: false,
            },
            mounted: function () {
                this.loadRequests();
            },
            methods: {
                changePage: function (number) {
                    this.page_number = parseInt(number, 10);
                    this.loadRequests();
                },
                loadRequests: function () {
                    var context = this;
                    this.is_processing = true;
                    this.requests = [];
                    axios.get("/xhr/directory/service-requests", {
                        params: {
                            limit: 12,
                            page: context.page_number
                        }
                    }).then(function (response) {
                        console.log(response.data);
                        context.is_processing = false;
                        context.requests = response.data.data;
                        context.meta = response.data.meta;
                    }).catch(function (error) {
                        var message = '';
                        context.is_processing = false;
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
                marked: function (index, request) {
                    this.requests.splice(index, 1, request);
                }
            }
        });
    </script>
@endsection