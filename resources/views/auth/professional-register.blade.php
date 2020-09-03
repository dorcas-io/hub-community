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
    <div id="register-page" class="row" style="width: 850px;">
        <div class="col s12 z-depth-4 card-panel">
            <div class="row">
                <div class="col s12 m6">
                    <form class="login-form" action="{{ route('register') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="input-field col s12 center">
                                <img src="{{ cdn('images/logo/login-logo.png') }}" alt="" class="circle responsive-img valign profile-image-login">
                                <p class="center login-form-text">Create a Professional Account</p>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="input-field col s12 m6 l6">
                                <input autocomplete="off" required name="firstname" type="text" maxlength="100" value="{{ old('firstname') }}"
                                       class="validate {{ $errors->has('firstname') ? ' invalid' : '' }}" id="firstname">
                                <label for="firstname"  @if ($errors->has('firstname')) data-error="{{ $errors->first('firstname') }}" @endif class="center-align">Firstname</label>
                            </div>
                            <div class="input-field col s12 m6 l6">
                                <input autocomplete="off" required name="lastname" type="text" maxlength="30" value="{{ old('lastname') }}"
                                       class="validate {{ $errors->has('lastname') ? ' invalid' : '' }}" id="lastname">
                                <label for="lastname"  @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif class="center-align">Lastname</label>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="input-field col s12 m6 l6">
                                <input autocomplete="off" required name="email" type="email" maxlength="80" value="{{ old('email') }}"
                                       class="validate {{ $errors->has('email') ? ' invalid' : '' }}" id="email">
                                <label for="email"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif class="center-align">Email</label>
                            </div>
                            <div class="input-field col s12 m6 l6">
                                <input autocomplete="off" required name="password" type="password"
                                       class="validate {{ $errors->has('password') ? ' invalid' : '' }}" id="password">
                                <label for="password" @if ($errors->has('password'))data-error="{{ $errors->first('password') }}"@endif>Password</label>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="input-field col s12 m6 l6">
                                <input autocomplete="off" required name="phone" type="text" maxlength="30" value="{{ old('phone') }}"
                                       class="validate {{ $errors->has('phone') ? ' invalid' : '' }}" id="phone">
                                <label for="phone"  @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif class="center-align">Phone</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12">
                                <input type="hidden" name="plan" value="{{ $plan }}" />
                                <input type="hidden" name="plan_type" value="{{ $plan_type }}" />
                                <input type="hidden" name="is_professional" value="1" />
                                <button class="btn waves-effect waves-light col s12" type="submit">
                                    Sign Up
                                </button>
                            </div>
                            <div class="input-field col s12">
                                <p class="margin center medium-small sign-up">
                                    Already have an account? <a href="{{ route('login') }}">Login</a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col s12 m6 pt-4">
                    @component('layouts.slots.video-embed')
                        //www.youtube.com/embed/WNgsHWOpoF4?rel=0
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection