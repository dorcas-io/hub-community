@extends('vpanel.layouts.app')
@section('body_content_main_header')
    <header>
        <div class="title-wrap pull-left">
            <div class="wrap">
                <div class="title">{{ $header['title'] }}</div>
            </div>
            <div class="reset"></div>
        </div>
        <div class="opts-wrap pull-right">

        </div>
    </header>
@endsection
@section('body_content_main_container')
    <div class="scrollable" id="settings-box">
        <div class="v-form_wrap">
            <div class="vgap-1x"></div>
            <div class="progress" v-if="loading">
                <div class="progress-bar progress-bar-animated progress-bar-striped" style="width: 100%"></div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="title">Profile Settings</div>
                    <div class="vgap-2x"></div>
                    <form method="post" action="">
                        {{ csrf_field() }}
                        <div class="form">
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname" required
                                               placeholder="First Name" v-model="user.firstname" maxlength="30">
                                        @if ($errors->has('firstname'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('firstname') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname" required
                                               placeholder="Last Name" v-model="user.lastname" maxlength="30">
                                        @if ($errors->has('lastname'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('lastname') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select class="form-control" name="gender" id="gender" v-model="user.gender" required>
                                            <option value="">Select your Gender</option>
                                            <option value="female">Female</option>
                                            <option value="male">Male</option>
                                        </select>
                                        @if ($errors->has('gender'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('gender') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" required
                                               placeholder="Contact Phone" v-model="user.phone" maxlength="30">
                                        @if ($errors->has('phone'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-default btn-default-type" name="action"
                                    value="update_profile">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="vgap-3x"></div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="title">Profile Photo</div>
                    <div class="vgap-2x"></div>
                    <form method="post" action="" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form">
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Avatar (We recommend square images e.g. 100x100)</label>
                                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                        @if ($errors->has('photo'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('photo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-default btn-default-type" name="action"
                                    value="update_photo">Update Photo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script>
        new Vue({
            el: '#settings-box',
            data: {
                user: {!! json_encode($dorcasUser) !!},
                company: {!! json_encode($business) !!},
                loading: false
            },

        });
    </script>
@endsection

