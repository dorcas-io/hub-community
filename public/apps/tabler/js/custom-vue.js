
Vue.component('group-card', {
    template: '<div class="col-md-6 col-xl-4">' +
    '<div class="card">' +
    	'<div class="card-status bg-blue"></div>' +
		'<div class="card-header">' +
			'<h3 class="card-title">{{ group.name + (typeof group.customers_count !== \'undefined\' ? " (" + group.customers_count + ")" : "") }}</h3>' +
			'<div class="card-options">' +
			'</div>' +
		'</div>' +
		'<div class="card-body" style="min-height:75px;">' +
			'{{ group.description }}' +
		'</div>' +
		'<div class="card-footer">' +
			'<a v-bind:href="\'/apps/crm/customers?groups=\' + group.id" class="btn btn-primary btn-sm">View Customers</a>' +
			'<a href="#" class="btn btn-secondary btn-sm" v-on:click.prevent="editGroup">Edit</a>' +
			'<a href="#" class="btn btn-danger btn-sm" v-on:click.prevent="deleteGroup">Delete</a>' +
		'</div>' +
    '</div>' +
    '</div>',
    props: {
        group: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        }
    },
    methods: {
        editGroup: function () {
            this.$emit('edit-group', this.index);
        },
        deleteGroup: function () {
            this.$emit('delete-group', this.index);
        }
    }
});




Vue.component('access-grant-company-card', {
    template: '<div class="col s12">' +
        '<div class="card">' +
        '<div class="card-content">' +
        '<span class="card-title"><h4>{{ company.name }}</h4></span>' +
        '<p class="flow-text">Phone: {{ company.phone !== null ? company.phone : "-" }}</p>' +
        '<p class="flow-text">Email: {{ company.email !== null ? company.email : "-" }}</p>' +
        '</div>' +
        '<div class="card-action">' +
        '<a href="#" v-on:click.prevent="requestModules"">Request Access</a>' +
        '</div>' +
        '</div>' +
        '</div>',
    props: {
        index: {
            type: Number,
            required: true
        },
        company: {
            type: Object,
            required: true
        }
    },
    methods: {
        requestModules: function () {
            this.$emit('request-modules', this.index);
        },
    }
});

Vue.component('access-grant-card', {
    template: '<div class="col s12">' +
        '<div class="card" v-bind:class="{\'yellow lighten-5\': grant.status === \'pending\', \'green lighten-5\': grant.status === \'accepted\'}">' +
        '<div class="card-content">' +
        '<span class="card-title"><h4>{{ grant.company.data.name }}</h4></span>' +
        '<p class="flow-text">Approved Modules: {{ grant.extra_json.modules.length }}</p>' +
        '<p class="flow-text">Pending: {{ grant.extra_json.pending_modules.length }}</p>' +
        '</div>' +
        '<div class="card-action">' +
        '<a v-bind:href="grant.url" class="btn-flat" target="_blank" v-bind:class="{\'disabled\': grant.status !== \'accepted\'}">Gain Access</a>' +
        '</div>' +
        '</div>' +
        '</div>',
    props: {
        index: {
            type: Number,
            required: true
        },
        grant: {
            type: Object,
            required: true
        }
    },
    methods: {
        requestModules: function () {
            this.$emit('request-modules', this.index);
        },
    }
});

Vue.component('contact-field', {
    template: '<div class="col s12">' +
    '<div class="card">' +
    '<div class="card-content">' +
    '<span class="card-title"><h4>{{ title }}</h4></span>' +
    '<p v-bind:class="content_class">{{ field_name }}</p>' +
    '</div>' +
    '<div class="card-action">' +
    '<a href="#" class="grey-text text-darken-3" v-bind:data-id="record_id" v-on:click.prevent="edit">Edit</a>' +
    '<a href="#" class="red-text" v-bind:data-id="record_id" v-on:click.prevent="deleteField" v-if="showDelete">REMOVE</a>' +
    '</div>' +
    '</div>' +
    '</div>',
    props: {
        title: {
            type: String,
            default: 'Custom Field'
        },
        content_class: {
            type: String,
            default: 'flow-text'
        },
        field_name: {
            type: String,
            required: true
        },
        record_id: {
            type: [String, Number],
            required: true
        },
        seen: {
            type: Boolean,
            default: true
        },
        showDelete: {
            type: Boolean,
            default: false
        }
    },
    data: function () {
        return {
            visible: this.seen
        }
    },
    methods: {
        edit: function () {
            var context = this;
            swal({
                    title: "Update Field",
                    text: "Enter new name ["+context.field_name+"]:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    showLoaderOnConfirm: true,
                    inputPlaceholder: "Custom Field Name"
                },
                function(inputValue){
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("You need to write something!");
                        return false
                    }
                    axios.put("/xhr/crm/custom-fields/"+context.record_id, {
                        name: inputValue
                    }).then(function (response) {
                        console.log(response);
                        context.field_name = inputValue;
                        return swal("Success", "The custom field was successfully updated.", "success");
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
        },
        deleteField: function (event) {
            var attrs = Hub.utilities.getElementAttributes(event.target);
            var id = attrs['data-id'] || null;
            if (id === null) {
                return false;
            }
            var context = this;
            swal({
                title: "Are you sure?",
                text: "You are about to delete the custom field (" + this.field_name + ").",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                axios.delete("/xhr/crm/custom-fields/" + id)
                    .then(function (response) {
                        console.log(response);
                        context.visible = false;
                        context.$emit('remove', id);
                        return swal("Deleted!", "The contact field was successfully deleted.", "success");
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

Vue.component('professional-social-connection', {
    template: '<div class="col s12">' +
    '<div class="card">' +
    '<div class="card-content">' +
    '<span class="card-title"><h4>{{ connection.channel.title_case() }}</h4></span>' +
    '<p class="flow-text" v-if="connection.id !== null && connection.id.length > 0">{{ connection.id }}</p>' +
    '<p><a v-bind:href="connection.url" target="_blank">{{ connection.url }}</a></p>' +
    '</div>' +
    '<div class="card-action">' +
    '<a v-bind:href="connection.url" class="black-text" target="_blank">OPEN</a>' +
    '<a href="#" class="red-text" v-on:click.prevent="deleteField">REMOVE</a>' +
    '</div>' +
    '</div>' +
    '</div>',
    props: {
        index: {
            type: Number,
            required: true
        },
        connection: {
            type: Object,
            required: true
        }
    },
    data: function () {
        return {
            visible: this.seen
        }
    },
    methods: {
        deleteField: function (event) {
            var context = this;
            swal({
                title: "Are you sure?",
                text: "You are about to delete the " + this.connection.channel.title_case() + " connection.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                axios.delete("/xhr/directory/social-connections", {
                    params: {index: context.index}
                }).then(function (response) {
                        console.log(response);
                        context.$emit('delete-connection', context.index);
                        return swal("Deleted!", "The connection was successfully deleted.", "success");
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

Vue.component('professional-credential', {
    template: '<div class="col s12">' +
    '<div class="card">' +
    '<div class="card-content">' +
    '<span class="card-title"><h4>{{ credential.certification }} ({{ credential.year }})</h4></span>' +
    '<p class="flow-text" v-if="credential.title !== null">{{ credential.title }} ({{ credential.type }})</p>' +
    '<p>{{ credential.description }}</p>' +
    '</div>' +
    '<div class="card-action">' +
    '<a href="#" class="red-text" v-on:click.prevent="deleteField">REMOVE</a>' +
    '</div>' +
    '</div>' +
    '</div>',
    props: {
        index: {
            type: Number,
            required: true
        },
        credential: {
            type: Object,
            required: true
        }
    },
    data: function () {
        return {
            visible: this.seen
        }
    },
    methods: {
        deleteField: function () {
            var context = this;
            swal({
                title: "Are you sure?",
                text: "You are about to delete the " + this.credential.certification+ " credential.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                axios.delete("/xhr/directory/credentials/" + context.credential.id)
                    .then(function (response) {
                        console.log(response);
                        context.$emit('delete-credential', context.index);
                        return swal("Deleted!", "The credential was successfully deleted.", "success");
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

Vue.component('professional-experience', {
    template: '<div class="col s12">' +
    '<div class="card">' +
    '<div class="card-content">' +
    '<span class="card-title"><h4>{{ experience.designation }}</h4></span>' +
    '<p class="flow-text">{{ experience.company }}</p>' +
    '<p>{{ experience.from_year }} - {{ experience.to_year === null ? "Present" : experience.to_year }}</p>' +
    '</div>' +
    '<div class="card-action">' +
    '<a href="#" class="red-text" v-on:click.prevent="deleteField">REMOVE</a>' +
    '</div>' +
    '</div>' +
    '</div>',
    props: {
        index: {
            type: Number,
            required: true
        },
        experience: {
            type: Object,
            required: true
        }
    },
    data: function () {
        return {
            visible: this.seen
        }
    },
    methods: {
        deleteField: function () {
            var context = this;
            swal({
                title: "Are you sure?",
                text: "You are about to delete experience at " + this.experience.company+ " experience.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                axios.delete("/xhr/directory/experiences/" + context.experience.id)
                    .then(function (response) {
                        console.log(response);
                        context.$emit('delete-experience', context.index);
                        return swal("Deleted!", "The experience was successfully deleted.", "success");
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

Vue.component('professional-service', {
    template: '<div class="col s12">' +
        '<div class="card">' +
        '<div class="card-content">' +
        '<span class="card-title"><h4>{{ service.title }}</h4></span>' +
        '<p class="flow-text">{{ service.cost_currency }}{{ service.cost_amount.formatted }} <small>{{ service.cost_frequency !== "standard" ? "per " + service.cost_frequency.title_case() : "" }}</small></p>' +
        '<div class="chip" v-for="category in service.categories.data">{{ category.name }}</div>' +
        '</div>' +
        '<div class="card-action">' +
        '<a href="#" class="black-text" v-on:click.prevent="editField">EDIT</a>' +
        '<a href="#" class="red-text" v-on:click.prevent="deleteField">REMOVE</a>' +
        '</div>' +
        '</div>' +
        '</div>',
    props: {
        index: {
            type: Number,
            required: true
        },
        service: {
            type: Object,
            required: true
        }
    },
    methods: {
        editField: function () {
            this.$emit('edit-service', this.index);
        },
        deleteField: function () {
            var context = this;
            swal({
                title: "Are you sure?",
                text: "You are about to delete service " + this.service.title,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                axios.delete("/xhr/directory/services/" + context.service.id)
                    .then(function (response) {
                        console.log(response);
                        context.$emit('delete-service', context.index);
                        return swal("Deleted!", "The service was successfully deleted.", "success");
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

Vue.component('professional-service-request', {
    template: '<div class="col s12">' +
    '<div class="card" v-bind:class="{\'grey lighten-3\': request.is_read}">' +
    '<div class="card-content">' +
    '<span class="card-title activator"><h4>{{ request.service.data.title }} <i class="material-icons right">info_outline</i></h4></span>' +
    '<p class="flow-text">{{ request.company.data.name }}</p>' +
    '<p>{{ request.message.substr(0, 50)  + (request.message.length > 50 ? "..." : "") }}</p>' +
    '</div>' +
    '<div class="card-action">' +
    '<a href="#" class="red-text" v-on:click.prevent="reject" v-if="request.status === \'pending\'">REJECT</a>' +
    '<a href="#" class="black-text" v-on:click.prevent="accept" v-if="request.status === \'pending\'">ACCEPT</a>' +
    '<a v-bind:href="request.attachment_url" target="_blank" class="black-text" v-bind:class="{right: request.status === \'pending\'}" v-if="typeof request.attachment_url === \'string\'">Open Attachment</a>' +
    '</div>' +
    '<div class="card-reveal">' +
    '    <span class="card-title grey-text text-darken-4">Request Details<i class="material-icons right">close</i></span>' +
    '    <p><strong>Business</strong><br>{{ request.company.data.name }}</p>' +
    '    <p><strong>Email</strong><br>{{ request.company.data.email }}</p>' +
    '    <p><strong>Phone</strong><br>{{ request.company.data.phone }}</p>' +
    '    <p><strong>Date</strong><br>{{ moment(request.created_at).format("ddd DD MMM, YYYY") }}</p>' +
    '    <p><strong>Additional Message</strong><br>{{ request.message }}</p>' +
    '    <p v-if="request.attachment_url !== null"><strong>Supporting Document</strong><br><a v-bind:href="request.attachment_url" target="_blank">Download Attachment</a></p>' +
    '</div>'+
    '</div>' +
    '</div>',
    props: {
        index: {
            type: Number,
            required: true
        },
        request: {
            type: Object,
            required: true
        }
    },
    methods: {
        accept: function () {
            var context = this;
            swal({
                title: "Accept the request?",
                text: "You are about to accept this service request. An invoice will be send to the customer as well.",
                type: "info",
                showCancelButton: true,
                confirmButtonText: "Continue",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                axios.put("/xhr/directory/service-requests/" + context.request.id, {status: 'accepted'})
                    .then(function (response) {
                        console.log(response);
                        context.$emit('request-marked', context.index, response.data);
                        return swal("Done!", "The service request was successfully accepted. An invoice will be sent to the customer", "success");
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
                        return swal("Acceptance Failed", message, "warning");
                    });
            });
        },
        moment: function (dateString) {
            return moment(dateString);
        },
        reject: function () {
            var context = this;
            swal({
                title: "Reject the request?",
                text: "You are about to reject this service request. ",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Continue",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                axios.put("/xhr/directory/service-requests/" + context.request.id, {status: 'rejected'})
                    .then(function (response) {
                        console.log(response);
                        context.$emit('request-marked', context.index, response.data);
                        return swal("Done!", "The service request was successfully rejected.", "success");
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
                        return swal("Rejection Failed", message, "warning");
                    });
            });
        }
    }
});

/**
 * Settings Toggle component for the right-nav
 */
Vue.component('settings-toggle', {
    template: '<div class="switch right"><label>'+
    '<input v-on:change="updateSetting($event)" v-model="isChecked" type="checkbox" v-bind:name="name">'+
    '<span class="lever"></span>'+
    '</label></div>',
    props: {
        checked: Boolean,
        name: String
    },
    data: function () {
        return {
            isChecked: this.checked
        }
    },
    methods: {
        updateSetting: function (event) {
            var attrs = Hub.utilities.getElementAttributes(event.target);
            // get the attributes
            console.log(attrs);
            var context = this;
            console.log({name: attrs['name'] || 'none', enabled: event.target.checked});
            axios.post("/xhr/settings", {name: attrs['name'] || 'none', enabled: event.target.checked})
                .then(function (response) {
                    context.checked = true;
                    swal("Done", "The change was successfully saved.", "success");
                })
                .catch(function (error) {
                    var e = error.response.data.errors[0];
                    console.log(error.response.data.errors);
                    context.isChecked = false;
                    return swal("Update Failed", e.title, "warning");
                });
        }
    }
});

Vue.component('product-category', {
    template: '<div class="col s12">' +
    '<div class="card">' +
    '<div class="card-content">' +
    '<span class="card-title"><h4>{{ category.name }} ({{ category.products_count }})</h4></span>' +
    '<p class="flow-text">{{ category.slug }}</p>' +
    '</div>' +
    '<div class="card-action">' +
    '<a href="#" class="grey-text text-darken-3" v-on:click.prevent="edit">Edit</a>' +
    '<a href="#" class="red-text" v-on:click.prevent="deleteField" v-if="showDelete">REMOVE</a>' +
    '</div>' +
    '</div>' +
    '</div>',
    props: {
        category: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        showDelete: {
            type: Boolean,
            default: false
        }
    },
    data: function () {
        return {
            visible: this.seen
        }
    },
    methods: {
        edit: function () {
            var context = this;
            swal({
                    title: "Update Category",
                    text: "Enter new name [" + context.category.name + "]:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    showLoaderOnConfirm: true,
                    inputPlaceholder: "Custom Field Name"
                },
                function(inputValue){
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("You need to write something!");
                        return false
                    }
                    axios.put("/xhr/inventory/categories/"+context.category.id, {
                        name: inputValue,
                        update_slug: true
                    }).then(function (response) {
                        console.log(response);
                        context.$emit('update', context.index, response.data);
                        return swal("Success", "The category name was successfully updated.", "success");
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
        },
        deleteField: function () {
            var context = this;
            swal({
                title: "Are you sure?",
                text: "You are about to delete the category (" + context.category.name + ").",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                axios.delete("/xhr/inventory/categories/" + context.category.id)
                    .then(function (response) {
                        console.log(response);
                        context.$emit('remove', context.index);
                        return swal("Deleted!", "The category was successfully deleted.", "success");
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

Vue.component('product-price-control', {
    template: '<div class="row">' +
    '    <div class="input-field col s12 m4">' +
    '        <select v-bind:id="id_currency" name="currencies[]" v-model="currency" class="browser-default">' +
    '            <option value="EUR">EUR</option>' +
    '            <option value="NGN">NGN</option>' +
    '            <option value="USD">USD</option>' +
    '        </select>' +
    '    </div>' +
    '    <div class="input-field col s12 m7">' +
    '        <input v-bind:id="id_price" type="number" name="prices[]" maxlength="10" min="0" step="0.01" v-model="price">' +
    '        <label v-bind:for="id_price">Unit Price</label>' +
    '    </div>' +
    '    <div class="input-field col s12 m1">' +
    '        <a href="#" class="grey-text text-darken-3" v-on:click.prevent="removeEntry"><i class="material-icons small">clear</i></a>' +
    '    </div>'+
    '</div>',
    data: function () {
        return {
            price: this.opening_price,
            currency: this.opening_currency
        };
    },
    props: {
        id_currency: {
            type: String,
            required: true
        },
        id_price: {
            type: String,
            required: true
        },
        opening_currency: {
            type: String,
            default: 'NGN'
        },
        opening_price: {
            type: [Number, String],
            default: 0
        },
        index: {
            type: Number,
            required: true
        }
    },
    mounted: function () {
        //$('select#'+this.id_currency).material_select();
    },
    methods: {
        removeEntry: function () {
            this.$emit('remove', this.index);
        }
    }
});

Vue.component('cart-item', {
    template: '<div class="row">' +
    '    <div class="input-field col s12 m6">' +
    '        <select name="products[]" class="browser-default" v-model="product" v-on:change="updatePrice">' +
    '            <option value="" disabled>Select a Product</option>' +
    '            <option v-for="product in products" :key="product.id" :value="product.id">' +
    '                {{ product.name }}' +
    '            </option>' +
    '        </select>' +
    '   </div>' +
    '   <div class="input-field col s12 m2">' +
    '        <input v-bind:id="quantity_id" name="quantities[]" type="number" min="1" v-model="quantity" v-on:keyup="syncCart" v-on:change="syncCart">' +
    '        <label v-bind:for="quantity_id">Quantity</label>' +
    '   </div>' +
    '   <div class="input-field col s12 m3">' +
    '        <input v-bind:id="unit_price_id" name="unit_prices[]" type="number" min="0" step="0.01" v-model="unit_price" v-on:keyup="syncCart" v-on:change="syncCart">' +
    '        <label v-bind:for="unit_price_id">Unit Price</label>' +
    '   </div>' +
    '    <div class="input-field col s12 m1">' +
    '        <a href="#" class="grey-text text-darken-3" v-on:click.prevent="removeItem"><i class="material-icons small">clear</i></a>' +
    '    </div>'+
    '</div>',
    data: function () {
        return {
            products: this.$parent.products,
            product: '',
            quantity: 0,
            itemIndex: this.index,
            unit_price: 0,
            currency: this.$parent.currency
        }
    },
    props: {
        quantity_id: {
            type: String,
            required: true
        },
        unit_price_id: {
            type: String,
            required: true
        },
        index: {
            type: Number,
            required: true
        }
    },
    methods: {
        updatePrice: function () {
            console.log('checking for price...');
            context = this;
            var product = this.products.find(function (p) {
                return p.id === context.product;
            });
            if (typeof product === 'undefined') {
                return;
            }
            var price = typeof product.default_unit_price !== 'undefined' && typeof product.default_unit_price.raw !== 'undefined' ?
                parseFloat(product.default_unit_price.raw) : 0;
            // set the initial price
            if (typeof product.prices !== 'undefined' && typeof product.prices.data !== 'undefined') {
                for (var i = 0; i < product.prices.data.length; i++) {
                    if (product.prices.data[i].currency !== this.currency) {
                        continue;
                    }
                    price = parseFloat(product.prices.data[i].unit_price.raw);
                    break;
                }
            }
            this.unit_price = price;
            this.syncCart();
        },
        removeItem: function () {
            this.$emit('remove-item', this.itemIndex);
        },
        syncCart: function () {
            this.$emit('sync-cart', this.itemIndex, this.quantity, this.unit_price, this.product);
        }
    }
});

Vue.component('integration-installer', {
    template: '<div class="col s12" v-if="!installed">' +
    '                <div class="card">' +
    '                    <div class="card-content center">' +
    '                        <h6 class="card-title font-weight-400">{{ display_name }}</h6>' +
    '                        <p>{{ description}}</p>' +
    '                    </div>' +
    '                    <div class="card-tabs">' +
    '                        <ul class="tabs tabs-fixed-width">' +
    '                            <li class="tab"><a class="active" v-bind:href="integration_tab_href">Integration</a></li>' +
    '                            <li class="tab"><a v-bind:href="config_tab_href" style="">Install</a></li>' +
    '                        </ul>'+
    '                    </div>' +
    '                    <div class="card-content">' +
    '                        <div v-bind:id="integration_tab_id" class="center active">' +
    '                            <img v-bind:src="image" width="300">' +
    '                        </div>' +
    '                        <div v-bind:id="config_tab_id" class="center">' +
    '                            <form class="col s12 mb-5" action="" method="post" v-on:submit.prevent="installApp">' +
    '                                <div class="row">' +
    '                                    <div v-for="(setting, index) in integration_configurations" :key="name + index" class="input-field col s12">' +
    '                                        <input v-bind:id="name + index" type="text" v-model="integration_configurations[index].value" required>' +
    '                                        <label v-bind:for="name + index">{{ setting.label }}</label>' +
    '                                    </div>' +
    '                                </div>' +
    '                                <div class="row">' +
    '                                    <div class="col s12">' +
    '                                        <div class="progress" v-if="installing">' +
    '                                            <div class="indeterminate"></div>' +
    '                                        </div>' +
    '                                        <button class="btn blue waves-effect waves-light center" type="submit" v-if="!installing">Install</button>' +
    '                                    </div>' +
    '                                </div>' +
    '                            </form>' +
    '                        </div>' +
    '                    </div>' +
    '                </div>' +
    '            </div>',
    data: function () {
        return {
            integration_type: this.type,
            integration_name: this.name,
            integration_configurations: this.configurations,
            installing: false
        };
    },
    props: {
        type: {
            type: String,
            required: true
        },
        image: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        },
        display_name: {
            type: String,
            required: false,
            default: function () {
                return this.name.title_case();
            }
        },
        description: {
            type: String,
            required: false,
            default: ''
        },
        configurations: {
            type: Array,
            required: false,
            default: []
        },
        installed: {
            type: Boolean,
            required: false,
            default: false
        },
        index: {
            type: Number,
            required: true
        }
    },
    computed: {
        integration_tab_id: function () {
            return this.name + '-integration';
        },
        integration_tab_href: function () {
            return '#' + this.integration_tab_id;
        },
        config_tab_id: function () {
            return this.name + '-config';
        },
        config_tab_href: function () {
            return '#' + this.config_tab_id;
        }
    },
    methods: {
        installApp: function () {
            var context = this;
            context.installing = true;
            axios.post("/xhr/integrations", {
                type: context.integration_type,
                name: context.integration_name,
                configurations: context.integration_configurations
            }).then(function (response) {
                console.log(response);
                context.installed = true;
                context.installing = false;
                context.$emit('installed', context.index);
                Materialize.toast('Added the ' + context.display_name + ' integration to your account.', 4000);
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
                context.installing = false;
                Materialize.toast("Oops!" + message, 4000);
            });
        }
    }
});

Vue.component('integration-manager', {
    template: '<div class="col s12">' +
    '                <div class="card">' +
    '                    <div class="card-content center">' +
    '                        <h6 class="card-title font-weight-400">{{ display_name}}</h6>' +
    '                        <p>{{ description}}</p>' +
    '                        <a class="waves-effect waves-light btn red fixed-margin mt-3" v-on:click.prevent="uninstall">Uninstall</a>' +
    '                    </div>' +
    '                    <div class="card-tabs">' +
    '                        <ul class="tabs tabs-fixed-width">' +
    '                            <li class="tab"><a class="active" v-bind:href="integration_tab_href">Integration</a></li>' +
    '                            <li class="tab"><a v-bind:href="config_tab_href" style="">Settings</a></li>' +
    '                        </ul>'+
    '                    </div>' +
    '                    <div class="card-content">' +
    '                        <div v-bind:id="integration_tab_id" class="center active">' +
    '                            <img v-bind:src="image" width="300">' +
    '                        </div>' +
    '                        <div v-bind:id="config_tab_id" class="center">' +
    '                            <form class="col s12 mb-5" action="" method="post" v-on:submit.prevent="updateApp">' +
    '                                <div class="row">' +
    '                                    <div v-for="(setting, index) in integration_configurations" :key="name + index" class="input-field col s12">' +
    '                                        <input v-bind:id="name + index" type="text" v-model="integration_configurations[index].value" required>' +
    '                                        <label v-bind:for="name + index">{{ setting.label }}</label>' +
    '                                    </div>' +
    '                                </div>' +
    '                                <div class="row">' +
    '                                    <div class="col s12">' +
    '                                        <div class="progress" v-if="updating">' +
    '                                            <div class="indeterminate"></div>' +
    '                                        </div>' +
    '                                        <button class="btn blue waves-effect waves-light center" type="submit" v-if="!updating">Update</button>' +
    '                                    </div>' +
    '                                </div>' +
    '                            </form>' +
    '                        </div>' +
    '                    </div>' +
    '                </div>' +
    '            </div>',
    data: function () {
        return {
            integration_type: this.type,
            integration_name: this.name,
            integration_configurations: this.configurations,
            updating: false
        };
    },
    props: {
        id: {
            type: [String, Number],
            required: true
        },
        type: {
            type: String,
            required: true
        },
        image: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        },
        display_name: {
            type: String,
            required: false,
            default: function () {
                return this.name.title_case();
            }
        },
        description: {
            type: String,
            required: false,
            default: ''
        },
        configurations: {
            type: Array,
            required: false,
            default: []
        },
        installed: {
            type: Boolean,
            required: false,
            default: false
        },
        index: {
            type: Number,
            required: true
        }
    },
    computed: {
        integration_tab_id: function () {
            return this.name + '-integration';
        },
        integration_tab_href: function () {
            return '#' + this.integration_tab_id;
        },
        config_tab_id: function () {
            return this.name + '-config';
        },
        config_tab_href: function () {
            return '#' + this.config_tab_id;
        }
    },
    methods: {
        updateApp: function () {
            var context = this;
            context.updating = true;
            axios.put("/xhr/integrations/" + context.id, {
                type: context.integration_type,
                name: context.integration_name,
                configurations: context.integration_configurations
            }).then(function (response) {
                console.log(response);
                context.updating = false;
                Materialize.toast('Updated the settings for the ' + context.display_name + ' integration.', 4000);
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
                Materialize.toast("Oops!" + message, 4000);
            });
        },
        uninstall: function () {
            var context = this;
            swal({
                title: "Are you sure?",
                text: "You are about to uninstall the " + this.display_name + " integration.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, uninstall it!",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                context.updating = true;
                axios.delete("/xhr/integrations/" + context.id)
                    .then(function (response) {
                        console.log(response);
                        context.visible = false;
                        context.$emit('remove-integration', context.index);
                        return swal("Uninstalled!", "The integration was successfully uninstalled.", "success");
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
                        return swal("Uninstall Failed", message, "warning");
                    });
            });
        }
    }
});

Vue.component('plan-chooser', {
    template: '<article class="col s12">' +
    '   <div class="card z-depth-1">' +
    '       <div class="card-image teal waves-effect">' +
    '           <div class="card-title">{{ display_name }}</div>' +
    '               <div class="price">' +
    '                   <sup>NGN</sup>{{ profile.price_monthly.formatted.substring(0, profile.price_monthly.formatted.indexOf(".")) }}' +
    '                   <sub>/mo</sub>' +
    '               </div>' +
    '               <div v-if="short_description.length > 0" class="price-desc tooltipped" data-position="bottom" data-delay="50" v-bind:data-tooltip="description">{{ short_description }}</div>' +
    '           </div>' +
    '           <div class="card-content">' +
    '               <ul class="collection">' +
    '                   <li class="collection-item" v-for="(text, index) in features" :key="index">{{ text }}</li>'+
    '               </ul>' +
    '           </div>' +
    '           <div class="card-action center-align">' +
    '               <p v-if="typeof footnote !== \'undefined\' && footnote.length > 0">{{ footnote }}</p>' +
    '               <a class="waves-effect waves-light light-blue btn" href="#!" v-bind:class="{disabled: isCurrentPlan}" v-on:click.prevent="setPlan">Select Plan</a>' +
    '           </div>'+
    '       </div>' +
    '   </div>' +
    '</article>',
    data: function () {
        return {
            business: this.$parent.business
        };
    },
    computed: {
        isCurrentPlan: function () {
            return this.business.plan.data.id === this.profile.id;
        },
        dropdownId: function () {
            return 'dropdown' + this.profile.id;
        }
    },
    props: {
        name: {
            type: String,
            required: true
        },
        display_name: {
            type: String,
            required: false,
            default: function () {
                return this.name.title_case();
            }
        },
        features: {
            type: Array,
            required: false,
            default: function () {
                return [];
            }
        },
        short_description: {
            type: String,
            required: false,
            default: ''
        },
        description: {
            type: String,
            required: false,
            default: ''
        },
        index: {
            type: Number,
            required: true
        },
        profile: {
            type: Object,
            required: true
        },
        footnote: {
            type: String,
            required: false,
            default: ''
        }
    },
    methods: {
        setPlan: function () {
            var context = this;
            swal({
                title: "Switch Plan",
                text: "Would you like to switch to the " + this.display_name + " plan?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, subscribe.",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                axios.post("/xhr/plans", {
                    plan: context.name
                }).then(function (response) {
                        console.log(response);
                        window.location = '/home';
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
                        return swal("Action Failed", message, "warning");
                    });
            });
        }
    }
});

Vue.component('blog-category', {
    template: '<div class="col s12">' +
    '<div class="card">' +
    '<div class="card-content">' +
    '<span class="card-title"><h4>{{ category.name }} ({{ category.posts_count }})</h4></span>' +
    '<p class="flow-text">{{ category.slug }}</p>' +
    '</div>' +
    '<div class="card-action">' +
    '<a href="#" class="grey-text text-darken-3" v-on:click.prevent="edit">Edit</a>' +
    '<a href="#" class="red-text" v-on:click.prevent="deleteField" v-if="showDelete">REMOVE</a>' +
    '</div>' +
    '</div>' +
    '</div>',
    props: {
        category: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        },
        showDelete: {
            type: Boolean,
            default: false
        }
    },
    data: function () {
        return {
            visible: this.seen
        }
    },
    methods: {
        edit: function () {
            var context = this;
            swal({
                    title: "Update Category",
                    text: "Enter new name [" + context.category.name + "]:",
                    type: "input",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    showLoaderOnConfirm: true,
                    inputPlaceholder: "Custom Field Name"
                },
                function(inputValue){
                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("You need to write something!");
                        return false
                    }
                    axios.put("/xhr/ecommerce/blog/categories/"+context.category.id, {
                        name: inputValue,
                        update_slug: true
                    }).then(function (response) {
                        console.log(response);
                        context.$emit('update', context.index, response.data);
                        return swal("Success", "The category name was successfully updated.", "success");
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
        },
        deleteField: function () {
            var context = this;
            swal({
                title: "Are you sure?",
                text: "You are about to delete the category (" + context.category.name + ").",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function() {
                axios.delete("/xhr/ecommerce/blog/categories/" + context.category.id)
                    .then(function (response) {
                        console.log(response);
                        context.$emit('remove', context.index);
                        return swal("Deleted!", "The category was successfully deleted.", "success");
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

Vue.component('advert-card', {
    template: '<div class="col s12 m4">' +
    '                <div class="card">' +
    '                    <div class="card-image">' +
    '                        <img v-bind:src="advert.image_url" v-bind:alt="advert.title + \' image\'" />' +
    '                        <span class="card-title">{{ advert.title }}</span>' +
    '                    </div>' +
    '                    <div class="card-action">' +
    '                        <a v-if="typeof advert.redirect_url !== \'undefined\' && advert.redirect_url !== null"' +
    '                           v-bind:href="advert.redirect_url" target="_blank">Open Link</a>' +
    '                        <a href="#" v-on:click.prevent="editAdvert">Edit</a>' +
    '                        <a href="#" class="red-text right" v-on:click.prevent="deleteAdvert">Delete</a>\n' +
    '                    </div>' +
    '                </div>' +
    '            </div>',
    props: {
        advert: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        }
    },
    methods: {
        editAdvert: function () {
            this.$emit('edit-advert', this.index);
        },
        deleteAdvert: function () {
            this.$emit('delete-advert', this.index);
        }
    }
});


Vue.component('webmail-account', {
    template: '<div class="col s12">' +
    '                <div class="card">' +
    '                    <div class="card-content">' +
    '                        <span class="card-title">{{ email.login }}</span>' +
    '                        <p><strong>Storage Quota: </strong>{{ email.diskquota }}MB<br><strong>Used Storage: </strong>{{ email.diskusedpercent_float }}%</p>' +
    '                    </div>' +
    '                    <div class="card-action">' +
    '                        <a target="_blank" :href="\'http://\' + email.domain + \'/webmail\'">WebMail</a>' +
    '                        <a class="red-text right" v-on:click.prevent="deleteEmail" href="#">Delete</a>' +
    '                    </div>' +
    '                </div>' +
    '            </div>',
    props: {
        email: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        }
    },
    methods: {
        deleteEmail: function () {
            this.$emit('delete-email', this.index);
        }
    }
});

Vue.component('app-store-app', {
    template: '<div class="col s12">' +
    '                <div class="card">' +
    '                    <div class="card-content center">' +
    '                        <h6 class="card-title font-weight-400">{{ app.name }}</h6>' +
    '                        <p><a :href="app.homepage_url" v-if="app.homepage_url !== null" target="_blank">{{ app.homepage_url }}</a></p>' +
    '                        <a class="waves-effect waves-light btn fixed-margin mt-3" v-if="app.is_installed && app.homepage_url !== null && app.type === \'web\'" :href="app.homepage_url + \'/install/setup?token=\' + auth_token" target="_blank">Launch</a>' +
    '                        <a class="waves-effect waves-light btn red fixed-margin mt-3" v-if="app.is_installed" v-on:click.prevent="uninstall">Uninstall</a>' +
    '                        <a class="waves-effect waves-light btn fixed-margin mt-3" v-if="!app.is_installed" v-on:click.prevent="install">Install</a>' +
    '                    </div>' +
    '                    <div class="card-tabs">' +
    '                        <ul class="tabs tabs-fixed-width">' +
    '                            <li class="tab"><a class="active" v-bind:href="branding_tab_href">Product</a></li>' +
    '                            <li class="tab"><a v-bind:href="description_tab_href" style="">Description</a></li>' +
    '                        </ul>'+
    '                    </div>' +
    '                    <div class="card-content">' +
    '                        <div v-bind:id="branding_tab_id" class="center active">' +
    '                            <img v-bind:src="app.icon_url" width="300">' +
    '                        </div>' +
    '                        <div v-bind:id="description_tab_id" class="">' +
    '                            <p>{{ app.description}}</p>' +
    '                        </div>' +
    '                    </div>' +
    '                </div>' +
    '            </div>',
    data: function () {
        return {
            updating: false,
            auth_token: ''
        };
    },
    computed: {
        branding_tab_id: function () {
            return this.app.type + '_' + this.index + '-branding';
        },
        branding_tab_href: function () {
            return '#' + this.branding_tab_id;
        },
        description_tab_id: function () {
            return  this.app.type + '_' + this.index + '-description';
        },
        description_tab_href: function () {
            return '#' + this.description_tab_id;
        }
    },
    mounted: function () {
        $('ul.tabs').tabs();
        this.auth_token = this.$parent.authorization_token;
    },
    props: {
        app: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        }
    },
    methods: {
        install: function () {
            this.$emit('install-app', this.index);
        },
        uninstall: function () {
            this.$emit('uninstall-app', this.index);
        }
    }
});