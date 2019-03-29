<div id="import-account-entries" class="modal">
    <form class="col s12" action="" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>{{ $importEntriesModal or 'Import Account Entries' }}</h4>
            <div class="row">
                <div class="col s12 m12">
                    <div class="input-field col s12 m4">
                        <select id="account" name="account" class="browser-default" required>
                            <optgroup v-for="account in accounts" :key="account.id" v-if="typeof account.sub_accounts !== 'undefined' && account.sub_accounts.data.length > 0"
                                      v-bind:label="account.display_name">
                                <option v-for="sub_account in account.sub_accounts.data"
                                        :key="sub_account.id" v-if="!hide_cash_and_bank || (hide_cash_and_bank && sub_account.name !== 'cash' && sub_account.name !== 'bank')"
                                        v-bind:value="sub_account.id">@{{ sub_account.display_name }} (@{{ sub_account.entry_type.title_case() }})</option>
                            </optgroup>
                            <option v-else v-bind:value="account.id">@{{ account.display_name }} (@{{ account.entry_type.title_case() }})</option>

                            <option v-if="accounts.length === 0 && accounts.length === 1"
                                    v-bind:value="accounts[0].id">@{{ accounts[0].display_name }} (@{{ accounts[0].entry_type.title_case() }})</option>
                        </select>
                    </div>
                    <div class="file-field input-field col s12 m8">
                        <div class="btn">
                            <span>Select CSV</span>
                            <input type="file" name="import_file" accept="text/csv" required>
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text">
                        </div>
                    </div>
                </div>
            </div>
            <p class="flow-text mt-8">
                Feel free to <a href="{{ cdn('samples/finance-entries.csv') }}" target="_blank">Download</a>
                our CSV template, add your data, then upload.
                <br>
                Boring stuff? Use TransTrak to automate your accounting process.
                <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="{{ route('apps.finance.transtrak') }}" >
                    Launch Transtrak
                </a>
            </p>
        </div>
        <div class="modal-footer">
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="action"
                    value="save_entries">Save Entries</button>
        </div>
    </form>
</div>