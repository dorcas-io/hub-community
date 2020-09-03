@extends('layouts.app')
@section('head_css')
    <link href="{{ cdn('css/layouts/page-center.css') }}" type="text/css" rel="stylesheet">
@endsection
@section('body_class')class="cyan" @endsection
@section('body')
    <div id="login-page" class="row">
        <div class="col s12 z-depth-4 card-panel">
            <form class="login-form" action="{{ route('password.request') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="row">
                    <div class="input-field col s12 center">
                        <img src="{{ !empty($appUiSettings['product_logo']) ? $appUiSettings['product_logo'] : cdn('images/logo/login-logo.png') }}" alt="" class="circle responsive-img valign profile-image-login">
                        <p class="center login-form-text">Reset account password</p>
                    </div>
                </div>
                <div class="row margin">
                    <div class="input-field col s12">
                        <input autocomplete="off" required name="email" type="email" value="{{ $email or old('email') }}"
                               class="validate {{ $errors->has('email') ? ' invalid' : '' }}" id="email" autofocus>
                        <label for="email"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif class="center-align">Account Email</label>
                    </div>
                </div>
                <div class="row margin">
                    <div class="input-field col s12">
                        <input autocomplete="off" required name="password" type="password"
                               class="validate {{ $errors->has('password') ? ' invalid' : '' }}" id="password">
                        <label for="password" @if ($errors->has('password'))data-error="{{ $errors->first('password') }}"@endif>New Password</label>
                    </div>
                </div>
                <div class="row margin">
                    <div class="input-field col s12">
                        <input autocomplete="off" required name="password_confirmation" type="password"
                               class="validate {{ $errors->has('password_confirmation') ? ' invalid' : '' }}" id="password_confirmation">
                        <label for="password_confirmation" @if ($errors->has('password_confirmation'))data-error="{{ $errors->first('password_confirmation') }}"@endif>Password</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <button class="btn waves-effect waves-light col s12" type="submit">
                            Reset Password
                        </button>
                    </div>
                    <div class="input-field col s12">
                        <p class="margin center medium-small sign-up">
                            Want to login instead? <a href="{{ route('login') }}">Login</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection