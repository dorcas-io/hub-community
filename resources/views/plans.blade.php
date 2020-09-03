@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.page-messages')     @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row">
            <div class="col s12">
                <section class="plans-container" id="plans">
                    <plan-chooser v-for="(plan, index) in plans" class="m4" :key="plan.profile.id"
                                  :index="index"
                                  :name="plan.name"
                                  :features="plan.features"
                                  :short_description="plan.description.short"
                                  :description="plan.description.long"
                                  :profile="plan.profile"></plan-chooser>
                </section>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#plans',
            data: {
                user: {!! json_encode($dorcasUser) !!},
                business: {!! json_encode($business) !!},
                plans: {!! json_encode($plans) !!}
            }
        })
    </script>
@endsection