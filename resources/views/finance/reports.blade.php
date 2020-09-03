@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="{{ route('apps.finance.reports.configure') }}">
        <i class="material-icons hide-on-med-and-up">add</i>
        <span class="hide-on-small-onl">Configure a Report</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section" id="accounting-reports">
            <div class="col s12 m4" v-for="config in configurations" :key="config.id">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">@{{ config.display_name }}</span>
                        <p class="flow-text">Configured report.</p>
                    </div>
                    <div class="card-action">
                        <a class="activator black-text" href="#" style="margin-right: 12px;">Accounts</a>
                        <a class="black-text" v-bind:href="'{{ route('apps.finance.reports.configure') }}/' + config.id" style="margin-right: 12px;">Edit</a>
                        <!--<a class="black-text" v-bind:href="'{{ route('apps.finance.reports') }}/' + config.id">Reports</a>-->
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">Configured Accounts<i class="material-icons right">close</i></span>
                        <ul>
                            <li v-for="account in config.accounts.data" :key="'report-' + config.id + '-account-' + account.id">
                                @{{ account.display_name }} (@{{ account.entry_type }})
                                <span v-if="typeof account.parent_account.data !== 'undefined'">- @{{ account.parent_account.data.display_name }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col s12" v-if="configurations.length === 0">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        event
                    @endslot
                    Dorcas makes it easy to create Accounting reports.
                    @slot('buttons')
                        <a class="btn-flat grey darken-3 white-text waves-effect waves-light modal-trigger"
                           href="{{ route('apps.finance.reports.configure') }}">
                            Configure a Report
                        </a>
                    @endslot
                @endcomponent
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#accounting-reports',
            data: {
                configurations: {!! json_encode($configurations ?: []) !!}
            },
            methods: {
                createBalanceSheet: function () {

                }
            }
        });
    </script>
@endsection