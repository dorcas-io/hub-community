@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.ui-response-alert')
    <div class="container" id="personal-profile">
        <div class="row">
            <div class="col s12 m4">
                <h5>Change Password</h5>
                <form class="col s12" action="" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col s12">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="password" type="password" name="password"
                                           required class="validate {{ $errors->has('password') ? ' invalid' : '' }}">
                                    <label for="password"  @if ($errors->has('password')) data-error="{{ $errors->first('password') }}" @endif>New Password</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                           required class="validate {{ $errors->has('password_confirmation') ? ' invalid' : '' }}">
                                    <label for="password_confirmation"  @if ($errors->has('password_confirmation')) data-error="{{ $errors->first('password_confirmation') }}" @endif>Confirm Password</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn cyan waves-effect waves-light left" type="submit" name="action" value="update_password">
                                Change Password
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
