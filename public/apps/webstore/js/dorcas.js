Vue.component('webstore-product', {
    template: '<div class="product clearfix" v-bind:class="[customClass]">' +
    '            <div class="product-image" v-if="product.images.data.length > 0">' +
    '                <a v-bind:href=\'"/products/" + product.id\' v-for="(image, index) in product.images.data" :key="image.id" v-if="index < 2">' +
    '                    <img v-bind:src="image.url" v-bind:alt="image + (index + 1)">' +
    '                </a>' +
    '                <div class="product-overlay">' +
    '                    <a href="#" class="add-to-cart" v-on:click.prevent="addToCart"><i class="icon-shopping-cart"></i><span> Add to Cart</span></a>' +
    '                    <a v-bind:href=\'"/product-quick-view/" + product.id\' class="item-quick-view" data-lightbox="ajax"><i class="icon-zoom-in2"></i><span> Quick View</span></a>\n' +
    '                </div>' +
    '            </div>' +
    '            <div class="product-image" v-else>' +
    '                <a v-bind:href=\'"/products/" + product.id\'><img src="/apps/webstore/images/products/1.jpg" v-bind:alt="product.name"></a>' +
    '                <div class="product-overlay">' +
    '                    <a href="#" class="add-to-cart" v-on:click.prevent="addToCart"><i class="icon-shopping-cart"></i><span> Add to Cart</span></a>' +
    '                    <a v-bind:href=\'"/product-quick-view/" + product.id\' class="item-quick-view" data-lightbox="ajax"><i class="icon-zoom-in2"></i><span> Quick View</span></a>\n' +
    '                </div>' +
    '            </div>' +
    '            <div class="product-desc">' +
    '                <div class="product-title"><h3><a v-bind:href=\'"/products/" + product.id\'>{{ product.name }}</a></h3></div>' +
    '                <div class="product-price" v-if=\'typeof product.prices === "undefined" || product.prices.data.length === 0\'><ins>{{ "NGN" + product.default_unit_price.formatted }}</ins></div>' +
    '                <div class="product-price" v-else><ins>{{ default_price.currency + default_price.unit_price.formatted }}</ins></div>' +
    '            </div>' +
    '        </div>',
    data: function () {
        return {
            product: this.product_json,
            active_currency: this.currency,
            is_processing: false
        }
    },
    computed: {
        customClass: function () {
            var customClasses = [];
            if (this.product.categories.data.length  > 0) {
                for (var i = 0; i < this.product.categories.data.length; i++) {
                    customClasses.push('sf-cat-' + this.product.categories.data[i].id);
                }
            }
            return customClasses.join(' ');
        },
        default_price: function () {
            if (typeof this.product.prices === 'undefined' || this.product.prices.data.length === 0) {
                return {
                    currency: this.currency,
                    unit_price: {raw: product.default_unit_price.raw, formatted: product.default_unit_price.formatted}
                };
            }
            for (var i = 0; i < this.product.prices.data.length; i++) {
                if (this.product.prices.data[i].currency !== this.active_currency) {
                    continue;
                }
                return this.product.prices.data[i];
            }
            return {
                currency: this.currency,
                unit_price: {raw: product.default_unit_price.raw, formatted: product.default_unit_price.formatted}
            };
        }
    },
    props: {
        product_json: {
            type: Object,
            required: true
        },
        currency: {
            type: String,
            required: false,
            default: function () {
                return 'NGN';
            }
        }
    },
    methods: {
        addToCart: function () {
            if (this.product_json.inventory === 0) {
                swal('Out of Stock', 'We\'re sorry but this product is out of stock at the moment.');
                return false;
            }
            this.is_processing = true;
            var image = typeof this.product.images !== 'undefined' && this.product.images.data.length > 0 ? this.product.images.data[0].url : '';
            this.$emit('add-to-cart', this.product.id, this.product.name, this.default_price.unit_price.raw, image);
        }
    }
});

Vue.component('blog-post', {
    template: '<div class="entry clearfix">' +
    '                <div class="entry-timeline">' +
    '                    {{ date_day }}<span>{{ date_month }}</span>' +
    '                    <div class="timeline-divider"></div>' +
    '                </div>' +
    '                <div class="entry-image">' +
    '                    <a v-if="typeof media.id !== \'undefined\'" v-bind:href="media.url" data-lightbox="image"><img class="image_fade" v-bind:src="media.url" :alt="media.title || \'Post Image\'"></a>' +
    '                </div>' +
    '                <div class="entry-title">' +
    '                    <h2><a v-bind:href="\'/blog/posts/\' + post.slug">{{ post.title }}</a></h2>' +
    '                </div>' +
    '                <ul class="entry-meta clearfix">' +
    '                    <li><a href="#"><i class="icon-user"></i> {{ posted_by.firstname + " " + posted_by.lastname }}</a></li>' +
    '                    <li v-if="post.categories.data.length  > 0"><i class="icon-folder-open"></i><a v-for="(category, index) in post.categories.data" :key="category.id" v-if="index < 2" v-bind:href="\'/categories/\' + category.slug">{{ index > 0 ? ", " : "" }}{{ category.name }}</a></li>' +
    '                    <li v-if="typeof media.id !== \'undefined\'"><a href="#"><i class="icon-camera-retro"></i></a></li>' +
    '                    <li v-if="showAdminButtons"><a v-bind:href="\'/admin-blog/\' + post.id + \'/edit\'"><i class="icon-edit"></i> Edit</a></li>' +
    '                    <li v-if="showAdminButtons"><a href="#" v-on:click.prevent="deletePost" class="text-danger"><i class="icon-trash"></i> Delete</a></li>' +
    '                </ul>' +
    '                <div class="entry-content">' +
    '                    <p>{{ post.summary === null ? "Click Read More to get more details" : post.summary }}</p>' +
    '                    <a v-bind:href="\'/blog/posts/\' + post.slug" class="more-link">Read More</a>' +
    '                </div>' +
    '            </div>',
    data: function () {
        return {
            posted_by: {},
            media: {},
            image_url: null,
            video_url: null
        };
    },
    props: {
        post: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        showAdminButtons: {
            type: Boolean,
            required: false,
            default: function () {
                return false;
            }
        }
    },
    computed: {
        posted_at: function () {
            return typeof this.post.publish_at !== 'undefined' && this.post.publish_at !== null ?
                this.post.publish_at : this.post.created_at;
        },
        date_day: function () {
            return moment(this.posted_at).format('DD');
        },
        date_month: function () {
            return moment(this.posted_at).format('MMM');
        }
    },
    mounted: function () {
        if (typeof this.post.posted_by !== 'undefined' && typeof this.post.posted_by.data !== 'undefined') {
            this.posted_by = this.post.posted_by.data;
        }
        if (typeof this.post.media !== 'undefined' && typeof this.post.media.data !== 'undefined') {
            this.media = this.post.media.data;
            this.image_url = this.media.url;
        }
    },
    methods: {
        deletePost: function () {
            this.$emit('delete-post', this.index);
        }
    }
});

Vue.component('suggestion-blog-post', {
    template: '<div class="mpost clearfix">' +
    '                <div class="entry-image">' +
    '                    <a v-if="typeof media.id !== \'undefined\'" v-bind:href="media.url" data-lightbox="image"><img class="image_fade" v-bind:src="media.url" :alt="media.title || \'Post Image\'"></a>' +
    '                </div>' +
    '                <div class="entry-c">' +
    '                    <div class="entry-title">' +
    '                        <h4><a v-bind:href="\'/blog/posts/\' + post.slug">{{ post.title }}</a></h4>' +
    '                    </div>' +
    '                    <ul class="entry-meta clearfix">' +
    '                        <li><i class="icon-calendar3"></i> {{ posted_at.format("DD MMM, YYYY") }}</li>' +
    '                        <li v-if="showAdminButtons"><a v-bind:href="\'/admin-blog/\' + post.id + \'/edit\'"><i class="icon-edit"></i> Edit</a></li>' +
    '                    </ul>' +
    '                    <div class="entry-content">' +
    '                        <p>{{ post.summary === null ? "Click Read More to get more details" : post.summary.substr(0, 50) }}</p>' +
    '                    </div>' +
    '                </div>' +
    '            </div>',
    data: function () {
        return {
            media: {},
            image_url: null,
            video_url: null
        };
    },
    props: {
        post: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        showAdminButtons: {
            type: Boolean,
            required: false,
            default: function () {
                return false;
            }
        }
    },
    computed: {
        posted_at: function () {
            var date = typeof this.post.publish_at !== 'undefined' && this.post.publish_at !== null ?
                this.post.publish_at : this.post.created_at;
            return moment(date);
        }
    },
    mounted: function () {
        if (typeof this.post.media !== 'undefined' && typeof this.post.media.data !== 'undefined') {
            this.media = this.post.media.data;
            this.image_url = this.media.url;
        }
    },
    methods: {
        deletePost: function () {
            this.$emit('delete-post', this.index);
        }
    }
})