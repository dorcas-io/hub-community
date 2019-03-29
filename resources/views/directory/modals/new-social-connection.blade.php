<div id="new-social-connection" class="modal">
    <form class="col s12" action="" method="post" v-on:submit.prevent="addSocialConnection">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Add Social Connection</h4>
            <div class="row">
                <div class="col s12 m12">
                    <div class="input-field col s12 m3">
                        <select name="channel" id="channel" class="browser-default" v-model="modals.social.channel">
                            <option value="" disabled="disabled">Social Network</option>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="googleplus">Google+</option>
                            <option value="twitter">Twitter</option>
                            <option value="youtube">Youtube</option>
                        </select>
                    </div>
                    <div class="input-field col s12 m3">
                        <input id="id" type="text" name="id" v-model="modals.social.id">
                        <label for="id">ID (e.g. @username)</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <input id="url" type="url" name="url" v-model="modals.social.url">
                        <label for="url">Profile URL</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="progress" v-if="modals.social.is_processing">
                <div class="indeterminate"></div>
            </div>
            <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat" v-if="!modals.social.is_processing">Close</a>
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="save_product"
                    value="1" v-if="!modals.social.is_processing">Save Connection</button>
        </div>
    </form>
</div>