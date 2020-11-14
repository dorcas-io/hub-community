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
                            <a class="black-text" :href="'/directory' + (isVendorMode ? '/vendors' : '') + '?category_id=' + category.id">View Services</a>
                        </div>
                    </div>
                </div>
            @else
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
                isVendorMode: {!! json_encode(!empty($vendorMode)) !!}
            },
            methods: {

            }
        });
    </script>
@endsection