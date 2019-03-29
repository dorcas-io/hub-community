@extends('layouts.app')
@section('head_css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
@endsection
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#" v-on:click.prevent="createEmail">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">New Email</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="listing">
        <div class="row mt-3" v-if="emails.length > 0">
            <webmail-account class="col s12 m4" v-for="(email, index) in emails"
                             :key="email.user" :email="email" :index="index"
                             v-on:delete-email="deleteEmail"></webmail-account>
        </div>
        <div class="col s12" v-if="emails.length === 0">
            @component('layouts.slots.empty-fullpage')
                @slot('icon')
                    email
                @endslot
                You can create one or more email accounts for your purchased domain.
                @slot('buttons')
                    @if (empty($domains) || $domains->count() === 0)
                        @if ($isOnPaidPlan)
                            <a class="btn-flat brown darken-3 white-text waves-effect waves-light"
                               href="{{ route('apps.ecommerce.domains') }}">
                                Buy a Domain
                            </a>
                        @else
                            <a class="btn-flat brown darken-3 white-text waves-effect waves-light"
                               href="{{ route('subscription') }}">
                                Upgrade &amp; buy a Domain
                            </a>
                        @endif
                    @elseif (!$isHostingSetup)
                        <form action="{{ route('apps.ecommerce.website') }}" method="post">
                            {{ csrf_field() }}
                            <button class="btn-flat grey darken-3 white-text waves-effect waves-light" name="action"
                                    value="setup_hosting">
                                Setup Hosting
                            </button>
                        </form>
                    @else
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="#"
                           v-on:click.prevent="createEmail">
                            Add Email Account
                        </a>
                    @endif
                @endslot
            @endcomponent
        </div>
        @include('ecommerce.modals.new-email')
    </div>
@endsection
@section('body_js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script type="text/javascript">
        var vm = new Vue({
            el: '#listing',
            data: {
                domains: {!! json_encode(!empty($domains) ? $domains : []) !!},
                emails: {!! json_encode(!empty($emails) ? $emails : []) !!},
                email: {username: '', domain: '', quota: 25}
            },
            methods: {
                createEmail: function () {
                    $('#manage-email-modal').modal('open');
                },
                deleteEmail: function (index) {
                    let email = typeof this.emails[index] !== 'undefined' ? this.emails[index] : null;
                    if (email === null) {
                        return;
                    }
                    let context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to delete email " + email.login,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/ecommerce/emails/" + email.user)
                            .then(function (response) {
                                console.log(response);
                                context.emails.splice(index, 1);
                                return swal("Deleted!", "The email was successfully deleted.", "success");
                            })
                            .catch(function (error) {
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
                                return swal("Delete Failed", message, "warning");
                            });
                    });
                }
            }
        });

        new Vue({
            el: '#header-button',
            data: {

            },
            methods: {
                createEmail: function () {
                    vm.createEmail();
                }
            }
        })
    </script>
@endsection