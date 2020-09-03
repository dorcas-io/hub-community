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