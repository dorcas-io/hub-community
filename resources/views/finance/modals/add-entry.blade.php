<div id="add-account-entry" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>{{ $addEntryModalTitle or 'Add Account Entry' }}</h4>
            <div class="row">
                <div class="col s12 m12">
                    <div class="input-field col s12 m4">
                        <select id="account" name="account" class="browser-default" required>
                            <optgroup v-for="account in filteredAccounts" :key="account.id"
                                      v-bind:label="account.display_name">
                                <option v-for="sub_account in account.sub_accounts.data"
                                        :key="sub_account.id" v-if="!hide_cash_and_bank || (hide_cash_and_bank && sub_account.name !== 'cash' && sub_account.name !== 'bank')"
                                        v-bind:value="sub_account.id">@{{ sub_account.display_name }} (@{{ sub_account.entry_type.title_case() }})</option>
                            </optgroup>
                            <option v-if="filteredAccounts.length === 0 && accounts.length === 1"
                                    v-bind:value="accounts[0].id">@{{ accounts[0].display_name }} (@{{ accounts[0].entry_type.title_case() }})</option>
                        </select>
                    </div>
                    <div class="input-field col s12 m4">
                        <select id="currency" name="currency" v-model="defaultCurrency" required>
                            @foreach ($isoCurrencies as $currency)
                                <option value="{{ $currency['alphabeticCode'] }}">{{ $currency['currency'] }} - {{ $currency['alphabeticCode'] }}</option>
                            @endforeach
                        </select>
                        <label for="currency">Currency</label>
                    </div>
                    <div class="input-field col s12 m4">
                        <input id="amount" type="number" name="amount" step="0.01" min="0" required="required">
                        <label for="amount">Amount</label>
                    </div>
                    <div class="input-field col s12 m8">
                        <input id="memo" name="memo" maxlength="300" type="text">
                        <label for="memo">Memo</label>
                    </div>
                    <div class="input-field col s12 m4">
                        <input type="text" class="custom-datepicker" name="created_at" id="created_at">
                        <label for="created_at">Transaction Date</label>
                    </div>
                    <input type="hidden" name="source_type" id="source_type" value="manual">
                    <input type="hidden" name="source_info" id="source_info" value="Dorcas Hub">
                    <input type="hidden" name="double_entry_type" id="double_entry_type" v-model="entry_type">
                    <input type="hidden" name="double_entry_period" id="double_entry_period" v-model="entry_period">
                </div>
            </div>
            <p class="flow-text">
                Boring stuff? Use TransTrak to automate your accounting process.
                <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="{{ route('apps.finance.transtrak') }}" >
                    Launch Transtrak
                </a>
            </p>
        </div>
        <div class="modal-footer">
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="action"
                    value="save_entry">Save Entry</button>
        </div>
    </form>
</div>