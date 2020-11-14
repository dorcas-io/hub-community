@extends('layouts.app')
@section('body_main_content_body')
    <div class="container">
        <div class="row section">
            <div class="col s12 m4 l4">
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title">Departments</span>
                        <p>
                            Manage the departments in your company.
                        </p>
                    </div>
                    <div class="card-action">
                        <a class="waves-effect waves-light btn blue darken-3"
                           href="{{ route('business.departments') }}">
                            Manage
                        </a>
                    </div>
                </div>
            </div>
            <div class="col s12 m4 l4">
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title">Employees</span>
                        <p>
                            Manage the team members information.
                        </p>
                    </div>
                    <div class="card-action">
                        <a class="waves-effect waves-light btn blue darken-3"
                           href="{{ route('business.employees') }}">
                            Manage
                        </a>
                    </div>
                </div>
            </div>
            <div class="col s12 m4 l4">
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title">Teams</span>
                        <p>
                            Manage the staff teams in your company.
                        </p>
                    </div>
                    <div class="card-action">
                        <a class="waves-effect waves-light btn blue darken-3"
                           href="{{ route('business.teams') }}">
                            Manage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('help_modal_content')
    @component('layouts.slots.video-embed')
        //www.youtube.com/embed/WNgsHWOpoF4?rel=0
    @endcomponent
@endsection