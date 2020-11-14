<div id="add-product-image" class="modal">
    <form class="col s12" action="{{ route('apps.inventory.single.images', [$product->id]) }}" method="post"
          enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Add an Image</h4>
            <div class="row">
                <div class="col s12">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>Product Image</span>
                            <input type="file" name="image" accept="image/*" >
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Select Image" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="action"
                    value="add_product_image">Upload Image</button>
        </div>
    </form>
</div>