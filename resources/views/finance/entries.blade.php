@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn dropdown-settings waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#!" data-activates="dropdown1" style="">
        <i class="material-icons hide-on-med-and-up">add</i>
        <span class="hide-on-small-onl">Entry Actions</span>
        <i class="material-icons right">arrow_drop_down</i>
    </a>
    <ul id="dropdown1" class="dropdown-content">
        <li>
            <a href="#add-account-entry" class="grey-text text-darken-2 modal-trigger">Add a New Entry</a>
        </li>
        <li>
            <a href="#import-account-entries" class="grey-text text-darken-2 modal-trigger">Import Entries From CSV</a>
        </li>
        <li v-if="accounts.length > 1">
            <a href="#" class="grey-text text-darken-2 modal-trigger"
               v-on:click.prevent="setPresentEntry('debit')">Real Debit/Expense</a>
        </li>
        <li v-if="accounts.length > 1">
            <a href="#" class="grey-text text-darken-2 modal-trigger"
               v-on:click.prevent="setFutureEntry('debit')">Future Debit/Expense</a>
        </li>
        <li v-if="accounts.length > 1">
            <a href="#" class="grey-text text-darken-2 modal-trigger"
               v-on:click.prevent="setPresentEntry('credit')">Real Credit/Income</a>
        </li>
        <li v-if="accounts.length > 1">
            <a href="#" class="grey-text text-darken-2 modal-trigger"
               v-on:click.prevent="setFutureEntry('credit')">Future Credit/Income</a>
        </li>
    </ul>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section" id="entries" v-on:click="clickAction($event)">
            <table class="bootstrap-table responsive-table" v-if="entriesCount > 0"
                   data-url="{{ url('/xhr/finance/entries') . '?' . http_build_query($args) }}"
                   data-page-list="[10,25,50,100,200,300,500]"
                   data-row-attributes="Hub.Table.formatAccountingEntries"
                   data-side-pagination="server"
                   data-show-refresh="true"
                   data-sort-class="sortable"
                   data-pagination="true"
                   data-search="true"
                   data-unique-id="id"
                   data-search-on-enter-key="true"
                   id="entries-table">
                <thead>
                <tr>
                    <th data-field="account_link">Account</th>
                    <th data-field="entry_type">Type</th>
                    <th data-field="currency">Currency</th>
                    <th data-field="amount.formatted">Amount</th>
                    <th data-field="source_info">Source</th>
                    <th data-field="memo" data-width="25%">Memo</th>
                    <th data-field="created_at" data-width="10%">Added On</th>
                    <th data-field="buttons">&nbsp;</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <div class="col s12" v-if="entriesCount === 0">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        assistant
                    @endslot
                    Add a new account entry to your records.
                    @slot('buttons')
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light"
                           href="#" v-on:click.prevent="setPresentEntry('debit')"
                           v-if="(accounts.length === 1 && accounts[0].entry_type === 'debit') || accounts.length > 1">
                            Real Debit/Expense
                        </a>
                        <a class="btn-flat grey darken-3 white-text waves-effect waves-light modal-trigger"
                           href="#" v-on:click.prevent="setFutureEntry('debit')"
                           v-if="(accounts.length === 1 && accounts[0].entry_type === 'debit') || accounts.length > 1">
                            Future Debit/Expense
                        </a>
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light"
                           href="#" v-on:click.prevent="setPresentEntry('credit')"
                           v-if="(accounts.length === 1 && accounts[0].entry_type === 'credit') || accounts.length > 1">
                            Real Credit/Income
                        </a>
                        <a class="btn-flat grey darken-3 white-text waves-effect waves-light"
                           href="#" v-on:click.prevent="setFutureEntry('credit')"
                           v-if="(accounts.length === 1 && accounts[0].entry_type === 'credit') || accounts.length > 1">
                            Future Credit/Income
                        </a>
                        <p class="flow-text">
                            Boring stuff? Use TransTrak to automate your accounting entries.
                            <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="{{ route('apps.finance.transtrak') }}" >
                                Launch Transtrak
                            </a>
                        </p>
                    @endslot
                @endcomponent
            </div>
        </div>
        @include('finance.modals.add-entry')
        @include('finance.modals.import-entries')
    </div>
@endsection
@section('body_js')
    <script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('input[type=checkbox].check-all').on('change', function () {
                var className = $(this).parent('div').first().data('item-class') || '';
                if (className.length > 0) {
                    $('input[type=checkbox].'+className).prop('checked', $(this).prop('checked'));
                }
            });
            $('.custom-datepicker').pickadate({
                today: 'Today',
                clear: 'Clear',
                close: 'Ok',
                closeOnSelect: true,
                format: 'yyyy-mm-dd',
                container: 'body',
                max: true
            });
            $('#add-account-entry').modal({
                complete: function () {
                    vmModal.allowedAccounts = [];
                }
            });
        });
        var vmModal = new Vue({
            el: '#add-account-entry',
            data: {
                allowedAccounts: [],
                accounts: {!! json_encode($accounts) !!},
                hide_cash_and_bank: false,
                entry_type: '',
                entry_period: 'present',
                defaultCurrency: '',
                ui_configuration: {!! json_encode($UiConfiguration) !!}
            },
            mounted: function () {
                if (typeof this.ui_configuration.currency !== 'undefined') {
                    this.defaultCurrency = this.ui_configuration.currency;
                } else {
                    this.defaultCurrency = 'NGN';
                }
            },
            computed: {
                filteredAccounts: function () {
                    var context = this;
                    if (this.accounts.length === 1 && (typeof this.accounts[0].sub_accounts !== 'undefined' || this.accounts[0].sub_accounts.data.length === 0 )) {
                        return [];
                    }
                    return this.accounts.filter(function (account) {
                        return typeof account.sub_accounts !== 'undefined' && account.sub_accounts.data.length > 0 &&
                            (context.allowedAccounts.length === 0 || (context.allowedAccounts.length > 0 && context.allowedAccounts.indexOf(account.id) !== -1));
                    });
                }
            }
        });
        var vmImportModal = new Vue({
            el: '#import-account-entries',
            data: {
                allowedAccounts: [],
                accounts: {!! json_encode($accounts) !!},
                hide_cash_and_bank: false,
                entry_type: '',
                entry_period: 'present',
            },
            mounted: function () {

            },
            computed: {
                filteredAccounts: function () {
                    var context = this;
                    if (this.accounts.length === 1 && (typeof this.accounts[0].sub_accounts !== 'undefined' || this.accounts[0].sub_accounts.data.length === 0 )) {
                        return [];
                    }
                    return this.accounts.filter(function (account) {
                        return typeof account.sub_accounts !== 'undefined' && account.sub_accounts.data.length > 0 &&
                            (context.allowedAccounts.length === 0 || (context.allowedAccounts.length > 0 && context.allowedAccounts.indexOf(account.id) !== -1));
                    });
                }
            }
        });
        var vmDropdown = new Vue({
            el: '#dropdown1',
            data: {
                accounts: vmModal.accounts
            },
            methods: {
                filterAccounts: function (type) {
                    console.log('Filtering for: ' + type);
                    var filtered = vmModal.accounts.filter(function (account) {
                        return account.entry_type === type;
                    });
                    vmModal.allowedAccounts = filtered.map(function (account) {
                        return account.id;
                    });
                    vmModal.hide_cash_and_bank = type === 'debit';
                    vmModal.entry_type = type;
                },
                setFutureEntry: function (type) {
                    type = type === 'credit' || type === 'debit' ? type : '';
                    if (type.length === 0) {
                        return;
                    }
                    vmModal.entry_period = 'future';
                    this.filterAccounts(type);
                    $('#add-account-entry').modal('open');
                },
                setPresentEntry: function (type) {
                    type = type === 'credit' || type === 'debit' ? type : '';
                    if (type.length === 0) {
                        return;
                    }
                    vmModal.entry_period = 'present';
                    this.filterAccounts(type);
                    $('#add-account-entry').modal('open');
                }
            }
        });
        new Vue({
            el: '#entries',
            data: {
                entriesCount: {{ $entriesCount }},
                accounts: {!! json_encode($accounts) !!}
            },
            methods: {
                clickAction: function (event) {
                    console.log(event.target);
                    var target = event.target.tagName.toLowerCase() === 'i' ? event.target.parentNode : event.target;
                    var attrs = Hub.utilities.getElementAttributes(target);
                    // get the attributes
                    var classList = target.classList;
                    if (classList.contains('view')) {
                        return true;
                    } else if (classList.contains('remove')) {
                        this.delete(attrs);
                    }
                },
                delete: function (attributes) {
                    console.log(attributes);
                    var id = attributes['data-id'] || null;
                    if (id === null) {
                        return false;
                    }
                    var context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to delete this accounting entry from the records.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/finance/entries/" + id)
                            .then(function (response) {
                                console.log(response);
                                $('#entries-table').bootstrapTable('removeByUniqueId', response.data.id);
                                return swal("Deleted!", "The entry was successfully deleted.", "success");
                            })
                            .catch(function (error) {
                                var message = '';
                                console.log(error);
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
                                return swal("Delete Failed", message, "warning");
                            });
                    });
                },
                setFutureEntry: function (type) {
                    vmDropdown.setFutureEntry(type);
                },
                setPresentEntry: function (type) {
                    vmDropdown.setPresentEntry(type);
                }
            }
        });
    </script>
@endsection