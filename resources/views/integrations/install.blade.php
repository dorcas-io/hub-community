@extends('layouts.app')
@section('body_main_content_body')
    <div class="container" id="integrations">
        <div class="row">
            <integration-installer class="m4" v-for="(integration, index) in integrations" :type="integration.type" :key="integration.name"
                         :image="integration.image_url" :name="integration.name" :index="index" :display_name="integration.display_name"
                         :description="integration.description" :configurations="integration.configurations" v-on:installed="installed"></integration-installer>

            <div class="col s12" v-if="showEmptyState">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        developer_board
                    @endslot
                    Yippie! There are no additional integrations to be installed.
                    @slot('buttons')
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
                integrations: {!! json_encode($availableIntegrations) !!}
            },
            computed: {
                showEmptyState: function () {
                    return this.integrations.length === 0;
                }
            },
            methods: {
                installed: function (index) {
                    this.integrations.splice(index, 1);
                }
            }
        });
    </script>
@endsection