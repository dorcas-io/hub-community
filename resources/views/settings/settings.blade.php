@extends('layouts.app')
@section('body_main_content_body')
    <div class="container">
        <div class="row">
            @if (empty($viewMode) || $viewMode === 'business')
                <div class="col s12 m4">
                    <div class="card hoverable">
                        <div class="card-content">
                            <span class="card-title">Company Information</span>
                            <p>Update your company profile information.</p>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('settings.business') }}">Edit</a>
                        </div>
                    </div>
                </div>
            @endif
            @if (!empty($viewMode) && in_array($viewMode, ['professional', 'vendor']))
                <div class="col s12 m4">
                    <div class="card hoverable">
                        <div class="card-content">
                            <span class="card-title">Bank Account</span>
                            <p>Update your bank account information.</p>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('settings.bank-account') }}">Manage</a>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col s12 m4">
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title">Personal Information</span>
                        <p>Update your personal profile information.</p>
                    </div>
                    <div class="card-action">
                        <a href="{{ route('settings.personal') }}">Edit</a>
                    </div>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title">Customisation</span>
                        <p>Customise Dorcas to suit your style.</p>
                    </div>
                    <div class="card-action">
                        <a href="{{ route('settings.customise') }}">Customise</a>
                    </div>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title">Security</span>
                        <p>Update your login password.</p>
                    </div>
                    <div class="card-action">
                        <a href="{{ route('settings.security') }}">Edit</a>
                    </div>
                </div>
            </div>
            @if (empty($viewMode) || $viewMode === 'business')
                <div class="col s12 m4">
                    <div class="card hoverable">
                        <div class="card-content">
                            <span class="card-title">Billing Settings</span>
                            <p>Update your billing preferences.</p>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('settings.billing') }}">Manage</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('body_js')

@endsection
