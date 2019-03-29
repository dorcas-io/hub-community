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
                        local_mall
                    @endslot
                    You can very easily setup an online store for your brand, and start collecting payments from your customers.
                    @slot('buttons')
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light"
                           href="{{ route('apps.ecommerce.store.dashboard') }}">
                            Get Started
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