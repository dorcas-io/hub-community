<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @section('head_meta')
        <meta name="description" content="">
        <meta name="keywords" content="" />
        <meta name="author" content="" />
    @show
    <!-- <title>@section('head_title'){{ !empty($page['title']) ? $page['title'] : 'We\'re Sorry' }} | {{ !empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : config('app.name') }}@show</title> -->
    <title>@section('head_title'){{ !empty($page['title']) ? $page['title'] : 'We\'re Sorry' }} | {{ !empty($page['login_product_name']) ? $page['login_product_name'] : '' }}@show</title>
    <!-- Favicons-->
    @include('layouts.blocks.favicons')
    <!-- Favicons-->
    <!-- CORE CSS-->
    <link href="{{ cdn('css/themes/collapsible-menu/materialize.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ cdn('css/themes/collapsible-menu/style.css') }}" type="text/css" rel="stylesheet">
    <!-- Custom CSS-->
    <link href="{{ cdn('css/custom/custom.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ cdn('css/hopscotch.css') }}" type="text/css" rel="stylesheet">
    <!-- INCLUDED PLUGIN CSS ON THIS PAGE -->
    <link href="{{ cdn('vendors/prism/prism.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ cdn('vendors/perfect-scrollbar/perfect-scrollbar.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ cdn('vendors/sweetalert/dist/sweetalert.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ cdn('vendors/flag-icon/css/flag-icon.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ cdn('vendors/bootstrap-table/bootstrap-table.css') }}" type="text/css" rel="stylesheet">
    <style type="text/css">
        select.browser-default {
            display: block !important;
            pointer-events: auto !important;
            width: 100% !important;
            height: 3rem !important;
            opacity: 1 !important;
            margin: 0 0 20px 0;
            font-size: 1rem !important;
            line-height: 1.5 !important;
        }
    </style>
    @yield('head_css')
</head>
<body @yield('body_class')>
<!-- Start Page Loading -->
<div id="loader-wrapper">
    <div id="loader"></div>
    <div class="loader-section section-left"></div>
    <div class="loader-section section-right"></div>
</div>
<!-- End Page Loading -->
@section('body')
    @section('header')
        @include('layouts.blocks.header')
    @show
    @section('body_main')
        <!-- START MAIN -->
        <div id="main">
            <!-- START WRAPPER -->
            <div class="wrapper">
                @section('body_main_nav')
                    @include('layouts.blocks.nav')
                @show
                <!-- START CONTENT -->
                @section('body_main_content')
                    <section id="content">
                        @section('body_main_content_header')
                            <!--breadcrumbs start-->
                            <div id="breadcrumbs-wrapper">
                                <!-- Search for small screen -->
                                @include('layouts.blocks.search-bar-mobile')
                                <!-- end search -->
                                <div class="container">
                                    <div class="row">
                                        <div class="col s10 m6 l6">
                                            <h5 class="breadcrumbs-title">{{ $page['header']['title'] or '' }}</h5>
                                            @include('layouts.blocks.breadcrumbs')
                                        </div>
                                        <div class="col s2 m6 l6" id="header-button">
                                            @section('body_main_content_header_button')
                                                @if (\Illuminate\Support\Facades\Auth::check() && empty($partner))
                                                    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow modal-trigger"
                                                       href="#help-modal">
                                                        <i class="material-icons hide-on-med-and-up">help_outline</i>
                                                        <span class="hide-on-small-onl">Help</span>
                                                    </a>
                                                @endif
                                            @show
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--breadcrumbs end-->
                        @show
                        @yield('body_main_content_body')
                        {{--
                        @section('body_main_content_FAB')
                            @include('layouts.blocks.FAB')
                        @show
                        --}}
                    </section>
                @show
                <!-- END CONTENT -->
                @section('body_main_nav2')
                    @include('layouts.blocks.nav-right')
                @show
            </div>
            <!-- END WRAPPER -->
        </div>
        <!-- END MAIN -->
    @show
    @section('footer')
        @include('layouts.blocks.footer')
    @show
@show
@include('layouts.modals.help')
<!-- jQuery Library -->
<script type="text/javascript" src="{{ cdn('vendors/jquery-3.2.1.min.js') }}"></script>
<!-- axios Library -->
<script type="text/javascript" src="{{ cdn('js/axios.min.js') }}"></script>
<!-- Vue.js Library -->
<script type="text/javascript" src="{{ cdn('js/vue.js') }}"></script>
<!-- Moment.js -->
<script type="text/javascript" src="{{ cdn('js/moment.js') }}"></script>
<!--materialize js-->
<script type="text/javascript" src="{{ cdn('js/materialize.min.js') }}"></script>
<!-- Tooltip -->
<script type="text/javascript" src="{{ cdn('vendors/prism/prism.js') }}"></script>
<!--scrollbar-->
<script type="text/javascript" src="{{ cdn('vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script type="text/javascript" src="{{ cdn('vendors/sweetalert/dist/sweetalert.min.js') }}"></script>
<!--plugins.js - Some Specific JS codes for Plugin Settings-->
<script type="text/javascript" src="{{ cdn('js/plugins.js') }}"></script>
<!--custom-script.js - Add your own theme custom JS-->
<script type="text/javascript" src="{{ cdn('js/custom-script.js') }}"></script>
<script type="text/javascript" src="{{ cdn('js/hopscotch.js') }}"></script>

@php
$fc_hubPartner = !empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : config('app.name');
$fc_hubPackage = !empty($business) ? title_case($business->plan['data']['name']) : 'Unavailable';
$fc_businessName = !empty($business) ? title_case($business->name) : 'Unavailable';
$fc_businessType = !empty($business->plan['extra_data']['business_type']) ? title_case($business->plan['extra_data']['business_type']) : 'Unavailable';
@endphp

@if (app()->environment() === 'production')
    @include('layouts.blocks.production-js')
@endif
<script type="text/javascript">
    $(function () {
        $('[data-tooltip]').tooltip({delay: 50, position:'top'});
        $('.modal').not('non-dismissible').modal({dismissible: true});
        $('.modal.non-dismissible').modal({dismissible: false});
        $('.date-picker').pickadate({
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 15, // Creates a dropdown of 15 years to control year,
            today: 'Today',
            clear: 'Clear',
            close: 'Done',
            closeOnSelect: true, // Close upon selecting a date,
            container: undefined, // ex. 'body' will append picker to body
            format: 'd/mm/yyyy'
        });
        var $tables = $('table.bootstrap-table');
        if ($tables.length > 0) {
            $tables.bootstrapTable({
                classes: 'bordered',
                pagination: $(this).data('pagination') || true,
                search: $(this).data('search') || true
            }).on('page-change.bs.table', function () {
                $('.bootstrap-table .btn[data-activates]').dropdown();
            }).on('load-success.bs.table', function () {
                $('.bootstrap-table .btn[data-activates]').dropdown();
            });
            $('.bootstrap-table .btn[data-activates]').dropdown();
        }
        Hub.productName = '{{ !empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : config('app.name') }}';
        window.productName = '{{ !empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : config('app.name') }}';


    });

    @if (!empty($uiToast))
    let message = {!! json_encode($uiToast) !!};
    Materialize.toast(message.html, message.displayLength);
    @endif

    @if (\Illuminate\Support\Facades\Auth::check())
        var sidebarVue = new Vue({
                el: '#right-sidebar-nav',
                data: {
                    user: {!! json_encode(!empty($dorcasUser) ? $dorcasUser : []) !!}
                }
            });

        new Vue({
            el: '#header',
            data: {
                pageMode: '{{ !empty($pageMode) ? $pageMode : 'default' }}',
                viewMode: '{{ empty($viewMode) ? 'business' : $viewMode }}',
                showUiModalAccessMenu: {!! json_encode(isset($showUiModalAccessMenu) ? $showUiModalAccessMenu : true) !!},
                user: sidebarVue.user
            }
        });

        new Vue({
            el: '#left-sidebar-nav',
            data: {
                pageMode: '{{ !empty($pageMode) ? $pageMode : 'default' }}',
                enabledUis: {!! json_encode(!empty($UiConfiguration) ? $UiConfiguration : []) !!},
                UiUsesGrants: {!! json_encode(!empty($UiUsesGrant)) !!}
            }
        });
    @endif

    /**
     * Shows a tour for the page
     */
    function showTour() {
        // shows the tour for a page
        var $tourContainer = $('.hopscotch-tour-box');
        // get the tour container, if any
        var extendedSteps = [];
        // extended steps for this tour
        var tourName = $tourContainer.data('tour-name');
        if (typeof tourName !== 'undefined' && typeof Hub.tours[tourName] !== 'undefined' && typeof Hub.tours[tourName] === 'object') {
            // just want to guarantee it's an array
            var tour = Hub.tours[tourName];
            extendedSteps = typeof tour.steps === 'object' && tour.steps.hasOwnProperty('length') ? tour.steps : [];
        }
        var customTour = {
            id: "custom-tour-" + Hub.utilities.randomId(6),
            i18n: {
                stepNums : ["1"]
            },
            steps: Hub.tours.navigation.steps.concat(extendedSteps)
        };
        hopscotch.startTour(customTour);
    }
</script>

@yield('body_js')
</body>
</html>