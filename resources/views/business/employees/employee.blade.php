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
    <div class="container" id="profile-view">
        <div class="row section">
            <div class="col s12 m4">
                <div id="profile-card" class="card">
                    <div class="card-image waves-effect waves-block waves-light">
                        <img class="activator" src="{{ cdn('images/gallery/3.png') }}" alt="user bg">
                    </div>
                    <div class="card-content">
                        <img v-bind:src="employee.photo" alt="" class="circle responsive-img activator card-profile-image cyan lighten-1 padding-2">
                        <a class="btn-floating activator btn-move-up waves-effect waves-light red accent-2 z-depth-4 right">
                            <i class="material-icons">edit</i>
                        </a>
                        <h5 class="card-title activator grey-text text-darken-4">@{{ employee.firstname }} @{{ employee.lastname }}</h5>
                        <p><i class="material-icons">perm_identity</i> @{{ employee.job_title }}</p>
                        <p v-if="displayDepartment"><i class="material-icons">domain</i> @{{ employee.department.data.name }}</p>
                        <p><i class="material-icons">perm_phone_msg</i> @{{ employee.phone }}</p>
                        <p><i class="material-icons">email</i> @{{ employee.email }}</p>
                        <div class="card-action pt-0" v-if="displayTeams">
                            <p>Teams</p>
                            <div class="chip" v-for="(team, index) in employee.teams.data" :key="team.id">
                                @{{ team.name }}
                                <i class="close material-icons" data-ignore-click="true" v-bind:data-index="index"
                                   v-bind:data-id="team.id" v-bind:data-name="team.name"
                                   v-on:click.prevent="removeTeam($event)">close</i>
                            </div>
                        </div>
                        <div class="progress" v-if="saving">
                            <div class="indeterminate"></div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">@{{ employee.firstname }} @{{ employee.lastname }}
                          <i class="material-icons right">close</i>
                        </span>
                        <form method="post" action="" v-on:submit.prevent="updateDetails">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <input id="firstname" type="text" v-model="employee.firstname" maxlength="30" required>
                                    <label for="firstname">Firstname</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="lastname" type="text" v-model="employee.lastname" maxlength="30" required>
                                    <label for="lastname">Lastname</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="job_title" type="text" v-model="employee.job_title" maxlength="80">
                                    <label for="job_title">Job Title</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="staff_code" type="text" v-model="employee.staff_code" maxlength="30">
                                    <label for="staff_code">Staff ID</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="salary_amount" type="number" min="0" v-model="employee.salary.raw">
                                    <label for="salary_amount">Salary Amount</label>
                                </div>
                                <div class="input-field col s12 m6">
                                    <input id="phone" type="text" maxlength="30" v-model="employee.phone">
                                    <label for="phone">Phone</label>
                                </div>
                                <p>
                                    <input type="radio" id="gender-female" value="female" v-model="employee.gender" name="gender" />
                                    <label for="gender-female">Female</label>
                                    <input type="radio" id="gender-male" value="male" v-model="employee.gender" name="gender" />
                                    <label for="gender-male">Male</label>
                                </p>
                                <div class="input-field col s12">
                                    <input id="email" type="email" maxlength="80" v-model="employee.email">
                                    <label for="email">Email</label>
                                </div>
                                <div class="col s12">
                                    <div class="progress" v-if="saving">
                                        <div class="indeterminate"></div>
                                    </div>
                                    <button class="btn waves-effect waves-light" type="submit" name="action" v-if="!saving">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col s12 m8">
                @if ($isOnPremiumPlan)
                    <div class="card">
                        <div class="card-content">
                            <p>You can easily perform various actions on this employee using the actions below</p>
                        </div>
                        <div class="card-tabs">
                            <ul class="tabs tabs-fixed-width">
                                <li class="tab" v-bind:class="{'disabled': typeof employee.user !== 'undefined'}">
                                    <a v-bind:class="{'active': typeof employee.user === 'undefined'}" href="#grant-access">Grant User Access</a>
                                </li>
                                <li class="tab" v-bind:class="{'disabled': typeof employee.user === 'undefined'}">
                                    <a v-bind:class="{'active': typeof employee.user !== 'undefined'}" href="#module-access">Restrict Module Access</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-content grey lighten-4">
                            <div id="grant-access" v-bind:class="{'active': typeof employee.user === 'undefined'}">
                                <form action="" method="post">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="input-field col s12 m6">
                                            <input autocomplete="off" required name="email" type="email" maxlength="80" v-model="employee.email"
                                                   class="validate {{ $errors->has('email') ? ' invalid' : '' }}" id="email">
                                            <label for="email"  @if ($errors->has('email')) data-error="{{ $errors->first('email') }}" @endif class="center-align">Email</label>
                                        </div>
                                        <div class="input-field col s12 m6">
                                            <input autocomplete="off" required name="password" type="password"
                                                   class="validate {{ $errors->has('password') ? ' invalid' : '' }}" id="password">
                                            <label for="password" @if ($errors->has('password'))data-error="{{ $errors->first('password') }}"@endif>Password</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12 m6">
                                            <input autocomplete="off" required name="firstname" type="text" maxlength="100" v-model="employee.firstname"
                                                   class="validate {{ $errors->has('firstname') ? ' invalid' : '' }}" id="firstname">
                                            <label for="firstname"  @if ($errors->has('firstname')) data-error="{{ $errors->first('firstname') }}" @endif class="center-align">Firstname</label>
                                        </div>
                                        <div class="input-field col s12 m6">
                                            <input autocomplete="off" required name="lastname" type="text" maxlength="30" v-model="employee.lastname"
                                                   class="validate {{ $errors->has('lastname') ? ' invalid' : '' }}" id="lastname">
                                            <label for="lastname"  @if ($errors->has('lastname')) data-error="{{ $errors->first('lastname') }}" @endif class="center-align">Lastname</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12 m6">
                                            <input autocomplete="off" name="phone" type="text" maxlength="30" v-model="employee.phone"
                                                   class="validate {{ $errors->has('phone') ? ' invalid' : '' }}" id="phone">
                                            <label for="phone"  @if ($errors->has('phone')) data-error="{{ $errors->first('phone') }}" @endif class="center-align">Phone</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <button class="btn waves-effect waves-light" type="submit" name="action"
                                                value="create_user">
                                            Add User
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div id="module-access" v-bind:class="{'active': typeof employee.user !== 'undefined'}">
                                <form action="" method="post">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        @foreach ($setupUiFields as $field)
                                            <div class="col s12 m6">
                                                <div class="card">
                                                    <div class="card-content">
                                                        <span class="card-title">{{ $field['name'] }}</span>
                                                        <div class="switch">
                                                            <label>
                                                                Disabled
                                                                <input type="checkbox" name="selected_apps[]"
                                                                       value="{{ $field['id'] }}" {{ !empty($field['enabled']) ? 'checked' : '' }}
                                                                        {{ !empty($field['is_readonly']) ? 'disabled' : '' }}>
                                                                <span class="lever"></span>
                                                                Enabled
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="row">
                                        <button class="btn waves-effect waves-light" type="submit" name="action"
                                                value="update_module_access">
                                            Save Module Access
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
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
            el: '#profile-views',
            data: {
                employee: {!! json_encode($employee) !!},
                saving: false
            },
            computed: {
                displayTeams: function () {
                    return typeof this.employee.teams !== 'undefined' && this.employee.teams.data.length > 0;
                },
                displayDepartment: function () {
                    return typeof this.employee.department !== 'undefined';
                }
            },
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
                },
                updateDetails: function () {
                    var context = this;
                    context.saving = true;
                    axios.put("/xhr/business/employees/" + context.employee.id, {
                        firstname: context.employee.firstname,
                        lastname: context.employee.lastname,
                        job_title: context.employee.job_title,
                        staff_code: context.employee.staff_code,
                        gender: context.employee.gender,
                        salary_amount: context.employee.salary.raw,
                        phone: context.employee.phone,
                        email: context.employee.email
                    }).then(function (response) {
                        console.log(response);
                        context.saving = false;
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
                        context.saving = false;
                        return swal("Oops!", message, "warning");
                    });
                },
                removeTeam: function (e) {
                    var attrs = Hub.utilities.getElementAttributes(e.target);
                    console.log(attrs);
                    var index = attrs['data-index'] || null;
                    var name = attrs['data-name'] || null;
                    var id = attrs['data-id'] || null;
                    if (id === null) {
                        return false;
                    }
                    if (this.saving) {
                        Materialize.toast('Please wait till the current activity completes...', 4000);
                        return;
                    }
                    this.saving = true;
                    var context = this;
                    axios.delete("/xhr/business/employees/" + context.employee.id + "/teams", {
                        data: {teams: [id]}
                    }).then(function (response) {
                            console.log(response);
                            console.log(index);
                            if (index !== null) {
                                context.employee.teams.data.splice(index, 1);
                            }
                            context.saving = false;
                            Materialize.toast('Team '+name+' removed for '+context.employee.firstname, 2000);
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