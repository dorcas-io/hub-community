@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section" id="transtrak-box">
            <div class="col s12" v-if="mode === 'intro'">
                <div class="progress" v-if="is_processing">
                    <div class="indeterminate"></div>
                </div>
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        layers
                    @endslot
                    <span v-if="!transtrakConfig.transtrak_enabled">
                        Transtrak automates your accounting by intelligently processing your bank email notifications and sorting them so they show up in your accounting reports.
                    </span>
                    <span v-if="transtrakConfig.transtrak_enabled && !transtrakConfig.transtrak_auto_enabled">
                        Transtrak has been enabled on your account, the next step is to enable auto-processing on your email.
                    </span>
                        <span v-if="transtrakConfig.transtrak_enabled && transtrakConfig.transtrak_auto_enabled">
                        Perfect, your Transtrak setup has been completed. Your set email will be processed periodically.
                    </span>
                    @slot('buttons')
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light"
                           href="{{ route('apps.finance.transtrak') }}?mode=setup">
                            Get Started
                        </a>
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light disabled"
                           v-on:click.prevent="transtrakEnableAuto">
                            Enable Auto Processing
                        </a>
                    @endslot
                @endcomponent
            </div>
            <div class="col s12" v-if="mode === 'setup'">
                <div class="row">
                    <div class="col s12 m6">
                        <form class="col s12" action="" method="post" v-on:submit.prevent="transtrakLogin" v-if="showEmailOptions">
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <input id="inbox_email" type="email" name="inbox_email" maxlength="100"
                                           v-model="setup.username" required>
                                    <label for="inbox_email" v-bind:class="{active: prefilledEmail}">Email Address</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="inbox_password" type="password" name="inbox_password" maxlength="100"
                                           v-model="setup.password" required>
                                    <label for="notifier_subject">Password</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <select id="provider" name="provider" class="browser-default" v-model="setup.provider">
                                        <option value="manual">Manual Configuration</option>
                                        <option value="gmail">Gmail Account</option>
                                        <option value="yahoo">Yahoo! Account</option>
                                    </select>
                                </div>
                                <div class="input-field col s12 m6" v-if="setup.provider === 'manual'">
                                    <input id="imap_url" type="text" name="imap_url" maxlength="100" required
                                           v-model="setup.imap_url">
                                    <label for="imap_url">IMAP URL</label>
                                </div>
                            </div>
                            <div class="row" v-if="setup.provider === 'manual'">
                                <div class="input-field col s12 m6">
                                    <input id="imap_port" type="number" name="imap_port" maxlength="5" step="1" required
                                           v-model="setup.imap_port">
                                    <label for="imap_port">IMAP Port</label>
                                </div>
                            </div>
                            <div class="row mt-8">
                                <div class="col s12">
                                    <div class="progress" v-if="is_processing">
                                        <div class="indeterminate"></div>
                                    </div>
                                    <button class="btn waves-effect waves-light col s12" type="submit" v-if="!is_processing">
                                        @{{ transtrakButtonText }}
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form class="col s12" action="" method="post" v-on:submit.prevent="transtrakFetch" v-show="showBankSelector">
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <select name="bank" id="bank" class="browser-default" v-model="setup.bank" v-on:change="changeSender">
                                        <option value="" disabled>Select the Bank</option>
                                        <option v-for="(config, key) in banks" :key="key" :value="key">@{{ key }}</option>
                                    </select>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input type="text" class="custom-datepicker" name="from_date" id="from_date"
                                           v-model="setup.from_date">
                                    <label for="from_date">From Date</label>
                                </div>
                            </div>
                            <!--<div class="row" v-show="!setup.hide_subject_line">
                                <div class="input-field col s12 m6">
                                    <input id="notifier_sender" type="email" name="notifier_sender" maxlength="100"
                                           v-model="setup.sender_email">
                                    <label for="notifier_sender" v-bind:class="{active: prefilledText}">Bank Email Sender</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="notifier_subject" type="text" name="notifier_subject" maxlength="100"
                                           v-model="setup.sender_subject">
                                    <label for="notifier_subject" v-bind:class="{active: prefilledText}">Bank Alert Subject</label>
                                </div>
                            </div>-->
                            <div class="row">

                                <!--<div class="input-field col s12 m6">
                                    <div class="card">
                                        <div class="card-content">
                                            <span class="card-title">&nbsp;</span>
                                            <div class="switch">
                                                <label>
                                                    Disabled
                                                    <input type="checkbox" name="auto_processing" value="1" v-model="setup.auto_processing">
                                                    <span class="lever"></span>
                                                    Enabled
                                                </label>
                                            </div>
                                            <p>Allow TransTrak to periodically search for similar emails, and process them</p>
                                        </div>
                                    </div>
                                </div>-->
                            </div>
                            <div class="row">
                                <div class="col s12">
                                    <div class="progress" v-if="is_processing">
                                        <div class="indeterminate"></div>
                                    </div>
                                    <button class="btn waves-effect waves-light col s12" type="submit" v-if="!is_processing">
                                        Process Emails
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col s12 m6">
                        <div class="card" v-show="setup.show_email_instructions">
                            <div class="card-content blue white-text">
                                <p>
                                    Depending on your email provider, you might need to take some additional action.
                                    See below for details
                                </p>
                            </div>
                            <div class="card-tabs tabs-transparent">
                                <ul class="tabs tabs-fixed-width">
                                    <li class="tab"><a href="#gmail" class="active">GMail</a></li>
                                    <li class="tab"><a href="#yahoomail">Yahoo!</a></li>
                                    <li class="tab"><a href="#manual">Manual Configuration</a></li>
                                </ul>
                            </div>
                            <div class="card-content grey lighten-3">
                                <div  class="active" id="gmail">
                                    <p>
                                        You need to enable a few settings on your GMail account to continue:
                                    </p>
                                    <ul>
                                        <li>Click <a href="https://myaccount.google.com/lesssecureapps" target="_blank">https://myaccount.google.com/lesssecureapps</a> and Enable</li>
                                        <li>Click <a href="https://www.google.com/accounts/DisplayUnlockCaptcha" target="_blank">https://www.google.com/accounts/DisplayUnlockCaptcha</a> and Continue</li>
                                        <li>You might need to generate an app-specific password for safety
                                            <a href="https://myaccount.google.com/apppasswords" target="_blank">https://myaccount.google.com/apppasswords</a> and Continue</li>
                                        <li>Ensure IMAP setting is enabled in your Gmail Settings</li>
                                    </ul>
                                </div>
                                <div id="yahoomail">
                                    <p>
                                        Go to your Yahoo Settings and Enable "Allow apps that use less secure sign in"
                                    </p>
                                    <ul>
                                        <li>Click <a href="https://login.yahoo.com/account/security?el=1" target="_blank">https://login.yahoo.com/account/security?el=1</a> and Enable</li>
                                    </ul>
                                </div>
                                <div id="manual">
                                    <p>
                                        Sign in to your email provider (e.g. Webmail), and follow these steps:
                                    </p>
                                    <ul>
                                        <li>Check for the "Configure Mail Client" option</li>
                                        <li>Copy the different settings for "IMAP HOST", "IMAP PORT"</li>
                                        <li>Continue</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        $(function() {
            $('.custom-datepicker').pickadate({
                today: 'Today',
                clear: 'Clear',
                close: 'Ok',
                closeOnSelect: false,
                onClose: function() {
                    console.log('onClose');
                    vm.setup.from_date = this.get('select', 'dd-mmm-yyyy');
                }
            });
        });
        var vm = new Vue({
            el: '#transtrak-box',
            data: {
                mode: '{{ $mode }}',
                banks: {!! json_encode($bankConfig) !!},
                is_processing: false,
                prefilledEmail: true,
                prefilledText: false,
                transtrakConfig: {!! json_encode($transtrakConfig) !!},
                setup: {!! json_encode($providerSetup) !!},
                showBankSelector: false,
                showEmailOptions: true,
                user: {!! json_encode($dorcasUser) !!},
                transtrakButtonText: "Test Connection"
            },
            methods: {
                clearSetup: function () {
                    this.setup =  {bank: '', account_no: '', sender_email: '', sender_subject: ''};
                },
                changeSender: function () {
                    if (this.setup.bank.length === 0) {
                        this.clearSetup();
                        return;
                    }
                    var config = typeof this.banks[this.setup.bank] !== 'undefined' ? this.banks[this.setup.bank] : null;
                    if (config === null) {
                        this.clearSetup();
                        return;
                    }
                    this.setup.sender_email = config['sender'] || '';
                    this.setup.sender_subject = config['title'] || '';
                },
                setMode: function (mode) {
                    this.mode = mode;
                },
                transtrakLogin: function () {
                    var context = this;
                    this.is_processing = true;
                    axios.post('/xhr/finance/transtrak/login', {
                        username: context.setup.username,
                        password: context.setup.password,
                        provider: context.setup.provider,
                        imap_host: context.setup.imap_url,
                        imap_port: context.setup.imap_port
                    }).then(function (response) {
                            console.log(response);
                            context.is_processing = false;
                            context.showEmailOptions = false;
                            context.showBankSelector = true;
                            context.transtrakButtonText = "Run Transtrak";
                        })
                        .catch(function (error) {
                            var message = '';
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                                var e = error.response.data.errors[0];
                                message = e.title;
                            } else if (error.request) {
                                // The request was made but no response was received
                                // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                // http.ClientRequest in node.js
                                message = 'The request was made but no response was received';
                            } else {
                                // Something happened in setting up the request that triggered an Error
                                message = error.message;
                            }
                            context.setup.show_email_instructions = true;
                            context.is_processing = false;
                            Materialize.toast('Error: '+message, 10000);
                        });
                },
                transtrakEnableAuto: function () {
                    var context = this;
                    this.is_processing = true;
                    axios.post('/xhr/finance/transtrak/enable-auto-processing', {
                        account: 'all'
                    }).then(function (response) {
                        console.log(response);
                        window.location = '{{ url()->current() }}';
                    })
                        .catch(function (error) {
                            var message = '';
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                                var e = error.response.data.errors[0];
                                message = e.title;
                                if (message.toLowerCase() === 'additional gmail settings needed') {
                                    // extra gmail settings required
                                    context.setup.show_email_instructions = true;
                                }
                            } else if (error.request) {
                                // The request was made but no response was received
                                // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                // http.ClientRequest in node.js
                                message = 'The request was made but no response was received';
                            } else {
                                // Something happened in setting up the request that triggered an Error
                                message = error.message;
                            }
                            context.is_processing = false;
                            Materialize.toast('Error: '+message, 4000);
                        });
                },
                transtrakFetch: function () {
                    var context = this;
                    this.is_processing = true;
                    axios.post('/xhr/finance/transtrak/fetch', {
                        username: context.setup.username,
                        password: context.setup.password,
                        provider: context.setup.provider,
                        imap_host: context.setup.imap_url,
                        imap_port: context.setup.imap_port,
                        user_id: context.user.id,
                        sender_address: context.setup.sender_email,
                        mail_subject: context.setup.sender_subject,
                        mail_since: context.setup.from_date
                    }).then(function (response) {
                            console.log(response);
                            context.is_processing = false;
                            context.showEmailOptions = true;
                            context.showBankSelector = false;
                            swal("Great", "Emails were fetched and will be processed in a while. If it succeeds, you will see the entries on the entries section.", "success");
                        })
                        .catch(function (error) {
                            var message = '';
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                                var e = error.response.data.errors[0];
                                message = e.title;
                            } else if (error.request) {
                                // The request was made but no response was received
                                // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                // http.ClientRequest in node.js
                                message = 'The request was made but no response was received';
                            } else {
                                // Something happened in setting up the request that triggered an Error
                                message = error.message;
                            }
                            context.is_processing = false;
                            Materialize.toast('Error: '+message, 4000);
                        });
                }
            }
        });
    </script>
@endsection