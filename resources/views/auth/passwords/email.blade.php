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
    <div id="login-page" class="row">
        <div class="col s12 z-depth-4 card-panel">
            <form class="login-form" action="{{ route('password.email') }}" method="post">
                {{ csrf_field() }}
                <div class="row">
                    <div class="input-field col s12 center">
                        <img src="{{ !empty($appUiSettings['product_logo']) ? $appUiSettings['product_logo'] : cdn('images/logo/login-logo.png') }}" alt="" class="circle responsive-img valign profile-image-login">
                        <p class="center login-form-text">Forgot Password</p>
                    </div>
                </div>
                <div class="row margin">
                    <div class="input-field col s12">
                        <input autocomplete="off" required name="email" type="email"
                               class="validate {{ $errors->has('email') ? ' invalid' : '' }}" id="email" autofocus>
                        <label for="email"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif class="center-align">Account Email</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <button class="btn waves-effect waves-light col s12" type="submit">
                            Continue
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12 m8 l8">
                        <p class="margin medium-small"><a href="{{ route('register') }}">Create an account</a></p>
                    </div>
                    <div class="input-field col s12 m4 l4">
                        <p class="margin right-align medium-small"><a href="{{ route('login') }}">Login</a></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        $(function () {
            @if (!empty($status))
                Materialize.toast("{{ $status }}", 7000);
            @endif
        });
    </script>
@endsection
