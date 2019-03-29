@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.ui-response-alert')
    <div class="container" id="personal-profile">
        <div class="row">
            <div class="col s12 m6">
                <h5>Personal Information</h5>
                <form class="col s12" action="" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col s12">
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <input id="firstname" type="text" name="firstname" maxlength="30" v-model="user.firstname"
                                           required class="validate {{ $errors->has('firstname') ? ' invalid' : '' }}">
                                    <label for="firstname"  @if ($errors->has('firstname')) data-error="{{ $errors->first('firstname') }}" @endif>Firstname</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="lastname" type="text" name="lastname" maxlength="30" v-model="user.lastname"
                                           required class="validate {{ $errors->has('lastname') ? ' invalid' : '' }}">
                                    <label for="lastname"  @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif>Lastname</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="email" type="email" name="email" v-model="user.email" maxlength="80"
                                           required class="validate {{ $errors->has('email') ? ' invalid' : '' }}">
                                    <label for="email"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif>Account Email</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="phone" type="text" name="phone" v-model="user.phone" maxlength="30"
                                           class="validate {{ $errors->has('phone') ? ' invalid' : '' }}">
                                    <label for="phone" class="active" @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif>Phone</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <select name="gender" id="gender" v-model="user.gender" class="validate {{ $errors->has('gender') ? ' invalid' : '' }}">
                                        <option value="" disabled>Select Gender</option>
                                        <option value="female">Female</option>
                                        <option value="male">Male</option>
                                    </select>
                                    <label for="gender" @if ($errors->has('gender')) data-error="{{ $errors->first('gender') }}" @endif>Gender</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn cyan waves-effect waves-light left" type="submit" name="action" value="update_business">
                                Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#personal-profile',
            data: {
                user: {!! json_encode($dorcasUser) !!}
            }
        })
    </script>
@endsection
