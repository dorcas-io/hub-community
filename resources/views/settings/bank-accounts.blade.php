@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.ui-response-alert')
    <div class="container" id="bank-setup">
        <div class="row">
            <div class="col s12 m6">
                <h5>Bank Account Information</h5>
                <form class="col s12" action="" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col s12">
                            <div class="row">
                                <div class="input-field col s12">
                                    <select name="bank" id="bank" v-model="account.json_data.bank_code" required>
                                        <option v-for="bank in banks" :key="bank.code" v-bind:value="bank.code">@{{ bank.name }}</option>
                                    </select>
                                    <label for="bank" @if ($errors->has('bank')) data-error="{{ $errors->first('bank') }}" @endif>Bank</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="account_number" type="text" name="account_number" maxlength="30" v-model="account.account_number"
                                           required class="validate {{ $errors->has('account_number') ? ' invalid' : '' }}">
                                    <label for="account_number"  @if ($errors->has('account_number')) data-error="{{ $errors->first('account_number') }}" @endif>Account Number</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="account_name" type="text" name="account_name" v-model="account.account_name" maxlength="80"
                                           required class="validate {{ $errors->has('account_name') ? ' invalid' : '' }}">
                                    <label for="account_name"  @if ($errors->has('account_name')) data-error="{{ $errors->first('account_name') }}" @endif>Account Name</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn cyan waves-effect waves-light left" type="submit" name="action" value="save_account">
                                Save Account
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
            el: '#bank-setup',
            data: {
                account: {!! json_encode(!empty($account) ? $account : $default) !!},
                banks: {!! json_encode($banks) !!},
                user: {!! json_encode($dorcasUser) !!}
            }
        })
    </script>
@endsection
