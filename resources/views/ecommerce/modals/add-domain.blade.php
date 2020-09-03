<div id="add-domain-modal" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Add a Domain you Own</h4>
            <div class="row">
                <div class="col s12 m6 offset-m3">
                    <div class="input-field col s12">
                        <input id="domain" type="text" name="domain" maxlength="50" v-model="domain" required
                               v-on:keyup="removeStatus()">
                        <label for="domain">Your Domain</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="add_domain"
                    value="1">Add Domain</button>
        </div>
    </form>
</div>