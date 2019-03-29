<div id="new-access-request" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Select the Modules</h4>
            <div class="row">
                <div class="col s12 m6" v-for="module in available_modules" :key="module.id" v-if="!module.is_readonly">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">@{{ module.name }}</span>
                            <div class="switch">
                                <label>
                                    Disabled
                                    <input type="checkbox" name="modules[]"
                                           v-bind:value="module.id" v-bind:checked="module.enabled">
                                    <span class="lever"></span>
                                    Enabled
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" v-model="business.id" name="business_id" />
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="action"
                    value="request_access">Send Request</button>
        </div>
    </form>
</div>