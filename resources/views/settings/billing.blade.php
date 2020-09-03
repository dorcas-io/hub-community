@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.ui-response-alert')
    <div class="container" id="billing-settings">
        <div class="row">
            <div class="col s12">
                <h5>&nbsp;</h5>
                <form class="col s12" action="" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col s12 m4">
                            <div class="input-field">
                                <select name="auto_billing" id="auto_billing" v-model="billing.auto_billing"
                                        class="validate {{ $errors->has('auto_billing') ? ' invalid' : '' }}">
                                    <option value="0">Turned Off</option>
                                    <option value="1">Turned On</option>
                                </select>
                                <label for="auto_billing" @if ($errors->has('auto_billing')) data-error="{{ $errors->first('auto_billing') }}" @endif>Would you lie to be billed automatically?</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn cyan waves-effect waves-light left" type="submit" name="action"
                                    value="save_billing">
                                Save Preferences
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
            el: '#billing-settings',
            data: {
                billing: {!! json_encode($billing) !!}
            }
        })
    </script>
@endsection
