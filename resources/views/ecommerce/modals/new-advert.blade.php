<div id="manage-ad-modal" class="modal">
    <form class="col s12" action="" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>@{{ typeof advert.id !== 'undefined' ? 'Edit Advert' : 'New Advert' }}</h4>
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <input id="ad-title" type="text" name="title" maxlength="80" v-model="advert.title">
                        <label for="ad-title" v-bind:class="{'active': advert.title.length > 0}">Advert Title</label>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <select class="browser-default" id="ad-type" name="type" v-model="advert.type" required
                                v-on:change="adjustRecommendation">
                            <option value="" disabled>Select the Advert Type</option>
                            <option value="sidebar">Sidebar Vertical Ad</option>
                            <option value="footer">Footer Horizontal Ad</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <input id="ad-url" type="text" name="redirect_url" maxlength="400" v-model="advert.redirect_url">
                        <label for="ad-url" v-bind:class="{'active': advert.redirect_url.length > 0}">Advert Redirect URL (when the Ad is clicked)</label>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <select class="browser-default" id="ad-is_default" name="is_default" v-model="advert.is_default">
                            <option value="0" selected>Leave it as is</option>
                            <option value="1">Make Default Ad for Type</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>File</span>
                            <input type="file" name="image" id="ad-image" accept="image/*" >
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Select Ad image" />
                            <small>We recommend a <strong>@{{ recommendedDim }}</strong> image, or similar</small>
                        </div>
                    </div>
                </div>
                <div class="col s12 m6">
                    <img v-if="typeof advert.image_url !== 'undefined' && advert.image_url !== null"
                         class="responsive-img" :src="advert.image_url">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="advert_id" id="ad-advert-id" :value="advert.id" v-if="typeof advert.id !== 'undefined'" />
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="save_ad"
                    value="1" >@{{ typeof advert.id !== 'undefined' ? 'Update Advert' : 'Create Ad' }}</button>
        </div>
    </form>
</div>