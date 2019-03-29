<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ title }}</h3>
            <div class="card-options">
                <label class="custom-switch m-0">
                    <input type="checkbox" value="1" class="custom-switch-input" v-model="checked" v-on:change="checkChanged">
                    <span class="custom-switch-indicator"></span>
                </label>
            </div>
        </div>
        <div class="card-body">
            {{ body }}
        </div>
    </div>
</template>

<script>
    export default {
        name: "CardWithSwitch",
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
    }
</script>

<style scoped>

</style>
