@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.ui-response-alert')
    <div class="container" id="business-profile">
        <div class="row">
            <div class="col s12 m6">
                <h5>Branding</h5>
                @if (!empty($company->logo))
                    <img src="{{ $company->logo }}" alt="img12" class="responsive-img mb-4">
                @endif
                <form class="col s12" action="" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col s12">
                            <div class="file-field input-field">
                                <div class="btn">
                                    <span>File</span>
                                    <input type="file" name="logo" accept="image/*" >
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" placeholder="Select business logo" />
                                    <small>We recommend a <strong>126x100</strong> logo, or similar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn cyan waves-effect waves-light left" type="submit" name="action" value="customise_logo">
                                Update Logo
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
            el: '#business-profile',
            data: {
                business: {!! json_encode($business) !!}
            }
        })
    </script>
@endsection
