<div id="manage-inventory" class="modal">
    <form class="col s12" action="{{ route('apps.inventory.single.stocks', [!empty($product) ? $product->id : '']) }}" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Add/Remove Stocks</h4>
            <div class="row">
                <div class="col s12 m12">
                    <div class="input-field col s12 m7">
                        <select id="action" name="action">
                            <option value="add">Add/Increase Stocks</option>
                            <option value="subtract">Remove Stocks</option>
                        </select>
                        <label for="action">Action</label>
                    </div>
                    <div class="input-field col s12 m5">
                        <input id="quantity" type="number" name="quantity" maxlength="10" min="1">
                        <label for="quantity">Quantity</label>
                    </div>
                    <div class="input-field col s12">
                        <textarea id="description" name="description" class="materialize-textarea"></textarea>
                        <label for="description">Describe the Activity</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="save_action"
                    value="1">Save Activity</button>
        </div>
    </form>
</div>