@extends('layouts.app')
@section('body_main_content_header_button')

@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section">
            <div class="col s12">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        public
                    @endslot
                    We have various ECommerce tools to help build your online presence.
                    @slot('buttons')
                        <a class="btn-flat grey darken-3 white-text waves-effect waves-light"
                           href="{{ route('apps.ecommerce.store') }}">
                            Online Store
                        </a>
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light"
                           href="{{ route('apps.ecommerce.website') }}">
                            Website Builder
                        </a>
                        <a class="btn-flat brown darken-3 white-text waves-effect waves-light"
                           href="{{ route('apps.ecommerce.blog') }}">
                            Blog
                        </a>
                    @endslot
                @endcomponent
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

@endsection