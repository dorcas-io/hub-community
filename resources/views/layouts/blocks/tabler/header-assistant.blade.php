<div class="nav-item d-none d-flex" id="modules-assistant">
    <a v-on:click.prevent="modulesAssistant" class="btn btn-sm btn-outline-primary" :disabled="loadingAssistant">
    	<span class="d-none d-md-inline">Help</span>
        <span class="d-inline d-md-none"> &nbsp; <i class="dropdown-icon fe fe-help-circle"></i></span>
    </a>
    @include('modules-assistant::modals.assistant')
</div>