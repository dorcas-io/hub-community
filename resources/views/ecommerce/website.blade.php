@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="website-setup">
        <div class="row section">
            <div class="col s12">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        public
                    @endslot
                    Dorcas website builder makes it easy to create your own web page by using a drag-and-drop interface.<br>
                    You need to have a registered domain before you can publish your website.
                    @slot('buttons')
                        @if (empty($domains) || $domains->count() === 0)
                            @if ($isOnPaidPlan)
                                <a class="btn-flat brown darken-3 white-text waves-effect waves-light"
                                   href="{{ route('apps.ecommerce.domains') }}">
                                    Buy a Domain
                                </a>
                            @else
                                <a class="btn-flat brown darken-3 white-text waves-effect waves-light"
                                   href="{{ route('subscription') }}">
                                    Upgrade &amp; buy a Domain
                                </a>
                            @endif
                            &nbsp;
                            <a class="btn-flat blue darken-3 white-text waves-effect waves-light" target="_blank"
                               href="{{ config('dorcas-builder.url') . '/auth/via_dorcas?' . http_build_query($authParams ?: []) }}">
                                Get Started
                            </a>
                        @elseif (!$isHostingSetup)
                            <form action="" method="post" v-on:submit=>
                                {{ csrf_field() }}
                                <button class="btn-flat grey darken-3 white-text waves-effect waves-light" name="action"
                                        value="setup_hosting" v-if="!isHostingRequestProcessing">
                                    Setup Hosting
                                </button>
                                <a class="btn-flat blue darken-3 white-text waves-effect waves-light" target="_blank"
                                   href="{{ config('dorcas-builder.url') . '/auth/via_dorcas?' . http_build_query($authParams ?: []) }}">
                                    Get Started
                                </a>
                                <div class="progress" v-if="isHostingRequestProcessing">
                                    <div class="indeterminate"></div>
                                </div>
                            </form>
                        @else
                            <a class="btn-flat blue darken-3 white-text waves-effect waves-light" target="_blank"
                               href="{{ config('dorcas-builder.url') . '/auth/via_dorcas?' . http_build_query($authParams ?: []) }}">
                                Get Started
                            </a>
                        @endif
                    @endslot
                @endcomponent
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script>
        new Vue({
            el: '#website-setup',
            data: {
                isHostingRequestProcessing: false
            },
            methods: {
                requestHosting: function () {
                    this.isHostingRequestProcessing = true;
                }
            }
        })
    </script>
@endsection
@section('help_modal_content')
    @component('layouts.slots.video-embed')
        //www.youtube.com/embed/wlZfFi9FsOk?rel=0
    @endcomponent
@endsection