<template>
    <a v-bind:href="href" class="dropdown-item d-flex" v-bind="notification.action" v-on:click.prevent="doAction">
        <span class="avatar mr-3 align-self-center" v-bind:style="styles.actor"></span>
        <div>
            <strong>{{ notification.actor }}</strong> {{ notification.activity }}.
            <div class="small text-muted">{{ momentsAgo() }}</div>
        </div>
    </a>
</template>

<script>
    export default {
        name: "TablerNotificationItem",
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
            if (typeof this.notification.actor_photo !== 'undefined' && this.notification.actor_photo !== null) {
                this.styles.actor['background-image'] = 'url(' + this.notifications.actor_photo + ')';
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

            }
        }
    }
</script>

<style scoped>

</style>
