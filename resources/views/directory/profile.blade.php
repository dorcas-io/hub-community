@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn dropdown-settings waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#!" data-activates="dropdown1" style="">
        <i class="material-icons hide-on-med-and-up">settings</i>
        <span class="hide-on-small-onl">Edit Profile</span>
        <i class="material-icons right">arrow_drop_down</i>
    </a>
    <ul id="dropdown1" class="dropdown-content">
        <li>
            <a href="#new-credential" class="grey-text text-darken-2 modal-trigger">Add Certification</a>
        </li>
        <li>
            <a href="#new-experience" class="grey-text text-darken-2 modal-trigger">Add Experience</a>
        </li>
        <li>
            <a href="#new-social-connection" class="grey-text text-darken-2 modal-trigger">Add Social Connection</a>
        </li>
        <li>
            <a href="#new-service" class="grey-text text-darken-2 modal-trigger">Add Service</a>
        </li>
    </ul>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="row" id="professional-profile">
        <div class="col s12">
            <ul class="tabs tabs-fixed-width z-depth-1">
                <li class="tab col s3"><a href="#credentials">Credentials</a></li>
                <li class="tab col s3"><a href="#experience">Experience</a></li>
                <li class="tab col s3"><a href="#social-connections">Social Connections</a></li>
                <li class="tab col s3"><a href="#services">Services</a></li>
            </ul>
            <div id="social-connections" class="col s12">
                <div class="row" v-if="typeof profile.extra_configurations.professional_social_contacts !== 'undefined' && profile.extra_configurations.professional_social_contacts !== null && profile.extra_configurations.professional_social_contacts.length > 0">
                    <professional-social-connection v-for="(connection, index) in profile.extra_configurations.professional_social_contacts"
                                                    :key="connection.id + '-' + connection.url" :connection="connection" class="m4"
                                                    :index="index" v-on:delete-connection="removeConnection">
                    </professional-social-connection>
                </div>
                <div class="col s12" v-if="typeof profile.extra_configurations.professional_social_contacts === 'undefined' || profile.extra_configurations.professional_social_contacts === null || profile.extra_configurations.professional_social_contacts.length === 0">
                    @component('layouts.slots.empty-fullpage')
                        @slot('icon')
                            settings_input_antenna
                        @endslot
                        Add social connection for people to learn more about you, and connect.
                        @slot('buttons')
                            <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger" href="#new-social-connection">
                                Add a Connection
                            </a>
                        @endslot
                    @endcomponent
                </div>
            </div>
            <div id="credentials" class="col s12">
                <div class="row" v-if="typeof profile.professional_credentials.data !== 'undefined' && profile.professional_credentials.data.length > 0">
                    <professional-credential v-for="(credential, index) in profile.professional_credentials.data"
                                                    :key="credential.id" :credential="credential" class="m6"
                                                    :index="index" v-on:delete-credential="removeCredential">

                    </professional-credential>
                </div>
                <div class="col s12" v-if="profile.professional_credentials.data.length === 0">
                    @component('layouts.slots.empty-fullpage')
                        @slot('icon')
                            school
                        @endslot
                        Add details about your certifications in your field.
                        @slot('buttons')
                            <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger" href="#new-credential">
                                Add a Certification
                            </a>
                        @endslot
                    @endcomponent
                </div>
            </div>
            <div id="experience" class="col s12">
                <div class="row" v-if="typeof profile.professional_experiences.data !== 'undefined' && profile.professional_experiences.data.length > 0">
                    <professional-experience v-for="(experience, index) in profile.professional_experiences.data"
                                             :key="experience.id" :experience="experience" class="m6"
                                             :index="index" v-on:delete-experience="removeExperience">

                    </professional-experience>
                </div>
                <div class="col s12" v-if="profile.professional_experiences.data.length === 0">
                    @component('layouts.slots.empty-fullpage')
                        @slot('icon')
                            query_builder
                        @endslot
                        Tell people more about the places you've worked.
                        @slot('buttons')
                            <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger" href="#new-experience">
                                Add Experience
                            </a>
                        @endslot
                    @endcomponent
                </div>
            </div>
            <div id="services" class="col s12">
                <div class="row" v-if="typeof profile.professional_services.data !== 'undefined' && profile.professional_services.data.length > 0 && viewMode === 'professional'">
                    <professional-service v-for="(service, index) in profile.professional_services.data"
                                             :key="service.id" :service="service" class="m6"
                                             :index="index"
                                          v-on:delete-service="removeService"
                                          v-on:edit-service="editService">

                    </professional-service>
                </div>
                <div class="row" v-if="typeof profile.vendor_services.data !== 'undefined' && profile.vendor_services.data.length > 0 && viewMode === 'vendor'">
                    <professional-service v-for="(service, index) in profile.vendor_services.data"
                                          :key="service.id" :service="service" class="m6"
                                          :index="index"
                                          v-on:delete-service="removeService"
                                          v-on:edit-service="editService">

                    </professional-service>
                </div>
                <div class="col s12" v-if="(viewMode === 'professional' && profile.professional_services.data.length === 0) || (viewMode === 'vendor' && profile.vendor_services.data.length === 0)">
                    @component('layouts.slots.empty-fullpage')
                        @slot('icon')
                            business_center
                        @endslot
                        Tell people what services you offer.
                        @slot('buttons')
                            <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger" href="#new-service">
                                Add a Service
                            </a>
                        @endslot
                    @endcomponent
                </div>
            </div>
        </div>
        @include('directory.modals.new-credential')
        @include('directory.modals.new-experience')
        @include('directory.modals.new-service')
        @include('directory.modals.new-social-connection')
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#professional-profile',
            data: {
                categories: {!! json_encode(!empty($categories) ? $categories : []) !!},
                profile: {!! json_encode(!empty($profile) ? $profile : []) !!},
                viewMode: '{{ empty($viewMode) ? 'business' : $viewMode }}',
                modals: {
                    social: {is_processing: false, channel: '', id: '', url: ''},
                    credential: {is_processing: false, title: '', type: '', year: '', description: '', certification: ''},
                    experience: {is_processing: false, company: '', designation: '', from_year: '', to_year: ''},
                    service: {is_processing: false, title: '', type: '', frequency: 'standard', currency: 'NGN', amount: 0.00, categories: [], extra_category: '', id: ''},
                }
            },
            methods: {
                addSocialConnection: function () {
                    this.modals.social.is_processing = true;
                    var context = this;
                    axios.post("/xhr/directory/social-connections", {
                        channel: context.modals.social.channel,
                        id: context.modals.social.id,
                        url: context.modals.social.url
                    }).then(function (response) {
                        console.log(response);
                        context.modals.social = {is_processing: false, channel: '', id: '', url: ''};
                        context.profile.extra_configurations = response.data.extra_configurations;
                        return swal("Success", "The social connection was successfully created.", "success");
                    }).catch(function (error) {
                            var message = '';
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
                            context.modals.social.is_processing = false;
                            return swal("Oops!", message, "warning");
                        });
                },
                removeConnection: function (index) {
                    this.profile.extra_configurations.professional_social_contacts.splice(index, 1);
                },
                addCredential: function () {
                    this.modals.credential.is_processing = true;
                    var context = this;
                    axios.post("/xhr/directory/credentials", context.modals.credential)
                        .then(function (response) {
                            console.log(response);
                            context.modals.credential = {is_processing: false, title: '', type: '', year: '', description: '', certification: ''};
                            context.profile.professional_credentials.data.push(response.data);
                            return swal("Success", "The credential was successfully created.", "success");
                        }).catch(function (error) {
                            var message = '';
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
                            context.modals.credential.is_processing = false;
                            return swal("Oops!", message, "warning");
                        });
                },
                removeCredential: function (index) {
                    this.profile.professional_credentials.data.splice(index, 1);
                },
                addExperience: function () {
                    this.modals.experience.is_processing = true;
                    var context = this;
                    axios.post("/xhr/directory/experiences", context.modals.experience)
                        .then(function (response) {
                            console.log(response);
                            context.modals.experience = {is_processing: false, company: '', designation: '', from_year: '', to_year: ''};
                            context.profile.professional_experiences.data.push(response.data);
                            return swal("Success", "The experience was successfully created.", "success");
                        }).catch(function (error) {
                        var message = '';
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
                        context.modals.experience.is_processing = false;
                        return swal("Oops!", message, "warning");
                    });
                },
                removeExperience: function (index) {
                    this.profile.professional_experiences.data.splice(index, 1);
                },
                editService: function (index) {
                    var reference = this.viewMode === 'vendor' ? this.profile.vendor_services : this.profile.professional_services;
                    if (typeof reference.data[index] === 'undefined') {
                        return;
                    }
                    var service = reference.data[index];
                    this.modals.service.id = service.id;
                    this.modals.service.title = service.title;
                    this.modals.service.type = service.cost_type;
                    this.modals.service.frequency = service.cost_frequency;
                    this.modals.service.currency = service.cost_currency;
                    this.modals.service.amount = service.cost_amount.raw;
                    this.modals.service.categories = [];
                    for (var i = 0; i < service.categories.data.length; i++) {
                        this.modals.service.categories.push(service.categories.data[i].id);
                    }
                    $('#new-service').modal('open');
                },
                addService: function () {
                    this.modals.service.is_processing = true;
                    var context = this;
                    context.modals.service.service_type = this.viewMode === 'vendor' ? 'vendor' : 'professional';
                    // set the service type
                    axios.post("/xhr/directory/services", context.modals.service)
                        .then(function (response) {
                            console.log(response);
                            var index = -1;
                            var reference = response.data.type === 'vendor' ? context.profile.vendor_services : context.profile.professional_services;
                            if (typeof response.data.id !== 'undefined') {
                                for (var i = 0; i < reference.data.length; i++) {
                                    if (reference.data[i].id !== response.data.id) {
                                        continue;
                                    }
                                    index = i;
                                    break;
                                }
                            }
                            if (index > -1) {
                                if (context.viewMode === 'vendor') {
                                    context.profile.vendor_services.data[index] = response.data;
                                } else {
                                    context.profile.professional_services.data[index] = response.data;
                                }
                            } else {
                                if (context.viewMode === 'vendor') {
                                    context.profile.vendor_services.data.push(response.data);
                                } else {
                                    context.profile.professional_services.data.push(response.data);
                                }
                            }
                            context.modals.service = {is_processing: false, title: '', type: '', frequency: 'standard', currency: 'NGN', amount: 0.00, categories: [], extra_category: '', id: ''};
                            return swal("Success", "The service was successfully created.", "success");
                        }).catch(function (error) {
                            var message = '';
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
                            context.modals.service.is_processing = false;
                            return swal("Oops!", message, "warning");
                    });
                },
                cancelServiceEdit: function () {
                    this.modals.service = {is_processing: false, title: '', type: '', frequency: 'standard', currency: 'NGN', amount: 0.00, categories: [], extra_category: '', id: ''};
                },
                removeService: function (index) {
                    this.profile.professional_services.data.splice(index, 1);
                },
            },
        })
    </script>
@endsection