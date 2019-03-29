@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow {{ $is_contact ? 'disabled' : '' }}"
       href="{{ route('directory.service', [$service->id]) . '?add_contact' }}">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">Add to Contacts</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.ui-response-alert')
    <div class="container" id="details-view">
        <div class="section row">
            <div class="col s12 m4">
                <div class="card">
                    <div class="card-content">
                        <h4 class="card-title activator grey-text text-darken-3">@{{ service.title }}<i class="material-icons right">info_outline</i></h4>
                        <h5>@{{ service.cost_currency }}@{{ service.cost_amount.formatted + (service.cost_frequency.toLowerCase() !== 'standard' ? ' per ' + service.cost_frequency : '') }}</h5>
                        <div class="chip" v-for="category in service.categories.data" :key="category.id">
                            <a class="black-text" v-bind:href="'{{ route('directory') . '?category_id=' }}' + category.id" target="_blank">
                                @{{ category.name.title_case() }} <i class="material-icons tiny">open_in_new</i>
                            </a>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">Professional Details<i class="material-icons right">close</i></span>
                        <p>
                            <strong>Name: </strong> @{{ service.user.data.firstname }} @{{ service.user.data.lastname }}<br>
                            <strong>Email: </strong> @{{ service.user.data.email }}<br>
                            <strong>Phone: </strong> @{{ service.user.data.phone }}<br>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col s12 m8">
                <ul class="tabs tabs-fixed-width z-depth-1">
                    <li class="tab col s6"><a href="#credentials">Credentials</a></li>
                    <li class="tab col s6"><a href="#experiences">Experience</a></li>
                    <li class="tab col s6"><a href="#request-service">Request Service</a></li>
                </ul>
                <div id="credentials" class="col s12">
                    <div class="row section" id="credentials-list" v-on:click="clickAction($event)">
                        <div class="card darken-1" v-for="credential in credentials" :key="credential.id">
                            <div class="card-content ">
                                <span class="card-title">@{{ credential.title }}</span>
                                <h5>@{{ credential.certification }} (@{{ credential.year }})</h5>
                                <p>@{{ credential.description }}</p>
                            </div>
                        </div>
                        <div class="col s12" v-if="credentials.length === 0">
                            @component('layouts.slots.empty-fullpage')
                                @slot('icon')
                                    school
                                @endslot
                                This professional has not added any certifications.
                                @slot('buttons') @endslot
                            @endcomponent
                        </div>
                    </div>
                </div>
                <div id="experiences" class="col s12">
                    <div class="row section" id="experiences-list">
                        <div class="card darken-1" v-for="experience in experiences" :key="experience.id">
                            <div class="card-content ">
                                <span class="card-title">@{{ experience.company }} (@{{ experience.from_year + ' - ' + (experience.is_current ? 'Present' : experience.to_year) }})</span>
                                <h5>@{{ experience.designation }}</h5>
                            </div>
                        </div>
                        <div class="col s12" v-if="experiences.length === 0">
                            @component('layouts.slots.empty-fullpage')
                                @slot('icon')
                                    domain
                                @endslot
                                This professional has not added any work experience information.
                                @slot('buttons') @endslot
                            @endcomponent
                        </div>
                    </div>
                </div>
                <div id="request-service" class="col s12">
                    <div class="row section">
                        <form class="col s12" action="" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col s12">
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <textarea class="materialize-textarea" name="message" required></textarea>
                                            <label for="message" class="active">Personal Message</label>
                                        </div>
                                        <div class="file-field input-field col s12 m6">
                                            <div class="btn">
                                                <span>File</span>
                                                <input type="file" name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,image/*" >
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text" placeholder="Supporting Documents, if any" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <button class="btn cyan waves-effect waves-light left" type="submit" name="action">Send Request
                                        <i class="material-icons left">send</i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}"></script>
    <script type="text/javascript">

        $(function(){
            $('.materialboxed').materialbox();
        });

        new Vue({
            el: '#details-view',
            data: {
                is_contact: {!! json_encode($is_contact) !!},
                service: {!! json_encode($service) !!},
                credentials: {!! json_encode(!empty($service->user['data']['professional_credentials']) ? $service->user['data']['professional_credentials']['data'] : [] ) !!},
                experiences: {!! json_encode(!empty($service->user['data']['professional_experiences']) ? $service->user['data']['professional_experiences']['data'] : [] ) !!},
            }
        })

    </script>
@endsection