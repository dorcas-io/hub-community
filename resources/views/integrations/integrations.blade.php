@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="{{ route('integrations.install') }}">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">Add Integration</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="integrations">
        <div class="row">
            <integration-manager class="m4" v-for="(integration, index) in integrations" :type="integration.type" :key="integration.name"
                                   :image="integration.image_url" :name="integration.name" :id="integration.id" :display_name="integration.display_name"
                                   :description="integration.description" :configurations="integration.configurations"
                                 :index="index"
                                 v-on:remove-integration="removeIntegration"></integration-manager>
            <div class="col s12" v-if="showEmptyState">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        developer_board
                    @endslot
                    Add an external app integration to give Dorcas wings.
                    @slot('buttons')
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="{{ route('integrations.install') }}">
                            Add Integration
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
            el: '#integrations',
            data: {
                integrations: {!! !empty($integrations) ? json_encode($integrations) : '[]' !!}
            },
            computed: {
                showEmptyState: function () {
                    return this.integrations.length === 0;
                }
            },
            methods: {
                removeIntegration: function (index) {
                    console.log('Removing Index: ' + index);
                    this.integrations.splice(index, 1);
                }
            }
        });
    </script>
@endsection
