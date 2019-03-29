<div id="add-product" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Add Product</h4>
            <div class="row">
                <div class="col s12 m12">
                    <div class="input-field col s12 m7">
                        <input id="name" type="text" name="name" maxlength="80" required>
                        <label for="name">Product Name</label>
                    </div>
                    <div class="input-field col s12 m2">
                        <select id="currency" name="currency">
                            <option value="NGN">NGN</option>
                        </select>
                        <label for="currency">Currency</label>
                    </div>
                    <div class="input-field col s12 m3">
                        <input id="price" type="number" name="price" maxlength="10" min="0">
                        <label for="price">Unit Price</label>
                    </div>
                    <div class="input-field col s12">
                        <textarea id="description" name="description" class="materialize-textarea"></textarea>
                        <label for="description">Product Description</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="save_product"
                    value="1">Save Product</button>
        </div>
    </form>
</div>