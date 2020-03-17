@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow modal-trigger"
       href="#add-employees">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">Add Employees</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="details-view">
        <div class="row section">
            <div class="col s12 m4">
                <div class="card">
                    <div class="card-image waves-effect waves-block waves-light">
                        <img class="activator" src="{{ cdn('images/gallery/2.png') }}">
                    </div>
                    <div class="card-content">
                        <span class="card-title activator grey-text text-darken-3">@{{ department.name }}<i class="material-icons right">edit</i></span>
                        <p>@{{ department.description }}</p>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">Edit Details<i class="material-icons right">close</i></span>
                        <form method="post" action="" v-on:submit.prevent="updateDetails">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="name" type="text" v-model="department.name" maxlength="80" required>
                                    <label for="name">Department Name</label>
                                </div>
                                <div class="input-field col s12">
                                    <textarea id="description" class="materialize-textarea" v-model="department.description">@{{ department.description }}</textarea>
                                    <label for="description">Description</label>
                                </div>
                                <div class="col s12">
                                    <div class="progress" v-if="updating">
                                        <div class="indeterminate"></div>
                                    </div>
                                    <button class="btn waves-effect waves-light" type="submit" name="action" v-if="!updating">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col s12 m8" v-on:click="clickAction">
                @if (!empty($department->employees['data']))
                    <table class="bootstrap-table responsive-table"
                           data-page-list="[10,25,50,100,200,300,500]"
                           data-sort-class="sortable"
                           data-pagination="true"
                           data-search="true">
                        <thead>
                        <tr>
                            <th data-field="name" data-sortable="true" data-width="25%">Name</th>
                            <th data-field="job_title" data-sortable="true" data-width="25%">Job Title</th>
                            <th data-field="staff_code" data-sortable="true" data-width="10%">Staff #</th>
                            <th data-field="gender" data-sortable="true" data-width="10%">Gender</th>
                            <th data-field="salary.formatted" data-sortable="true" data-width="10%">Salary</th>
                            <th data-field="buttons" data-width="20%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($department->employees['data'] as $employee)
                            <tr>
                                <td>
                                    <div class="row valign-wrapper">
                                        <div class="col s3">
                                            <img src="{{ $employee['photo'] }}" alt="" class="circle responsive-img">
                                        </div>
                                        <div class="col s9 no-padding">
                                        <span class="black-text">
                                            <a href="{{ route('business.employees.single', [$employee['id']]) }}">{{ implode(' ', [$employee['firstname'], $employee['lastname']]) }}<i class="tiny material-icons">open_in_new</i> </a><br>
                                            <small>{{ str_limit($employee['email'], 20) }}</small>
                                        </span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee['job_title'] }}</td>
                                <td>{{ $employee['staff_code'] }}</td>
                                <td>{{ title_case($employee['gender']) }}</td>
                                <td>{{ $employee['salary']['formatted'] }}</td>
                                <td>
                                    <a class="btn-flat btn-small red-text waves-effect waves-red remove"
                                       data-id="{{ $employee['id'] }}" data-name="{{ $employee['firstname'] . ' ' . $employee['lastname'] }}">
                                        Remove
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
                                domain
                            @endslot
                            You can add add members to this department from your employees.
                            @slot('buttons')
                                <a class="btn-flat blue darken-3 white-text waves-effect waves-light modal-trigger" href="#add-employees">
                                    Add Employees
                                </a>
                            @endslot
                        @endcomponent
                    </div>
                @endif
            </div>
        </div>
        @include('business.modals.add-employees')
    </div>
@endsection
@section('body_js')
    <script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}"></script>
    <script type="text/javascript">
        new Vue({
            el: '#details-views',
            data: {
                department: {!! json_encode($department) !!},
                updating: false,
                employeesCount: {{ empty($employees) ? 0 : $employees->count() }}
            },
            computed: {
                showAddButton: function () {
                    return !this.updating && this.employeesCount > 0;
                }
            },
            methods: {
                updateDetails: function () {
                    var context = this;
                    context.updating = true;
                    axios.put("/xhr/business/departments/" + context.department.id, {
                        name: context.department.name,
                        description: context.department.description
                    }).then(function (response) {
                        console.log(response);
                        context.updating = false;
                        return swal("Success", "Your changes were successfully saved.", "success");
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
                        context.updating = false;
                        return swal("Oops!", message, "warning");
                    });
                },
                clickAction: function (event) {
                    console.log(event.target);
                    var target = event.target.tagName.toLowerCase() === 'i' ? event.target.parentNode : event.target;
                    var attrs = Hub.utilities.getElementAttributes(target);
                    // get the attributes
                    var classList = target.classList;
                    if (classList.contains('view')) {
                        return true;
                    } else if (classList.contains('remove')) {
                        this.removeEmployee(attrs);
                    }
                },
                removeEmployee: function (attributes) {
                    console.log(attributes);
                    var name = attributes['data-name'] || '';
                    var id = attributes['data-id'] || null;
                    if (id === null) {
                        return false;
                    }
                    context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to remove " + name + " from the department.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, continue!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/business/departments/" + context.department.id + "/employees", {
                                data: {employees: [id]}
                            }).then(function (response) {
                                console.log(response);
                                window.location = '{{ route('business.departments.single', [$department->id]) }}';
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
                                return swal("Removal Failed", message, "warning");
                            });
                    });
                },
                addEmployees: function () {

                }
            }
        });
    </script>
@endsection