@extends('layouts.app')
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row" id="employee-new">
            <form class="col s12" action="" method="post" v-on:submit.prevent="create">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col s12">
                        <div class="row mb-2">
                            <div class="input-field col s12 m3">
                                <input id="firstname" type="text" name="firstname" maxlength="30" v-model="employee.firstname"
                                       required class="validate {{ $errors->has('firstname') ? ' invalid' : '' }}">
                                <label for="firstname"  @if ($errors->has('firstname')) data-error="{{ $errors->first('name') }}" @endif>Firstname</label>
                            </div>
                            <div class="input-field col s12 m3">
                                <input id="lastname" type="text" name="lastname" v-model="employee.lastname"
                                       maxlength="30" class="validate {{ $errors->has('lastname') ? ' invalid' : '' }}">
                                <label for="lastname" @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif>Lastname</label>
                            </div>
                            <div class="input-field col s12 m3">
                                <input id="staff_code" type="text" name="staff_code" maxlength="30" v-model="employee.staff_code"
                                       class="validate {{ $errors->has('staff_code') ? ' invalid' : '' }}">
                                <label for="staff_code" class="active"  @if ($errors->has('staff_code')) data-error="{{ $errors->first('staff_code') }}" @endif>Staff ID</label>
                            </div>
                            <div class="input-field col s12 m3">
                                <input id="job_title" type="text" name="job_title" maxlength="80" v-model="employee.job_title"
                                       class="validate {{ $errors->has('job_title') ? ' invalid' : '' }}">
                                <label for="job_title" class="active"  @if ($errors->has('job_title')) data-error="{{ $errors->first('job_title') }}" @endif>Job Title</label>
                            </div>
                            <div class="input-field col s12 m3">
                                <input id="salary_amount" type="number" name="salary_amount" min="0" v-model="employee.salary_amount"
                                       required class="validate {{ $errors->has('salary_amount') ? ' invalid' : '' }}">
                                <label for="salary_amount"  @if ($errors->has('salary_amount')) data-error="{{ $errors->first('salary_amount') }}" @endif>Salary Amount</label>
                            </div>
                            <div class="input-field col s12 m3">
                                <select name="gender" id="gender" class="browser-default" v-model="employee.gender">
                                    <option value="" disabled>Select Gender</option>
                                    <option value="female">Female</option>
                                    <option value="male">Male</option>
                                </select>
                            </div>
                            <div class="input-field col s12 m3">
                                <input id="phone" type="text" name="phone" maxlength="30" v-model="employee.phone"
                                       class="validate {{ $errors->has('phone') ? ' invalid' : '' }}">
                                <label for="phone"  @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif>Phone</label>
                            </div>
                            <div class="input-field col s12 m3">
                                <input id="email" type="email" name="email" maxlength="80" v-model="employee.email"
                                       class="validate {{ $errors->has('email') ? ' invalid' : '' }}">
                                <label for="email" class="active"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif>Email</label>
                            </div>
                            @if (!empty($departments))
                                <div class="input-field col s12 m4">
                                    <select name="department" id="department" class="browser-default" v-model="employee.department">
                                        <option value="" disabled>Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            @if (!empty($locations))
                                <div class="input-field col s12 m8">
                                    <select name="location" id="location" class="browser-default" v-model="employee.location">
                                        <option value="" disabled>Select Location</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }} - {{ implode(', ', [$location->address1, $location->address2, $location->city]) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="row mt-5">
                            <div class="col s12">
                                <div class="progress" v-if="saving">
                                    <div class="indeterminate"></div>
                                </div>
                                <button type="submit" class="btn waves-effect waves-teal"
                                        v-if="!saving">Add Employee</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('body_js')
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
            el: '#employee-new',
            data: {
                saving: false,
                employee: {
                    firstname: "{{ old('firstname') }}",
                    lastname: "{{ old('lastname') }}",
                    phone: "{{ old('phone') }}",
                    email: "{{ old('email') }}",
                    staff_code: "{{ old('staff_code') }}",
                    job_title: "{{ old('job_title') }}",
                    salary_amount: "{{ old('salary_amount') }}",
                    salary_period: "month",
                    department: "{{ old('department') }}",
                    location: "{{ old('location') }}",
                    gender: "{{ old('gender') }}"
                }
            },
            methods: {
                reset: function () {
                    for (var key in this.employee) {
                        if (!this.employee.hasOwnProperty(key)) {
                            continue;
                        }
                        this.employee[key] = '';
                    }
                },
                create: function () {
                    this.saving = true;
                    var context = this;
                    axios.post("/xhr/business/employees", context.employee)
                        .then(function (response) {
                            console.log(response);
                            context.saving = false;
                            context.reset();
                            Materialize.toast('Successfully added the employee', 4000);
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
                            context.saving = false;
                            return swal("Delete Failed", message, "warning");
                        });
                }
            }
        });
    </script>
@endsection