<div class="nav-item d-none d-flex" id="modules-assistant">
    <a v-on:click.prevent="modulesAssistant" class="btn btn-sm btn-outline-primary" :disabled="loadingAssistant">Help</a>
    @include('modules-assistant::modals.assistant')
</div>