Vue.component('tabler-notification-item', {
    template: '<a v-bind:href="href" class="dropdown-item d-flex" v-bind="notification.action" v-on:click.prevent="doAction">' +
    '   <span class="avatar mr-3 align-self-center" v-bind:style="styles.actor"></span>' +
    '   <div>' +
    '       <strong>{{ notification.actor }}</strong> {{ notification.activity }}.' +
    '       <div class="small text-muted">{{ momentsAgo() }}</div>' +
    '   </div>' +
    '</a>',
    data: function () {
        return {
            styles: {
                actor: {}
            },
            href: '#'
        };
    },
    props: {
        notification: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        }
    },
    mounted: function () {
        if (typeof this.notification.photo !== 'undefined' && this.notification.photo !== null) {
            this.styles.actor['background-image'] = 'url(' + this.notifications.photo + ')';
        }
        if (typeof this.notification.action['data-url'] !== 'undefined') {
            this.href = this.notification.action['data-url'];
        }
    },
    methods: {
        momentsAgo: function () {
            return moment(this.notification.timestamp).fromNow();
        },
        doAction: function () {
            if (this.href !== '#') {
                window.location = this.href;
            }
            this.$emit('notification-clicked', this.index, this.notification.action);
        }
    }
});

Vue.component('tabler-card-switch', {
    template: '<div class="card">' +
    '        <div class="card-header">' +
    '            <h3 class="card-title">{{ title }}</h3>' +
    '            <div class="card-options">' +
    '                <label class="custom-switch m-0">' +
    '                    <input type="checkbox" value="1" class="custom-switch-input" v-model="checked" v-on:change="checkChanged">' +
    '                    <span class="custom-switch-indicator"></span>' +
    '                </label>' +
    '            </div>' +
    '        </div>' +
    '        <div class="card-body">' +
    '            {{ body }}' +
    '        </div>' +
    '    </div>',
    props: {
        title: {
            type: String,
            required: true
        },
        body: {
            type: String,
            required: true
        },
        checked: {
            type: Boolean,
            required: false,
            default: function () {
                return false;
            }
        },
        index: {
            type: Number,
            required: true
        },
        data: {
            type: Object,
            required: false,
            default: function () {
                return {};
            }
        }
    },
    methods: {
        checkChanged: function () {
            this.$emit('card-check-changed', this.index, this.data);
        }
    }
});

Vue.component('application-card', {
    template: '<div class="card">' +
    '        <div class="card-status" :class="{\'bg-red\': !application.is_published, \'bg-success\': application.is_published}"></div>' +
    '        <div class="card-header">' +
    '            <h3 class="card-title"><i class="fe" :class="{\'fe-smartphone\': application.type === \'mobile\', \'fe-globe\': application.type === \'web\', \'fe-monitor\': application.type === \'desktop\', \'fe-terminal\': application.type === \'cli\'}"></i> {{ application.name }}</h3>' +
    '            <div class="card-options">' +
    '                <a href="#" class="btn btn-primary btn-sm" v-if="!is_processing" v-on:click.prevent="togglePublish">{{ !application.is_published ? "Publish" : "Unpublish" }}</a>' +
    '                <a href="#" class="btn-sm ml-0" v-on:click.prevent="editApp" v-if="!is_editing"><i class="fe fe-edit"></i></a>' +
    '                <a href="#" class="btn-sm ml-0" v-on:click.prevent="cancelEditing" v-if="is_editing"><i class="fe fe-x-circle"></i></a>' +
    '                <a href="#" class="btn-sm ml-0" v-if="!is_processing" v-on:click.prevent="deleteApp"><i class="fe fe-trash-2"></i></a>' +
    '            </div>' +
    '        </div>' +
    '        <div class="card-body">' +
    '           <div class="dimmer active" v-if="is_processing">' +
    '               <div class="loader"></div>' +
    '               <div class="dimmer-content">{{ application.description }}</div>' +
    '           </div>' +
    '           <p v-if="!is_processing">{{ application.description }}</p>' +
    '           <img v-if="!is_processing && application.icon_url !== null" :src="application.icon_url" style="border: 0;" :alt="application.name + \' icon\'" class="img-thumbnail">' +
    '        </div>' +
    '        <div class="card-footer" v-if="!is_processing">' +
    '            <strong>Application ID: </strong> {{ typeof application.oauth_client !== "undefined" && application.oauth_client.data !== null ? application.oauth_client.data.id : "" }}<br>' +
    '            <strong>Application Secret: </strong> {{ typeof application.oauth_client !== "undefined" && application.oauth_client.data !== null ? application.oauth_client.data.secret : "" }}' +
    '        </div>' +
    '    </div>',
    data: function () {
        return {is_editing: false, is_processing: false, icon: 'fe-globe'};
    },
    props: {
        application: {
            type: Object,
            required: true
        },
        index: {
            type: Number,
            required: true
        }
    },
    mounted: function () {
        if (typeof this.application.is_processing !== 'undefined') {
            this.is_processing = this.application.is_processing;
        }
        if (this.application.type === 'mobile') {
            this.icon = 'smartphone';
        } else if (this.application.type === 'desktop') {
            this.icon = 'monitor';
        } else if (this.application.type === 'cli') {
            this.icon = 'terminal';
        }
    },
    updated: function () {
        if (typeof this.application.is_processing !== 'undefined') {
            this.is_processing = this.application.is_processing;
        }
    },
    methods: {
        updateProcessing: function (status) {
            this.is_processing = status;
            if (typeof this.application.is_processing !== 'undefined') {
                this.application.is_processing = status;
            }
        },
        togglePublish: function () {
            this.updateProcessing(true);
            this.$emit('toggle-app-publish', this.index);
        },
        editApp: function () {
            this.updateProcessing(true);
            this.is_editing = true;
            this.$emit('edit-app', this.index);
        },
        cancelEditing: function () {
            this.updateProcessing(false);
            this.is_editing = false;
            this.$emit('cancel-edit-app', this.index);
        },
        deleteApp: function () {
            this.$emit('delete-app', this.index);
        }
    }
});
