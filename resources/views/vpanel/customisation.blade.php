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
            <div class="row">
                <div class="col-sm-12">
                    <div class="title">Adjust your Settings</div>
                    <div class="vgap-2x"></div>
                    <form method="post" action="">
                        {{ csrf_field() }}
                        <div class="form">
                            <div class="progress" v-if="loading">
                                <div class="progress-bar progress-bar-animated progress-bar-striped" style="width: 100%"></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Display Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required
                                               placeholder="Name to show" v-model="partner.name" maxlength="80">
                                        @if ($errors->has('name'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Welcome Video (YouTube Only)</label>
                                        <input type="text" class="form-control" id="video_url" name="video_url"
                                               placeholder="Welcome video to show users"
                                               v-model="video_url" maxlength="80">
                                        @if ($errors->has('video_url'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('video_url') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" class="form-control" id="product_name" name="product_name"
                                               placeholder="What do you want to name this product?"
                                               v-model="hubConfig.product_name" maxlength="80">
                                        @if ($errors->has('product_name'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('product_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Support Email</label>
                                        <input type="text" class="form-control" id="support_email" name="support_email"
                                               placeholder="Support Email Address" v-model="partner.extra_data.support_email" maxlength="80">
                                        @if ($errors->has('support_email'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('support_email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Email Subject</label>
                                        <input type="text" class="form-control" id="email_subject" name="email_subject"
                                               placeholder="Invite Email Subject" v-model="inviteConfig.email_subject" maxlength="100">
                                        @if ($errors->has('email_subject'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('email_subject') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Email Body</label>
                                        <textarea type="text" class="form-control" id="email_body" name="email_body" v-model="inviteConfig.email_body" rows="5">
                                        </textarea>
                                        @if ($errors->has('email_body'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('email_body') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Email Footer</label>
                                        <textarea type="text" class="form-control" id="email_footer" name="email_footer" v-model="inviteConfig.email_footer" rows="3">
                                        </textarea>
                                        @if ($errors->has('email_footer'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('email_footer') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-default btn-default-type">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="vgap-3x"></div>
            <div class="row">
                <div class="col-sm-12 col-md-4" v-if="video_url.length > 0">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" :src="'https://www.youtube.com/embed/' + partner.extra_data.welcome_video_id + '?rel=0'" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <div class="vgap-3x"></div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="title">Brand Settings</div>
                    <div class="vgap-2x"></div>
                    <form method="post" action="" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form">
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Business Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        @if ($errors->has('logo'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('logo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-default btn-default-type">Save Settings</button>
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
                address: {},
                partner: {!! json_encode($partner) !!},
                hubConfig: {},
                inviteConfig: {},
                loading: false
            },
            mounted: function () {
                if (typeof this.partner.extra_data.hubConfig !== 'undefined') {
                    this.hubConfig = this.partner.extra_data.hubConfig;
                }
                if (typeof this.partner.extra_data.inviteConfig !== 'undefined') {
                    this.inviteConfig = this.partner.extra_data.inviteConfig;
                }
            },
            computed: {
                video_url: function () {
                    let video_id = typeof this.partner.extra_data.welcome_video_id !== 'undefined' ? this.partner.extra_data.welcome_video_id : '';
                    if (video_id.length === 0) {
                        return '';
                    }
                    return 'https://youtu.be/' + video_id;
                }
            }
        });
    </script>
@endsection

