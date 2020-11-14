@extends('layouts.app')
@section('body_main_content_header_button')
    <a class="btn waves-effect waves-light breadcrumbs-btn right gradient-45deg-light-blue-cyan gradient-shadow"
       href="#" v-on:click.prevent="createAdvert">
        <i class="material-icons hide-on-med-and-up">add_circle_outline</i>
        <span class="hide-on-small-onl">New Advert</span>
    </a>
@endsection
@section('body_main_content_body')
    @include('blocks.page-messages')
    @include('blocks.ui-response-alert')
    <div class="container" id="listing">
        <div class="row mt-3" v-show="adverts !== null && adverts.length > 0">
            <advert-card v-for="(ad, index) in adverts" :key="ad.id" :advert="ad" :index="index"
                         v-on:edit-advert="editAdvert" v-on:delete-advert="deleteAdvert"></advert-card>
        </div>
        <div class="col s12" v-if="adverts === null || adverts.length === 0">
            @component('layouts.slots.empty-fullpage')
                @slot('icon')
                    burst_mode
                @endslot
                You can add one or more ads to be displayed on your Dorcas store fronts (store, blog, e.t.c.).
                @slot('buttons')
                    <a class="btn-flat blue darken-3 white-text waves-effect waves-light" href="#"
                       v-on:click.prevent="createAdvert">
                        Add Advert
                    </a>
                @endslot
            @endcomponent
        </div>
        @include('ecommerce.modals.new-advert')
    </div>
@endsection
@section('body_js')
    <script type="text/javascript">
        var vm = new Vue({
            el: '#listing',
            data: {
                adverts: {!! json_encode(!empty($adverts) ? $adverts : []) !!},
                advert: {title: '', type: '', redirect_url: '', is_default: 1},
                editMode: false,
                recommendedDim: ''
            },
            methods: {
                createAdvert: function () {
                    this.advert = {title: '', type: '', redirect_url: '', is_default: 1};
                    $('#manage-ad-modal').modal('open');
                },
                adjustRecommendation: function () {
                    if (this.advert.type === 'sidebar') {
                        this.recommendedDim = '240 x [any height]';
                    } else if (this.advert.type === 'footer') {
                        this.recommendedDim = '[any width] x [any height]';
                    } else {
                        this.recommendedDim = 'proper';
                    }
                },
                editAdvert: function (index) {
                    let advert = typeof this.adverts[index] !== 'undefined' ? this.adverts[index] : null;
                    if (typeof advert.id === 'undefined') {
                        return;
                    }
                    advert.is_default = advert.is_default ? 1 : 0;
                    this.advert = advert;
                    $('#manage-ad-modal').modal('open');
                },
                deleteAdvert: function (index) {
                    let advert = typeof this.adverts[index] !== 'undefined' ? this.adverts[index] : null;
                    if (advert === null) {
                        return;
                    }
                    advert.is_default = advert.is_default ? 1 : 0;
                    this.advert = advert;
                    let context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to delete advert " + context.advert.title,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/ecommerce/adverts/" + context.advert.id)
                            .then(function (response) {
                                console.log(response);
                                context.adverts.splice(index, 1);
                                return swal("Deleted!", "The advert was successfully deleted.", "success");
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
                                return swal("Delete Failed", message, "warning");
                            });
                    });
                }
            }
        });

        new Vue({
            el: '#header-button',
            data: {

            },
            methods: {
                createAdvert: function () {
                    vm.createAdvert();
                }
            }
        })
    </script>
@endsection