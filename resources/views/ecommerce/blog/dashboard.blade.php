@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn dropdown-settings waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#!" data-activates="dropdown1" v-if="show_dropdown" style="">
        <i class="material-icons hide-on-med-and-up">add</i>
        <span class="hide-on-small-onl">Actions</span>
        <i class="material-icons right">arrow_drop_down</i>
    </a>
    <ul id="dropdown1" class="dropdown-content">
        <li>
            <a href="#" v-on:click.prevent="newField" class="grey-text text-darken-2 modal-trigger">New Blog Category</a>
        </li>
        <li>
            <a href="{{ $blogUrl or '#' }}" target="_blank" class="grey-text text-darken-2 modal-trigger">New Blog Post</a>
        </li>
    </ul>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="blog-dashboard">
        <div class="row">
            <div class="col s12">
                <div class="row">
                    <div class="col s12 m4">
                        <div class="card">
                            <div class="card-content {{ empty($subdomain) ? 'red' : 'green' }} darken-3 white-text center-align">
                                <h4 class="card-stats-number">{{ empty($subdomain) ? 'Disabled' : 'Enabled' }}</h4>
                                <p class="card-stats-compare">Blog Status</p>
                            </div>
                            <div class="card-action {{ empty($subdomain) ? 'red' : 'green' }} darken-1">
                                <div id="clients-bar" class="center-align"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="card">
                            <div class="card-content blue darken-3 white-text center-align">
                                <h4 class="card-stats-number">{{ $postsCount ? number_format($postsCount) : 'No Posts' }}</h4>
                                <p class="card-stats-compare">Posts</p>
                            </div>
                            <div class="card-action blue darken-1">
                                <div id="clients-bar" class="center-align"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="card">
                            <div class="card-content blue darken-3 white-text center-align">
                                <h4 class="card-stats-number">Blog Domain</h4>
                                <p class="card-stats-compare">{{ !empty($subdomain) ? $subdomain . '/blog' : 'Not Reserved' }}</p>
                            </div>
                            <div class="card-action blue darken-1">
                                <div id="clients-bar" class="center-align"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if (!empty($subdomain))
                        <div class="col s12 m6">
                            <form action="" method="post" class="col s12">
                                {{ csrf_field() }}
                                @component('layouts.slots.empty-fullpage')
                                    @slot('icon')
                                        comment
                                    @endslot
                                    <div class="row black-text">
                                        <div class="input-field col s12">
                                            <input id="blog_name" name="blog_name" type="text"
                                                   class="validate" v-model="blog_settings.blog_name">
                                            <label for="blog_name">Blog Name</label>
                                        </div>
                                    </div>
                                    <div class="row black-text">
                                        <div class="input-field col s6">
                                            <input id="blog_instagram_id" name="blog_instagram_id" type="text"
                                                   class="validate" v-model="blog_settings.blog_instagram_id">
                                            <label for="blog_instagram_id">Blog Instagram ID</label>
                                        </div>
                                        <div class="input-field col s6">
                                            <input id="blog_twitter_id" name="blog_twitter_id" type="text"
                                                   class="validate" v-model="blog_settings.blog_twitter_id">
                                            <label for="blog_twitter_id">Blog Twitter ID</label>
                                        </div>
                                    </div>
                                    <div class="row black-text">
                                        <div class="input-field col s12">
                                            <input id="blog_facebook_page" name="blog_facebook_page" type="url"
                                                   class="validate" v-model="blog_settings.blog_facebook_page">
                                            <label for="blog_facebook_page">Facebook Page</label>
                                        </div>
                                    </div>
                                    <div class="row black-text">
                                        <div class="input-field col s12">
                                            <input id="blog_terms_page" name="blog_terms_page" type="url"
                                                   class="validate" v-model="blog_settings.blog_terms_page">
                                            <label for="blog_terms_page">Terms of Service URL</label>
                                        </div>
                                    </div>
                                    @slot('buttons')
                                        <button type="submit" class="btn-flat blue darken-3 white-text waves-effect waves-light">
                                            Save Settings
                                        </button>
                                    @endslot
                                @endcomponent
                            </form>
                        </div>
                    @endif
                    <div class="col s12 m6">
                        <div class="row">
                            <h5>Categories</h5>
                            <blog-category v-for="(category, index) in categories" class="m6 l6" :key="category.id"
                                           :index="index" :category="category"
                                           v-bind:show-delete="true" v-on:update="update"
                                           v-on:remove="decrement"></blog-category>

                            <div class="col s12" v-if="categories.length  === 0">
                                @component('layouts.slots.empty-fullpage')
                                    @slot('icon')
                                        storage
                                    @endslot
                                    Add one or more categories to classify your blog posts.
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
                    @if (empty($subdomain))
                        <div class="col s12 m6">
                            @component('layouts.slots.empty-fullpage')
                                @slot('icon')
                                    public
                                @endslot
                                Get your custom [business].dorcas.ng sub-domain to enable your store.
                                @slot('buttons')
                                    @if ($isOnPaidPlan)
                                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light"
                                           href="{{ route('apps.ecommerce.domains') }}">
                                            Reserve SubDomain
                                        </a>
                                    @else
                                        <a class="btn-flat blue darken-3 white-text waves-effect waves-light"
                                           href="{{ route('subscription') }}">
                                            Upgrade &amp; Reserve SubDomain
                                        </a>
                                    @endif
                                @endslot
                            @endcomponent
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
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
                    axios.post("/xhr/ecommerce/blog/categories", {
                        name: inputValue
                    }).then(function (response) {
                        console.log(response);
                        vm.categories.push(response.data);
                        return swal("Success", "The blog category was successfully created.", "success");
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
            el: '#blog-dashboard',
            data: {
                blog_owner: {!! json_encode($business) !!},
                blog_settings: {!! json_encode($blogSettings) !!},
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
            data: {
                show_dropdown: false
            },
            mounted: function () {
                if (typeof vm.categories !== 'undefined' && vm.categories.length > 0) {
                    this.show_dropdown = true;
                }
            },
            methods: {
                newField: function () {
                    addCategory();
                }
            }
        });
    </script>
@endsection