<div id="add-subaccount" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>New Sub-Account</h4>
            <div class="row">
                <div class="col s12 m12">
                    <div class="input-field col s12">
                        <input id="name" name="name" maxlength="80" type="text" required>
                        <label for="name">Account Name</label>
                    </div>
                    <input type="hidden" name="parent_account_id" value="{{ $baseAccount->id }}">
                    <input type="hidden" name="entry_type" value="{{ $baseAccount->entry_type }}">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="save_product"
                    value="1">Add Account</button>
        </div>
    </form>
</div>