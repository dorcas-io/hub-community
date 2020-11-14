@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn dropdown-settings waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#!" data-activates="dropdown1" style="">
        <i class="material-icons hide-on-med-and-up">grid_on</i>
        <span class="hide-on-small-onl">View</span>
        <i class="material-icons right">arrow_drop_down</i>
    </a>
    <ul id="dropdown1" class="dropdown-content">
        <li>
            <a href="{{ empty($vendorMode) ? route('directory') : route('directory.vendors') }}?view=listing" class="grey-text text-darken-2">By Listing</a>
        </li>
        <li>
            <a href="{{ empty($vendorMode) ? route('directory') : route('directory.vendors') }}?view=categories" class="grey-text text-darken-2">By Category</a>
        </li>
    </ul>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section" id="directory">
            @if (!empty($view) && $view === 'categories')
                <div class="col s12 m3" v-for="category in categories" :key="category.id">
                    <div class="card">
                        <div class="card-content black-text">
                            <span class="card-title">@{{ category.name.title_case() }}</span>
                            <h4 class="center">@{{ category.counts.services }}</h4>
                        </div>
                        <div class="card-action">
                            <a class="black-text" v-bind:href="'/directory' + (isVendorMode ? '/vendors' : '') + '?category_id=' + category.id">View Services</a>
                        </div>
                    </div>
                </div>
            @else
                <ul class="tabs tabs-fixed-width z-depth-1">
                    <li class="tab col s6"><a href="#directory-listing">Directory</a></li>
                    <li class="tab col s6 disabled"><a href="#directory-contacts">Your Contacts</a></li>
                </ul>
                <div id="directory-listing" class="col s12">
                    <table class="bootstrap-table responsive-table"
                           data-url="{{ url('/xhr/directory') }}?{{ http_build_query($query) }}"
                           data-page-list="[10,25,50,100,200,300,500]"
                           data-row-attributes="Hub.Table.formatDirectory"
                           data-side-pagination="server"
                           data-show-refresh="true"
                           data-sort-class="sortable"
                           data-pagination="true"
                           data-search="true"
                           data-unique-id="id"
                           data-search-on-enter-key="true"
                           id="directory-listing">
                        <thead>
                        <tr>
                            <th data-field="title" data-width="25%">Service</th>
                            <th data-field="provider" data-width="15%">Provider</th>
                            <th data-field="provider_verified" data-width="10%">Verified</th>
                            <th data-field="cost_type" data-width="10%">Type</th>
                            <th data-field="cost" data-width="15%">Cost</th>
                            <th data-field="category_list" data-width="10%">Categories</th>
                            <th data-field="buttons" data-width="15%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div id="directory-contacts" class="col s12">
                    <div class="col s12 m6" v-for="(contact, index) in contacts" :key="contact.id">
                        <div class="card">
                            <div class="card-content">
                                <span class="card-title">@{{ typeof contact.contactable !== null ? contact.contactable.data.firstname + ' ' + contact.contactable.data.lastname : contact.firstname + ' ' + contact.lastname }}</span>
                                <h5>@{{ typeof contact.contactable !== null ? contact.contactable.data.email : contact.email }}</h5>
                                <p class="flow-text">@{{ typeof contact.contactable !== null ? contact.contactable.data.phone : contact.phone }}</p>
                            </div>
                            <div class="card-action">
                                <a class="activator black-text" href="#" style="margin-right: 12px;"
                                   v-if="typeof contact.contactable !== 'undefined' && typeof contact.contactable.data.professional_services !== 'undefined' && contact.contactable.data.professional_services.data.length  > 0">Services</a>
                                <a class="black-text" v-bind:href="'{{ route('directory.vendors') }}/' + contact.id" style="margin-right: 12px;">Send Payment</a>
                                <a class="red-text right" href="#" v-on:click.prevent="deleteContact(index)" style="margin-right: 12px;">Remove</a>
                            </div>
                            <div class="card-reveal" v-if="typeof contact.contactable !== 'undefined' && typeof contact.contactable.data.professional_services !== 'undefined' && contact.contactable.data.professional_services.data.length  > 0">
                                <span class="card-title grey-text text-darken-4">Services<i class="material-icons right">close</i></span>
                                <ul class="">
                                    <li v-for="service in contact.contactable.data.professional_services.data" :key="service.id">
                                        @{{ service.title }} @ @{{ service.cost_currency + service.cost_amount.formatted + (service.cost_frequency === 'standard' ? '' : '/' + service.cost_frequency) }} &nbsp; <a v-bind:href="'/directory/' + service.id">View</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col s12" v-if="typeof meta.pagination !== 'undefined' && meta.pagination.total_pages > 1">
                        <!--TODO: Handle situations where the number of pages > 10; we need to limit the pages displayed in those cases -->
                        <ul class="pagination">
                            <li class="waves-effect"><a href="#!" v-on:click.prevent="changePage(1)"><i class="material-icons">chevron_left</i></a></li>
                            <li v-for="n in meta.pagination.total_pages" v-bind:class="{active: n === page_number, 'waves-effect': n !== page_number}">
                                <a href="#!" v-on:click.prevent="changePage(n)">@{{ n }}</a>
                            </li>
                            <li class="waves-effect"><a href="#!" v-on:click.prevent="changePage(meta.pagination.total_pages)"><i class="material-icons">chevron_right</i></a></li>
                        </ul>
                    </div>
                    <div class="progress" v-if="is_fetching">
                        <div class="indeterminate"></div>
                    </div>
                    <div class="col s12" v-if="contacts.length === 0">
                        @component('layouts.slots.empty-fullpage')
                            @slot('icon')
                                book
                            @endslot
                            Your contacts.
                            @slot('buttons')
                            @endslot
                        @endcomponent
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}"></script>
    <script type="text/javascript">
        new Vue({
            el: '#directory',
            data: {
                is_loading: true,
                categories: {!! json_encode(!empty($categories) ? $categories : []) !!},
                isVendorMode: {!! json_encode(!empty($vendorMode)) !!},
                contacts: [],
                is_fetching: false,
                search_term: '',
                page_number: 1,
                meta: []
            },
            methods: {
                changePage: function (number) {
                    this.page_number = parseInt(number, 10);
                    this.searchContacts();
                },
                deleteContact: function (index) {
                    var contact = typeof this.contacts[index] !== 'undefined' ? this.contacts[index] : null;
                    if (contact === null) {
                        return;
                    }
                    var context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to remove this contact.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, continue!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/directory/contacts/" + contact.id)
                            .then(function (response) {
                                console.log(response);
                                context.contacts.splice(index, 1);
                                return swal("Deleted!", "The contact was successfully removed.", "success");
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
                searchContacts: function () {
                    var context = this;
                    this.is_fetching = true;
                    this.products = [];
                    axios.get("/xhr/directory/contacts", {
                        params: {
                            search: context.search_term,
                            limit: 12,
                            page: context.page_number
                        }
                    }).then(function (response) {
                        context.is_fetching = false;
                        context.contacts = response.data.rows;
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
                }
            },
            mounted: function () {
                this.searchContacts();
            }
        });
    </script>
@endsection