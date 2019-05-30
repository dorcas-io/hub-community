<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="en" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @section('head_meta')
        <meta name="description" content="">
        <meta name="keywords" content="" />
        <meta name="author" content="" />
    @show
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#4188c9">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <link rel="icon" href="{{ cdn('favicon.ico') }}" type="image/x-icon"/>
    <link rel="shortcut icon" type="image/x-icon" href="{{ cdn('favicon.ico')  }}" />
    <!-- Generated: 2018-04-16 09:29:05 +0200 -->
    <title>@section('head_title'){{ !empty($page['title']) ? $page['title'] : 'Tabler' }} | {{ config('app.name') }}@show</title>
    @include('layouts.blocks.tabler.favicons')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,500,500i,600,600i,700,700i&amp;subset=latin-ext">
    <!-- Dashboard Core -->
    <link href="{{ cdn('apps/tabler/css/dashboard.css') }}" rel="stylesheet" />
    <!-- c3.js Charts Plugin -->
    <link href="{{ cdn('apps/tabler/plugins/charts-c3/plugin.css') }}" rel="stylesheet" />
    <link href="{{ cdn('apps/tabler/plugins/iconfonts/plugin.css') }}" rel="stylesheet" />
    <link href="{{ cdn('apps/tabler/plugins/prismjs/plugin.css') }}" rel="stylesheet" />
    <link href="{{ cdn('apps/tabler/css/bootstrap-table.min.css') }}" rel="stylesheet" />
    <style type="text/css">
        .combodate {
            display: block;
            width: 100%;
        }
        .combodate .form-control {
            display: inline-block;
        }
        /*.dropdown>.dropdown-menu {
            transition: 0s !important;
            transition-delay: 1s !important;
        }
        .dropdown:hover>.dropdown-menu {
            display: block !important;
            transition-delay: 0s !important;
        }*/
    </style>
    @yield('head_css')
    @yield('head_js')
</head>
<body @section('body_class') class="" @show>
<div class="page" id="tabler-page">
    @section('body')
        <div class="page-main">
            @section('body_header')
                <div class="header py-4" id="tabler-header">
                    <div class="container">
                        <div class="d-flex">
                            @include('layouts.blocks.tabler.header-logo')
                            <div class="d-flex order-lg-2 ml-auto" id="header-options">
                                @include('layouts.blocks.tabler.header-assistant')
                                @section('body_header_notification')
                                    @include('layouts.blocks.tabler.notification')
                                @show
                                @if (\Illuminate\Support\Facades\Auth::check() && !empty($dorcasUser))
                                    @include('layouts.blocks.tabler.auth-options')
                                @endif
                            </div>
                            <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
                                <span class="header-toggler-icon"></span>
                            </a>
                        </div>
                    </div>
                </div>
                @section('body_header_nav')
                    @include('layouts.blocks.tabler.nav')
                @show
            @show
            @section('body_content')
                    <div class="my-3 my-md-5" id="tabler-content">
                        <div class="container">
                            @section('body_content_header')
                                <div class="page-header">
                                    <h1 class="page-title">
                                        {{ $header['title'] ?: 'Dashboard' }}
                                    </h1>
                                    @yield('body_content_header_extras')
                                </div>
                            @show
                            @yield('body_content_main')
                        </div>
                    </div>
            @show
        </div>
        @section('footer')
            @section('footer_top')
                &nbsp;
            @show
            @include('layouts.blocks.tabler.footer')
        @show
    @show
</div>
<!-- Dashboard Core -->
<script src="{{ cdn('apps/tabler/js/vendors/jquery-3.2.1.min.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/vendors/bootstrap.bundle.min.js') }}"></script>
<script src="{{ cdn('apps/tabler/plugins/prismjs/js/prism.pack.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/dashboard.js') }}"></script>
<!-- c3.js Charts Plugin -->
<script src="{{ cdn('apps/tabler/plugins/charts-c3/js/d3.v3.min.js') }}"></script>
<script src="{{ cdn('apps/tabler/plugins/charts-c3/js/c3.min.js') }}"></script>
<!-- Input Mask Plugin -->
<script src="{{ cdn('apps/tabler/plugins/input-mask/js/jquery.mask.min.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/core.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/lib/axios.min.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/lib/moment.min.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/lib/vue.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/lib/sweetalert.min.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/lib/voca.min.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/lib/tabler-components.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/lib/bootstrap-table/bootstrap-table.min.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/lib/moment.min.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/lib/combodate.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/app.js') }}"></script>
<!--custom-script.js - Add your own theme custom JS-->
<script src="{{ cdn('apps/tabler/js/custom-vue.js') }}"></script>
<script src="{{ cdn('apps/tabler/js/custom_script.js') }}"></script>
<!-- Production JS code -->
@if (app()->environment() === 'production')
    @include('layouts.blocks.production-js')
@endif
<script>
    $(function() {
        $('.combodate-date').combodate();
        $('.bootstrap-table').bootstrapTable({
            buttonsClass: 'outline',
            iconsPrefix: 'fe',
            icons: {
                paginationSwitchDown: 'fe-chevron-down',
                paginationSwitchUp: 'fe-chevron-up',
                refresh: 'fe-refresh-cw',
                toggle: 'fe-list',
                columns: 'fe-grid',
                detailOpen: 'fe-maximize-2',
                detailClose: 'fe-minimize-2'
            }
        });
    });
@if (!in_array(\Route::getFacadeRoot()->current()->uri(),array("login","register","forgot-password","reset-password")))

    new Vue({
        //el: '#tabler-header',
        el: '#notification-container',
        data: {
            notificationMessages: {!! json_encode(!empty($notificationMessages) ? $notificationMessages : []) !!},
        }
    });

    new Vue({
        el: '#headerMenuCollapse',
        data: {
            selectedMenu: '{{ !empty($selectedMenu) ? $selectedMenu : '' }}'
        }
    });

    new Vue({
        el: '#dorcas-auth-options',
        data: {
            loggedInUser: {!! json_encode(!empty($dorcasUser) ? $dorcasUser : []) !!},
            loggedInUserCompany: {!! json_encode(!empty($business) ? $business : []) !!},
            loggedInUserRole: {!! json_encode(!empty($dorcasUserRole) ? $dorcasUserRole : 'Business') !!}
        }
    });
@if (!in_array(\Route::getFacadeRoot()->current()->uri(),array("dashboard-business")))
    new Vue({
        el: '#sub-menu-menu',
        data: {
            selectedSubMenu: '{{ !empty($selectedSubMenu) ? $selectedSubMenu : '' }}',
        }
    });
@endif
/*
* The Assistant Listener
*/
@if (\Route::has("assistant-main"))

    var assistantVue = new Vue({
        el: '#modules-assistant',
        data: {
            assistant: [],
            a: {assistant: [], docs: [], help: []},
            loadingAssistant: true,
            showLessDocs: true,
            showDocsCount: 2,
            showDocsLabel: 'Show All'
        },
        methods: {
            modulesAssistant: function () {
                $('#modules-assistant-modal').modal('show');
            },
            generateAssistant: function (module, url) {
                var context = this;
                axios.get("/mas/assistant-generate/" + module + "/" + url)
                    .then(function (response) {
                        //console.log(response);
                        context.loadingAssistant = false;
                        context.assistant = response.data;
                        context.a.assistant = context.assistant.assistant_assistant;
                        context.a.docs = context.assistant.assistant_docs;
                        context.a.help = context.assistant.assistant_help;
                    })
                    .catch(function (error) {
                        var message = '';
                        if (error.response) {
                            //console.log(error.response)
                            // The request was made and the server responded with a status code
                            // that falls out of the range of 2xx
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            message = error.response.data.message;
                        } else if (error.request) {
                            // The request was made but no response was received
                            // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                            // http.ClientRequest in node.js
                            message = 'The request was made but no response was received';
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            message = error.message;
                        }
                        context.savingNote = false;
                    });

            },
            showDocsToggle: function () {
                if (this.showLessDocs === true) {
                    this.showLessDocs = false
                    this.showDocsLabel = 'Show Less'
                } else {
                    this.showLessDocs = true
                    this.showDocsLabel = 'Show All'
                }
            }
        },
        mounted: function () {
            var context = this;
            //console.log("Loading Assistant Module data...");
            context.loadingAssistant = true;
            let path_module = "";
            let path_url = "";
            let path_string = window.location.pathname;
            //console.log(path_string)
            let paths = path_string.split("/");
            path_module = paths[1];
            if (typeof paths[2] !== 'undefined') {
                path_url = paths[2];
            } else {
                path_url = "default";
            }
            
            //console.log(paths);
            context.generateAssistant(path_module,path_url);
        }
    });


    var assistantVueFooter = new Vue({
        el: '#modules-assistant-footer',
        methods: {
            modulesAssistant: function () {
                assistantVue.modulesAssistant();
            }
        }
    });

@endif

@endif

</script>
@yield('body_js')
</body>
</html>
