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
    <div id="respond-invite" class="row" style="width: 850px;">
        <div class="col s12 z-depth-4 card-panel">
            <div class="row">
                <div class="col s12 m6">
                    <form class="login-form" action="" method="post" v-if="invite.status === 'pending'">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="input-field col s12 center">
                                <img src="{{ !empty($domain) && !empty($domain->owner['data']['logo']) ? $domain->owner['data']['logo'] : cdn('images/logo/login-logo.png') }}"
                                     alt="" class="circle responsive-img valign profile-image-login">
                                <p class="center login-form-text">Respond to Invite</p>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="input-field col s12 m6">
                                <input autocomplete="off" required name="firstname" type="text" maxlength="100"
                                       v-model="invite.firstname"
                                       class="validate {{ $errors->has('firstname') ? ' invalid' : '' }}" id="firstname">
                                <label for="firstname"  @if ($errors->has('firstname')) data-error="{{ $errors->first('firstname') }}" @endif class="center-align">Firstname</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input autocomplete="off" required name="lastname" type="text" maxlength="30"
                                       v-model="invite.lastname"
                                       class="validate {{ $errors->has('lastname') ? ' invalid' : '' }}" id="lastname">
                                <label for="lastname"  @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif class="center-align">Lastname</label>
                            </div>
                        </div>
                        <div class="input-field col s12">
                            <input autocomplete="off" required name="email" type="email" maxlength="80"
                                   v-model="invite.email"
                                   class="validate {{ $errors->has('email') ? ' invalid' : '' }}" id="email">
                            <label for="email"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif class="center-align">Email</label>
                        </div>
                        <div class="row margin">
                            <div class="input-field col s12 m6">
                                <input autocomplete="off" required name="password" type="password"
                                       class="validate {{ $errors->has('password') ? ' invalid' : '' }}" id="password">
                                <label for="password" @if ($errors->has('password'))data-error="{{ $errors->first('password') }}"@endif>Password</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input autocomplete="off" required name="phone" type="text" maxlength="30" value="{{ old('phone') }}"
                                       class="validate {{ $errors->has('phone') ? ' invalid' : '' }}" id="phone">
                                <label for="phone"  @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif class="center-align">Phone</label>
                            </div>
                        </div>
                        <div class="input-field col s12" v-if="invite.config_data.action === 'invite_business'">
                            <input autocomplete="off" name="business" type="text" maxlength="100"
                                   v-model="invite.config_data.business"
                                   class="validate {{ $errors->has('business') ? ' invalid' : '' }}" id="business" autofocus>
                            <label for="company"  @if ($errors->has('business')) data-error="{{ $errors->first('business') }}" @endif class="center-align">Business Name</label>
                        </div>
                        <div class="row">
                            <div class="col s12">
                                <button class="btn waves-effect waves-light" type="submit">
                                    Accept Invite
                                </button>
                                <a class="btn btn-flat red-text darken-2" href="{{ route('invite', [$invite->id]) }}?reject_invite=1">Reject</a>
                            </div>
                        </div>
                    </form>
                    <div class="col s12" v-if="invite.status !== 'pending'">
                        @component('layouts.slots.empty-fullpage')
                            @slot('icon')
                                assistant
                            @endslot
                            Invite @{{ invite.status }}
                            @slot('buttons')
                            @endslot
                        @endcomponent
                    </div>
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
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#respond-invite',
            data: {
                invite: {!! json_encode($invite) !!}
            }
        });
    </script>
@endsection