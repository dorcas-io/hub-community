@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section">
            <div class="col s12 m6 l6">
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title">Contacts Manager</span>
                        <p>
                            Create custom fields for customer contact information, so you store what
                            you need from your customers.
                        </p>
                    </div>
                    <div class="card-action">
                        <a class="waves-effect waves-light btn blue darken-3"
                           href="{{ route('apps.crm.customers') }}">
                            GO
                        </a>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l6">
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title">Customer Manager</span>
                        <p>
                            Manage your customers, their contact information, custom configured data, and
                            other relationship information.
                        </p>
                    </div>
                    <div class="card-action">
                        <a class="waves-effect waves-light btn blue darken-3"
                           href="{{ route('apps.crm.custom-fields') }}">
                            GO
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection