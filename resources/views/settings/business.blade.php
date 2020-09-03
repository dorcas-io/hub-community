@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.ui-response-alert')
    <div class="container" id="business-profile">
        <div class="row">
            <div class="col s12 m6">
                <h5>Business Information</h5>
                <form class="col s12" action="" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col s12">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="name" type="text" name="name" maxlength="80" v-model="company.name"
                                           required class="validate {{ $errors->has('name') ? ' invalid' : '' }}">
                                    <label for="name"  @if ($errors->has('name')) data-error="{{ $errors->first('name') }}" @endif>Business Name</label>
                                </div>
                                <div class="input-field col s12 m4">
                                    <input id="registration" type="text" name="registration" v-model="company.registration"
                                           maxlength="30" class="validate {{ $errors->has('registration') ? ' invalid' : '' }}">
                                    <label for="registration" @if ($errors->has('registration')) data-error="{{ $errors->first('registration') }}" @endif>Registration Number</label>
                                </div>
                                <div class="input-field col s12 m8">
                                    <input id="phone" type="text" name="phone" v-model="company.phone" maxlength="30"
                                           class="validate {{ $errors->has('phone') ? ' invalid' : '' }}">
                                    <label for="phone"  @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif>Contact Phone</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="email" type="email" name="email" v-model="company.email" maxlength="80"
                                           class="validate {{ $errors->has('email') ? ' invalid' : '' }}">
                                    <label for="email" class="active"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif>Email</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="website" type="text" name="website" v-model="company.website" maxlength="80"
                                           class="validate {{ $errors->has('website') ? ' invalid' : '' }}">
                                    <label for="website" class="active"  @if ($errors->has('website')) data-error="{{ $errors->first('website') }}" @endif>Website</label>
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
            <div class="col s12 m6">
                <h5>Address information</h5>
                <form class="col s12" action="" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col s12">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="address1" type="text" name="address1" v-model="location.address1"
                                           maxlength="100" required class="validate {{ $errors->has('address1') ? ' invalid' : '' }}">
                                    <label for="address1"  @if ($errors->has('address1')) data-error="{{ $errors->first('address1') }}" @endif>Address Line 1</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="address2" type="text" name="address2" v-model="location.address2"
                                           maxlength="100" class="validate {{ $errors->has('address2') ? ' invalid' : '' }}">
                                    <label for="address2"  @if ($errors->has('address2')) data-error="{{ $errors->first('address2') }}" @endif>Address Line 2</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="city" type="text" name="city" maxlength="100" v-model="location.city"
                                           required class="validate {{ $errors->has('city') ? ' invalid' : '' }}">
                                    <label for="city"  @if ($errors->has('city')) data-error="{{ $errors->first('city') }}" @endif>City</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <select name="state" id="state" v-model="location.state.data.id"
                                            class="validate {{ $errors->has('state') ? ' invalid' : '' }}">
                                        <option v-for="state in states" :value="state.id" :key="state.id">@{{ state.name }}</option>
                                    </select>
                                    <label for="state"  @if ($errors->has('state')) data-error="{{ $errors->first('state') }}" @endif>State</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn cyan waves-effect waves-light left" type="submit" name="action" value="update_location">
                                Update Address
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
            el: '#business-profile',
            data: {
                company: {!! json_encode($company) !!},
                location: {!! json_encode($location) !!},
                states: {!! json_encode($states) !!}
            }
        })
    </script>
@endsection
