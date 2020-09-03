@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#" v-on:click.prevent="addNew" id="button-add-new">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">Add Department</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container hopscotch-tour-box" data-tour-name="business_departments">
        <div class="row section" id="departments-list" v-on:click="clickAction">
            @if (!empty($departments) && $departments->count() > 0)
                <table class="bootstrap-table responsive-table"
                       data-page-list="[10,25,50,100,200,300,500]"
                       data-sort-class="sortable"
                       data-pagination="true"
                       data-search="true">
                    <thead>
                    <tr>
                        <th data-field="name" data-sortable="true" data-width="20%">Name</th>
                        <th data-field="description" data-sortable="true" data-width="45%">Description</th>
                        <th data-field="counts.employees" data-sortable="true" data-width="5%">Employees</th>
                        <th data-field="created_at" data-sortable="true" data-width="15%">Added On</th>
                        <th data-field="buttons" data-width="15%">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($departments as $department)
                        <tr>
                            <td>{{ $department->name }}</td>
                            <td>{{ str_limit($department->description) }}</td>
                            <td>{{ $department->counts['employees'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($department->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <a class="btn-flat btn-small waves-effect waves-teal view" href="{{ route('business.departments.single', [$department->id]) }}">
                                    View
                                </a>
                                <a class="btn-flat btn-small red-text waves-effect waves-red remove"
                                   data-id="{{ $department->id }}" data-name="{{ $department->name }}">
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
                            domain
                        @endslot
                        Add one or more department to group your employees.
                        @slot('buttons')
                            <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="#" v-on:click.prevent="addNew">
                                Add Department
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

        function createDepartment(name) {
            axios.post("/xhr/business/departments", {
                name: name
            }).then(function (response) {
                console.log(response);
                window.location = '{{ route('business.departments') }}';
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
                return swal("Oops!", message, "warning");
            });
        }

        new Vue({
            el: '#breadcrumbs-wrapper',
            methods: {
                addNew: function () {
                    swal({
                            title: "Add Department",
                            text: "",
                            type: "input",
                            showCancelButton: true,
                            closeOnConfirm: false,
                            animation: "slide-from-top",
                            showLoaderOnConfirm: true,
                            inputPlaceholder: "Department Name"
                        },
                        function(inputValue){
                            if (inputValue === false) return false;
                            if (inputValue === "") {
                                swal.showInputError("Enter a department name.");
                                return false
                            }
                            createDepartment(inputValue);
                        });
                }
            }
        });
        new Vue({
            el: '#departments-list',
            data: {

            },
            computed: {

            },
            methods: {
                addNew: function () {
                    swal({
                            title: "Add Department",
                            text: "",
                            type: "input",
                            showCancelButton: true,
                            closeOnConfirm: false,
                            animation: "slide-from-top",
                            showLoaderOnConfirm: true,
                            inputPlaceholder: "Department Name"
                        },
                        function(inputValue){
                            if (inputValue === false) return false;
                            if (inputValue === "") {
                                swal.showInputError("Enter a department name.");
                                return false
                            }
                            createDepartment(inputValue);
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
                        text: "You are about to delete department " + name,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/business/departments/" + id)
                            .then(function (response) {
                                console.log(response);
                                window.location = '{{ route('business.departments') }}';
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