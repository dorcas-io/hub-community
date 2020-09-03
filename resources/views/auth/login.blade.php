@extends('layouts.app')
@section('head_css')
    <link href="{{ cdn('css/layouts/page-center.css') }}" type="text/css" rel="stylesheet">
    <style type="text/css">
        body {
            background: url("{{ cdn('/images/background/all_in_one_3.png') }}") top center no-repeat;
        }
    </style>
@endsection
@section('body_class')class="cyan" @endsection
@section('body')
    <div id="login-page" class="row" style="width: 850px;">
        <div class="col s12 z-depth-4 card-panel">
            @include('blocks.ui-response-alert')
            <div class="row">
                <div class="col s12 m6">
                    <form class="login-form" action="{{ route('login') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="input-field col s12 center">
                                <img src="{{ !empty($appUiSettings['product_logo']) ? $appUiSettings['product_logo'] : cdn('images/logo/login-logo_dorcas.png') }}" alt="" class="responsive-img valign profile-image-login" style="height: auto !important; width:auto !important; max-width: 250px !important; max-height: 100px !important;">
                                <p class="center login-form-text">
                                    <!-- {{ !empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : config('app.name') }} -->
                                    {{ $page['login_product_name'] }}
                                </p>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="input-field col s12">
                                <input autocomplete="off" required name="email" type="email" value="{{ old('email') }}"
                                       class="validate {{ $errors->has('email') ? ' invalid' : '' }}" id="email" autofocus>
                                <label for="email"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif class="center-align">Email</label>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="input-field col s12">
                                <input autocomplete="off" required name="password" type="password"
                                       class="validate {{ $errors->has('password') ? ' invalid' : '' }}" id="password">
                                <label for="password" @if ($errors->has('password'))data-error="{{ $errors->first('password') }}"@endif>Password</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 m12 l12 ml-2 mt-3">
                                <input type="checkbox" id="remember" name="remember" value="1" />
                                <label for="remember">Remember me</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12">
                                <button class="btn waves-effect waves-light col s12" type="submit">
                                    Log In
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <p class="margin medium-small"><a href="{{ route('register') }}">Create an Account</a></p>
                            </div>
                            <div class="input-field col s12 m6">
                                <p class="margin right-align medium-small"><a href="{{ url('/forgot-password') }}">Forgot password?</a></p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col s12 m6 pt-4">
                    @component('layouts.slots.video-embed')
                        //www.youtube.com/embed/{{ !empty($partner) && !empty($partner->extra_data['welcome_video_id']) ? $partner->extra_data['welcome_video_id'] : 'WNgsHWOpoF4' }}?rel=0
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection
