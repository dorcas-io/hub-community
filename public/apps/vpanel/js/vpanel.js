/**
 * A simple method to convert a string to it's "title case" representation.
 *
 * title case => Title Case
 * tITLE CAse => Title Case
 *
 * @returns {string}
 */
String.prototype.title_case = function () {
    this.toLowerCase();
    var components = this.split(' ');
    return components.map(function (component) {
        return component.charAt(0).toUpperCase() + component.substr(1).toLowerCase();
    }).join(' ');
};

function vpanel_simple_swal(title, message, type) {
    return swal({
        animation: true,
        title: title,
        text: message,
        customClass: 'swal2-btns-left',
        confirmButtonClass: 'swal2-btn swal2-btn-confirm',
        confirmButtonText: 'Ok',
        type: type
    });
}

var vpanel = {
    utilities: {
        getElementAttributes: function (target) {
            if (!target.hasAttributes()) {
                return [];
            }
            var attributes = {};
            var attrs = target.attributes;
            for (var i = 0; i < attrs.length; i++) {
                attributes[attrs[i].name] = attrs[i].value;
            }
            return attributes;
        },
        randomId: function (length) {
            length = typeof length === 'undefined' || isNaN(length) ? 7 : parseInt(length, 10);
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            for (var i = 0; i < length; i++) {
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            return text;
        }
    },
    formatters: {
        companies: function (row, index) {
            let contact_name = '';
            if (typeof row.contact !== 'undefined') {
                contact_name += row.contact.firstname !== null ? row.contact.firstname + ' ' : '';
                contact_name += row.contact.lastname !== null ? row.contact.lastname : '';
            }
            row.status = row.is_trashed ? 'Trashed' : 'Live';
            row.menu = '<div class="actions" style="font-size: 1.2rem; margin-left: 10px;">';
            if (!row.is_trashed) {
                row.menu += '<a href="/vpanel/businesses/' + row.id + '" data-action="view"><i class="ion-md-eye"></i></a>' +
                    '&nbsp;<a href="/vpanel/businesses/' + row.id + '/edit" data-action="view"><i class="ion-md-create"></i></a>';
            }
            row.menu += '&nbsp;<a href="#"><i  data-action="delete" data-index="' + index + '" class="ion-md-trash"></i></a>'+
                '</div>';
            return row;
        },
        users: function (row, index) {
            row.status = row.is_trashed ? 'Trashed' : 'Live';
            row.is_vendor = row.is_vendor ? 'Yes' : 'No';
            row.menu = '<div class="actions" style="font-size: 1.2rem; margin-left: 10px;">' +
                '<a href="/vpanel/businesses/' + row.company.data.id + '" data-action="view"><i class="ion-md-eye"></i></a>';
            row.menu += '&nbsp;<a href="#"><i  data-action="delete" data-index="' + index + '" class="ion-md-trash"></i></a>'+
                '</div>';
            return row;
        },
        invites: function (row, index) {
            if (typeof row.inviting_user !== 'undefined' && row.inviting_user.data !== null) {
                row.invited_by = row.inviting_user.data.firstname + ' ' + row.inviting_user.data.lastname;

            } else if (typeof row.inviter !== 'undefined' && row.inviter.data !== null) {
                row.invited_by = row.inviter.data.firstname + ' ' + row.inviter.data.lastname;
            }
            row.action = row.config_data.action.split('_').join(' ').title_case();
            row.sent_at = moment(row.created_at).format('DD MMM, YYYY HH:mm');
            row.menu = '<div class="actions" style="font-size: 1.2rem; margin-left: 10px;">' +
                '<a href="#"><i  data-action="delete" data-index="' + index + '" class="ion-md-trash"></i></a>'+
                '</div>';
            return row;
        }
    }
};

/**
 * VUE Components
 */
Vue.component('address-table-row', {
    template: '<tr>'+
        '<td>{{ address.name !== null && address.name.length > 0 ? address.name.toUpperCase() : "" }}</td>' +
        '<td>{{ address.address1.toUpperCase() }}</td>' +
        '<td>{{ address.address2 !== null && address.address2.length > 0 ? address.address2.toUpperCase() : "" }}</td>' +
        '<td>{{ address.city !== null && address.city.length > 0 ? address.city.toUpperCase() : "" }}</td>' +
        '<td>{{ typeof address.state !== "undefined" ? address.state.data.name.toUpperCase() : "" }}</td>' +
        '<td v-if="showMenu">' +
            '<div class="actions dropdown">' +
                '<span class="icon" data-toggle="dropdown"><i class="ion ion-ios-more"></i></span>' +
                '<div class="menu dropdown-menu">' +
                    '<a v-bind:href="profileUrl">View Location</a>' +
                    '<a v-bind:href="editUrl">Edit Address</a>' +
                    '<a href="#" v-on:click.prevent="deleteAddress">Delete Address</a>' +
                '</div>' +
            '</div>' +
        '</td>' +
    '</tr>',
    props: {
        address: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        baseUrl: {
            type: String,
            required: false,
            default: function () {
                return '';
            }
        },
        showMenu: {
            type: Boolean,
            required: false,
            default: function () {
                return true;
            }
        }
    },
    computed: {
        profileUrl: function () {
            return this.baseUrl.length === 0 ? '#' : this.baseUrl + '/' + this.address.id;
        },
        editUrl: function () {
            return this.profileUrl + '/edit';
        },
        fullAddress: function () {
            let address = this.address.address1.toUpperCase();
            address += this.address.address2 !== null && this.address.address2.length > 0 ?
                ' ' + this.address.address2.toUpperCase() : '';
            address += this.address.city !== null && this.address.city.length > 0 ?
                ', ' + this.address.city.toUpperCase() : '';
            address += (typeof this.address.state !== 'undefined' ? ', ' + this.address.state.data.name.toUpperCase() : '');
            return address;
        }
    },
    methods: {
        deleteAddress: function () {
            let context = this;
            swal({
                animation: true,
                title: "Are you sure?",
                text: "You are about to delete this address: " + context.fullAddress,
                customClass: 'swal2-btns-left',
                showCancelButton: true,
                confirmButtonClass: 'swal2-btn swal2-btn-confirm',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonClass: 'swal2-btn swal2-btn-cancel',
                cancelButtonText: 'Cancel',
                closeOnConfirm: false,
            }).then((b) => {
                console.log(b);
                if (typeof b.dismiss !== 'undefined' && b.dismiss === 'cancel') {
                    return '';
                }
                axios.delete("/xhr/company/addresses/" + context.address.id)
                    .then(function (response) {
                        console.log(response);
                        context.$emit('deleted', context.index);

                        return vpanel_simple_swal("Deleted!", "The address was successfully deleted.", "success");
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
                        return vpanel_simple_swal("Delete Failed", message, "warning");
                    });
            });
        }
    }
});

Vue.component('vehicle-table-row', {
    template: '<tr v-bind:class="{\'selected\': vehicle.is_checked}">'+
        '<td v-if="showCheckbox">' +
            '<input v-model="vehicle.is_checked" name="vehicles[]" v-bind:value="vehicle.id" type="checkbox" ' +
                'v-on:change="showChecked">' +
        '</td>' +
        '<td>{{ vehicle.plate_number.toUpperCase() }}</td>' +
        '<td>{{ vehicle.vin.toUpperCase() }}</td>' +
        '<td>{{ typeof make !== "undefined" && make !== null ? make.name.toUpperCase() : "" }}</td>' +
        '<td>{{ typeof model !== "undefined" && model !== null ? model.name.toUpperCase() : "" }}</td>' +
        '<td>{{ vehicle.colour !== null ? vehicle.colour.toUpperCase() : "" }}</td>' +
        '<td>{{ vehicle.registration_year }}</td>' +
        '<td>{{ vehicle.vehicle_age }}</td>' +
        '<td>{{ typeof vehicle.vehicle_usages !== "undefined" ? vehicle.vehicle_usages.data[0].name : "" }}</td>' +
        '<td>{{ typeof type !== "undefined" && type !== null ? type.name : "" }}</td>' +
        '<td v-if="showMenu">' +
            '<div class="actions dropdown">' +
                '<span class="icon" data-toggle="dropdown"><i class="ion ion-ios-more"></i></span>' +
                '<div class="menu dropdown-menu">' +
                    '<a v-bind:href="detailsUrl">View Additional Information</a>' +
                    '<a v-bind:href="editUrl">Edit Vehicle</a>' +
                    '<a href="#" v-on:click.prevent="deleteVehicle">Delete Vehicle</a>' +
                '</div>' +
            '</div>' +
        '</td>' +
    '</tr>',
    data: function () {
        return {
            make: null,
            model: null,
            type: null
        };
    },
    props: {
        vehicle: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        baseUrl: {
            type: String,
            required: false,
            default: function () {
                return '';
            }
        },
        showMenu: {
            type: Boolean,
            required: false,
            default: function () {
                return true;
            }
        },
        showCheckbox: {
            type: Boolean,
            required: false,
            default: function () {
                return false;
            }
        },
    },
    computed: {
        detailsUrl: function () {
            return this.baseUrl.length === 0 ? '#' : this.baseUrl + '/' + this.vehicle.id;
        },
        editUrl: function () {
            return this.baseUrl + '/' + this.vehicle.id + '/edit';
        }
    },
    mounted: function () {
        if (typeof this.vehicle.vehicle_model !== 'undefined') {
            this.model = this.vehicle.vehicle_model.data;
        }
        if (this.model !== null) {
            this.make = typeof this.model.vehicle_make !== 'undefined' ? this.model.vehicle_make.data : null;
            this.type = typeof this.model.vehicle_type !== 'undefined' ? this.model.vehicle_type.data : null;
        }
    },
    methods: {
        showChecked: function () {
            this.$emit('show-vehicle-checked', this.index, this.vehicle.is_checked);
        },
        deleteVehicle: function () {
            let context = this;
            swal({
                animation: true,
                title: "Are you sure?",
                text: "You are about to delete this vehicle: " + context.vehicle.plate_number,
                customClass: 'swal2-btns-left',
                showCancelButton: true,
                confirmButtonClass: 'swal2-btn swal2-btn-confirm',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonClass: 'swal2-btn swal2-btn-cancel',
                cancelButtonText: 'Cancel',
                closeOnConfirm: false,
            }).then((b) => {
                console.log(b);
                if (typeof b.dismiss !== 'undefined' && b.dismiss === 'cancel') {
                    return '';
                }
                axios.delete("/xhr/vehicles/" + context.vehicle.id)
                    .then(function (response) {
                        console.log(response);
                        context.$emit('deleted', context.index);
                        return vpanel_simple_swal("Deleted!", "The vehicle was successfully deleted.", "success");
                    })
                    .catch(function (error) {
                        let message = '';
                        if (error.response) {
                            // The request was made and the server responded with a status code
                            // that falls out of the range of 2xx
                            let e = error.response.data.errors[0];
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
                        return vpanel_simple_swal("Delete Failed", message, "warning");
                    });
            });
        }
    }
});

Vue.component('vehicle-insurance-table-row', {
    template: '<tr>'+
        '<td>{{ insurer }}</td>' +
        '<td>{{ insurance.policy_number }}</td>' +
        '<td>{{ insurance.policy_ends_at }}</td>' +
        '<td><a v-bind:href="editUrl"><i class="ion-md-create"></i> Edit</a></td>' +
        '<td><a v-on:click.prevent="deleteInsurance" class="text-danger" href="#"><i class="ion-ios-trash"></i> Delete</a></td>' +
    '</tr>',
    props: {
        vehicleId: {
            type: String,
            required: true
        },
        insurance: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        baseUrl: {
            type: String,
            required: true
        }
    },
    computed: {
        editUrl: function () {
            return '/policies/' + this.insurance.id;
        },
        insurer: function () {
            if (typeof this.insurance.insurer === 'undefined') {
                return '';
            }
            let insurer = this.insurance.insurer.data;
            return typeof insurer.name !== 'undefined' ? insurer.name : '';
        }
    },
    methods: {
        deleteInsurance: function () {
            let context = this;
            swal({
                animation: true,
                title: "Are you sure?",
                text: "You are about to delete this insurance record: " + context.insurance.policy_number,
                customClass: 'swal2-btns-left',
                showCancelButton: true,
                confirmButtonClass: 'swal2-btn swal2-btn-confirm',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonClass: 'swal2-btn swal2-btn-cancel',
                cancelButtonText: 'Cancel',
                closeOnConfirm: false,
            }).then((b) => {
                console.log(b);
                if (typeof b.dismiss !== 'undefined' && b.dismiss === 'cancel') {
                    return '';
                }
                axios.delete("/xhr/vehicles/" + this.vehicleId + "/insurance/" + context.insurance.id)
                    .then(function (response) {
                        console.log(response);
                        context.$emit('deleted', context.index);

                        return vpanel_simple_swal("Deleted!", "The insurance record was successfully deleted.", "success");
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
                        return vpanel_simple_swal("Delete Failed", message, "warning");
                    });
            });
        }
    }
});

Vue.component('service-provider-address-card', {
    template: '<div class="card">' +
        '<div class="preview">' +
            '<img v-if="showImage" v-bind:src="imageUrl" />' +
            '<div class="stars">' +
                '<span class="starred"><i class="ion ion-ios-star"></i></span>' +
                '<span class="starred"><i class="ion ion-ios-star"></i></span>' +
                '<span class="starred"><i class="ion ion-ios-star"></i></span>' +
                '<span><i class="ion ion-ios-star"></i></span>' +
                '<span><i class="ion ion-ios-star"></i></span>' +
            '</div>' +
        '</div>' +
        '<div class="data">' +
            '<div class="n font-weight-bold">{{ garageName }}</div>' +
            '<div class="a">{{ fullAddress }}</div>' +
            '<div class="d">{{ distanceAway }} away</div>' +
            '<h6>{{ serviceCost }}</h6>' +
            '<div><a href="#" v-on:click.prevent="selectGarage" v-bind:class="{\'disabled\': !isSelectable}" class="btn btn-default btn-outline btn-default-type centered">Select</a></div>' +
        '</div>' +
    '</div>',
    data: function () {
        return {
            photos: [],
            addressable: null,
            price: '',
            provider: {}
        };
    },
    props: {
        address: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        defaultPhoto: {
            type: String,
            required: false
        }
    },
    computed: {
        garageName: function () {
            if (this.addressable === null) {
                return '';
            }
            let name = this.addressable.name;
            if (typeof this.address.name !== 'undefined') {
                name += ' [' + this.address.name + ']';
            }
            return name;
        },
        fullAddress: function () {
            let address = this.address.address1;
            address += this.address.address2 !== null && this.address.address2.length > 0 ?
                ' ' + this.address.address2 : '';
            address += this.address.city !== null && this.address.city.length > 0 ?
                ', ' + this.address.city : '';
            address += (typeof this.address.state !== 'undefined' ? ', ' + this.address.state.data.name : '');
            return address;
        },
        distanceAway: function () {
            let distanceInMetres = parseFloat(this.address.distance_in_metres);
            if (distanceInMetres < 1000) {
                return this.round(distanceInMetres) + 'metres';
            }
            return this.round(distanceInMetres / 1000) + 'km';
        },
        showImage: function () {
            //return this.photos.length > 0 || (typeof this.defaultPhoto !== 'undefined' && this.defaultPhoto !== null);
            return false;
        },
        imageUrl: function () {
            if (this.photos.length > 0) {
                return this.photos[0].url;
            }
            return this.defaultPhoto;
        },
        isSelectable: function () {
            if (typeof this.address.is_selectable === 'undefined') {
                return true;
            }
            return this.address.is_selectable;
        },
        serviceCost: function () {
            if (typeof this.provider.id === 'undefined') {
                return '';
            }
            return this.provider.currency + this.provider.amount.formatted;
        }
    },
    mounted: function () {
        if (typeof this.address.photos !== 'undefined') {
            this.photos = this.address.photos.data;
        }
        if (typeof this.address.provider !== 'undefined') {
            this.provider = this.address.provider.data;
        }
        this.addressable = this.address.addressable.data;
    },
    methods: {
        selectGarage: function () {
            this.$emit('select-garage', this.index);
        },
        round: function (number, decimalPlaces) {
            number = parseFloat(number);
            decimalPlaces = typeof decimalPlaces === 'undefined' ? 2 : decimalPlaces;
            let exp = Math.pow(10, decimalPlaces);
            return Math.round(number * exp) / exp;
        }
    }
});

Vue.component('insurance-policy-table-row', {
    template: '<tr>'+
        '<td>{{ insurer }}</td>' +
        '<td>{{ policy.type }}</td>' +
        '<td>{{ policy.policy_number }}</td>' +
        '<td>{{ policy.policy_ends_at }}</td>' +
        '<td v-if="showMenu">' +
            '<div class="actions dropdown">' +
                '<span class="icon" data-toggle="dropdown"><i class="ion ion-ios-more"></i></span>' +
                '<div class="menu dropdown-menu">' +
                    '<a v-bind:href="editUrl">Edit Policy</a>' +
                    '<a v-on:click.prevent="deletePolicy" class="text-danger" href="#">Delete Policy</a>' +
                '</div>' +
            '</div>' +
        '</td>' +
    '</tr>',
    props: {
        policy: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        baseUrl: {
            type: String,
            required: true
        },
        showMenu: {
            type: Boolean,
            required: false,
            default: function () {
                return true;
            }
        },
    },
    computed: {
        editUrl: function () {
            return this.baseUrl + '/' + this.policy.id;
        },
        insurer: function () {
            if (typeof this.policy.insurer === 'undefined') {
                return '';
            }
            let insurer = this.policy.insurer.data;
            return typeof insurer.name !== 'undefined' ? insurer.name : '';
        }
    },
    methods: {
        moment: function (dateString, format) {
            return moment(dateString, format);
        },
        deletePolicy: function () {
            let context = this;
            swal({
                animation: true,
                title: "Are you sure?",
                text: "You are about to delete this insurance policy: " + context.policy.policy_number,
                customClass: 'swal2-btns-left',
                showCancelButton: true,
                confirmButtonClass: 'swal2-btn swal2-btn-confirm',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonClass: 'swal2-btn swal2-btn-cancel',
                cancelButtonText: 'Cancel',
                closeOnConfirm: false,
            }).then((b) => {
                console.log(b);
                if (typeof b.dismiss !== 'undefined' && b.dismiss === 'cancel') {
                    return '';
                }
                axios.delete("/xhr/insurance-companies/" + context.policy.id)
                    .then(function (response) {
                        console.log(response);
                        context.$emit('deleted', context.index);

                        return vpanel_simple_swal("Deleted!", "The insurance record was successfully deleted.", "success");
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
                        return vpanel_simple_swal("Delete Failed", message, "warning");
                    });
            });
        }
    }
});

Vue.component('photo-box', {
    template: '<div>' +
    '    <img class="img-thumbnail" v-bind:src="photo.url" />' +
    '    <p class="mt-1">{{ photo.text !== null ? photo.text : "" }}</p>' +
    '    <a class="btn-link text-danger" href="#" v-on:click.prevent="triggerDelete">Delete</a>' +
    '</div>',
    props: {
        photo: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        showMenu: {
            type: Boolean,
            required: false,
            default: function () {
                return true;
            }
        },
    },
    methods: {
        triggerDelete: function () {
            this.$emit('delete-photo', this.index);
        }
    }
});
