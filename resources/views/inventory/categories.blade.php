@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#" v-on:click.prevent="newField">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">Add Category</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container">
        <div class="row section" id="product-categories">
            <product-category v-for="(category, index) in categories" class="m4 l4" :key="category.id"
                              :index="index" :category="category"
                           v-bind:show-delete="true" v-on:update="update"
                           v-on:remove="decrement"></product-category>

            <div class="col s12" v-if="categories.length  === 0">
                @component('layouts.slots.empty-fullpage')
                    @slot('icon')
                        storage
                    @endslot
                    Add one or more categories to classify your inventory.
                    @slot('buttons')
                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="#"
                           v-on:click.prevent="newCategory">
                            Add Category
                        </a>
                    @endslot
                @endcomponent
            </div>
        </div>
    </div>
@endsection
@section('help_modal_content')
    @component('layouts.slots.video-embed')
        //www.youtube.com/embed/UdXldTFenQk?rel=0
    @endcomponent
@endsection
@section('body_js')
    <script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}"></script>
    <script type="text/javascript">
        function addCategory() {
            swal({
                    title: "New Category",
                    text: "Enter the name for the category:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    showLoaderOnConfirm: true,
                    inputPlaceholder: "e.g. Stationery"
                },
                function(inputValue){
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("You need to write something!");
                        return false
                    }
                    axios.post("/xhr/inventory/categories", {
                        name: inputValue
                    }).then(function (response) {
                        console.log(response);
                        vm.categories.push(response.data);
                        return swal("Success", "The product category was successfully created.", "success");
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
                            return swal("Oops!", message, "warning");
                        });
                });
        }

        var vm = new Vue({
            el: '#product-categories',
            data: {
                categories: {!! json_encode($categories ?: [])  !!}
            },
            methods: {
                decrement: function (index) {
                    console.log('Removing: ' + index);
                    this.categories.splice(index, 1);
                },
                newCategory: function () {
                    addCategory();
                },
                update: function (index, category) {
                    console.log('Updating: ' + index);
                    this.categories.splice(index, 1, category);
                }
            }
        });
        new Vue({
            el: '#breadcrumbs-wrapper',
            methods: {
                newField: function () {
                    addCategory();
                }
            }
        });
    </script>
@endsection