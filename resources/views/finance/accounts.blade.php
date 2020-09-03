@extends('layouts.app')
@section('body_main_content_header_button')
    @if (!empty($mode) && $mode !== 'topmost')
        <a class="btn dropdown-settings waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow modal-trigger"
           v-if=""
           href="#add-subaccount" style="">
            <i class="material-icons hide-on-med-and-up">add</i>
            <span class="hide-on-small-onl">Add SubAccount</span>
        </a>
    @endif
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="row" id="finance-accounts">
        <div class="col s12">
            <div class="progress" v-if="is_processing">
                <div class="indeterminate"></div>
            </div>
        </div>
        <div class="col s12 m4" v-for="(account, index) in accounts" :key="account.id">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">@{{ account.display_name }}</span>
                    <h4>@{{ account.entry_type.title_case() }}</h4>
                </div>
                <div class="card-action">
                    <a class="activator black-text" href="#" style="margin-right: 12px;">Edit</a>
                    <a class="black-text" v-bind:href="'{{ route('apps.finance') }}/' + account.id" v-if="mode === 'topmost'" style="margin-right: 12px;">Sub-Accounts</a>
                    <a class="black-text" v-bind:href="'{{ route('apps.finance.entries') }}?account=' + account.id" style="margin-right: 12px;">Entries</a>
                    <a href="#" v-on:click.prevent="toggleVisibility(index)"
                       data-tooltip="Whether or not to show in reports"
                       class="tooltipped"  style="margin-right: 12px;"
                       v-bind:class="{'red-text': account.is_visible, 'black-text': !account.is_visible}">@{{ account.is_visible ? 'Hide' : 'Show' }}</a>
                </div>
                <div class="card-reveal">
                    <span class="card-title grey-text text-darken-4">Edit Details<i class="material-icons right">close</i></span>
                    <form method="post" action="" v-on:submit.prevent="editAccount(index)">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input id="name" type="text" v-model="accounts[index].display_name" name="name" maxlength="80" required>
                                <label for="name">Account Name</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <select name="entry_type" id="entry_type" v-model="accounts[index].entry_type" class="browser-default">
                                    <option value="" disabled>Record Entry Type</option>
                                    <option value="credit">Credit</option>
                                    <option value="debit">Debit</option>
                                </select>
                            </div>
                            <div class="col s12 mb-2 mt-4">
                                <button class="btn waves-effect waves-light" type="submit" name="action"
                                        v-if="!is_processing">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col s12" v-if="accounts.length === 0">
            @component('layouts.slots.empty-fullpage')
                @slot('icon')
                    attach_money
                @endslot
                You have not yet setup Finance, you can use the button below to do that now.
                @slot('buttons')
                    <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="#"
                       v-on:click.prevent="installFinance">
                        Setup Finance
                    </a>
                @endslot
            @endcomponent
        </div>
        @if (!empty($baseAccount))
            @include('finance.modals.add-subaccount')
        @endif
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#finance-accounts',
            data: {
                accounts: {!! json_encode($accounts ?: []) !!},
                is_processing: false,
                mode: '{{ empty($mode) ? 'topmost' : $mode }}'
            },
            methods: {
                editAccount: function (index) {
                    var context = this;
                    var account = this.accounts.length > index ? this.accounts[index] : null;
                    if (account === null) {
                        return;
                    }
                    context.is_processing = true;
                    axios.put("/xhr/finance/accounts/" + account.id, {
                        display_name: account.display_name,
                        entry_type: account.entry_type
                    })
                        .then(function (response) {
                            console.log(response);
                            context.is_processing = false;
                            context.accounts.splice(index, 1, response.data);
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
                },
                installFinance: function () {
                    this.is_processing = true;
                    var context = this;
                    axios.post("/xhr/finance/install")
                        .then(function (response) {
                            console.log(response);
                            context.is_processing = false;
                            context.accounts = response.data.filter(function (r) { return typeof r.parent_account === 'undefined' || r.parent_account.data.length === 0; });
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
                },
                toggleVisibility: function (index) {
                    var context = this;
                    var account = this.accounts.length > index ? this.accounts[index] : null;
                    if (account === null) {
                        return;
                    }
                    context.is_processing = true;
                    axios.put("/xhr/finance/accounts/" + account.id, {is_visible: account.is_visible ? 0 : 1})
                        .then(function (response) {
                            console.log(response);
                            context.is_processing = false;
                            context.accounts.splice(index, 1, response.data);
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
            },
            mounted: function () {

            }
        });
    </script>
@endsection