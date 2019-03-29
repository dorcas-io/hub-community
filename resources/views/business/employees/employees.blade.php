@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="{{ route('business.employees.new') }}">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">Add Employee</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section" id="employee-list" v-on:click="clickAction">
            @if (!empty($employees) && $employees->count() > 0)
                <table class="bootstrap-table responsive-table"
                       data-page-list="[10,25,50,100,200,300,500]"
                       data-sort-class="sortable"
                       data-pagination="true"
                       data-search="true">
                    <thead>
                    <tr>
                        <th data-field="name" data-sortable="true" data-width="20%">Name</th>
                        <th data-field="job_title" data-sortable="true" data-width="10%">Job Title</th>
                        <th data-field="department.data.name" data-sortable="true" data-width="10%">Department</th>
                        <th data-field="staff_code" data-sortable="true" data-width="10%">Staff #</th>
                        <th data-field="phone" data-sortable="true" data-width="10%">Phone</th>
                        <th data-field="gender" data-sortable="true" data-width="5%">Gender</th>
                        <th data-field="salary.formatted" data-sortable="true" data-width="10%">Salary</th>
                        <th data-field="created_at" data-sortable="true" data-width="10%">Added On</th>
                        <th data-field="buttons" data-width="15%">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td>
                                <div class="row valign-wrapper">
                                    <div class="col s3">
                                        <img src="{{ $employee->photo }}" alt="" class="circle responsive-img">
                                    </div>
                                    <div class="col s9 no-padding">
                                        <span class="black-text">
                                            <a href="mailto:{{ $employee->email }}">{{ implode(' ', [$employee->firstname, $employee->lastname]) }}<i class="tiny material-icons">open_in_new</i> </a><br>
                                            <small>{{ str_limit($employee->email, 27) }}</small>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->job_title }}</td>
                            <td>{{ !empty($employee->department) ? $employee->department['data']['name'] : '-' }}</td>
                            <td>{{ $employee->staff_code }}</td>
                            <td>{{ $employee->phone }}</td>
                            <td>{{ title_case($employee->gender) }}</td>
                            <td>{{ $employee->salary['formatted'] }}<br><small>per {{ $employee->salary['period'] }}</small></td>
                            <td>{{ Carbon\Carbon::parse($employee->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <a class="btn-flat btn-small waves-effect waves-teal view" href="{{ route('business.employees.single', [$employee->id]) }}">
                                    View
                                </a>
                                <a class="btn-flat btn-small red-text waves-effect waves-red remove"
                                   data-id="{{ $employee->id }}" data-name="{{ $employee->firstname . ' ' . $employee->lastname }}">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="col s12">
                    @component('layouts.slots.empty-fullpage')
                        @slot('icon')
                            people_outline
                        @endslot
                        Add one or more of your staff members.
                        @slot('buttons')
                            <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="{{ route('business.employees.new') }}">
                                Add Employee
                            </a>
                        @endslot
                    @endcomponent
                </div>
            @endif
        </div>
    </div>
@endsection
@section('body_js')
    <script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $('input[type=checkbox].check-all').on('change', function () {
                var className = $(this).parent('div').first().data('item-class') || '';
                if (className.length > 0) {
                    $('input[type=checkbox].'+className).prop('checked', $(this).prop('checked'));
                }
            });
        });

        new Vue({
            el: '#employee-list',
            methods: {
                clickAction: function (event) {
                    console.log(event.target);
                    var target = event.target.tagName.toLowerCase() === 'i' ? event.target.parentNode : event.target;
                    var attrs = Hub.utilities.getElementAttributes(target);
                    // get the attributes
                    var classList = target.classList;
                    if (classList.contains('view')) {
                        return true;
                    } else if (classList.contains('remove')) {
                        this.delete(attrs);
                    }
                },
                delete: function (attributes) {
                    console.log(attributes);
                    var name = attributes['data-name'] || '';
                    var id = attributes['data-id'] || null;
                    if (id === null) {
                        return false;
                    }
                    context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to delete employee " + name,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/business/employees/" + id)
                            .then(function (response) {
                                console.log(response);
                                window.location = '{{ route('business.employees') }}';
                            })
                            .catch(function (error) {
                                var message = '';
                                console.log(error);
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
    </script>
@endsection