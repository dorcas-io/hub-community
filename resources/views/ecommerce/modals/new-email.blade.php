<div id="manage-email-modal" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>New Email Account</h4>
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <input id="email-username" type="text" name="username" maxlength="80" required
                               v-model="email.username">
                        <label for="email-username" >Email Username</label>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <select class="browser-default" id="email-domain" name="domain" required v-model="email.domain">
                            <option value="" disabled>Select the Domain</option>
                            <option v-for="domain in domains" :key="domain.id" :value="domain.domain">@{{ domain.domain }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <input id="email-password" type="password" name="password" maxlength="255">
                        <label for="email-password">Password</label>
                        <small>The password should have at least one uppercase-character, and a number</small>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <input id="email-quota" type="number" name="quota" min="25" max="1024" v-model="email.quota">
                        <label for="email-quota">Storage Quota (in MB)</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="action"
                    value="create_email" >Create Email Account</button>
        </div>
    </form>
</div>