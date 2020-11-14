<div id="new-credential" class="modal">
    <form class="col s12" action="" method="post" v-on:submit.prevent="addCredential">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Add Credential</h4>
            <div class="row">
                <div class="col s12 m12">
                    <div class="input-field col s12 m7">
                        <input id="title" type="text" name="title" v-model="modals.credential.title" required>
                        <label for="title">Institution</label>
                    </div>
                    <div class="input-field col s12 m5">
                        <select name="channel" id="channel" class="browser-default" v-model="modals.credential.type" required>
                            <option value="" disabled="disabled">Certification Type</option>
                            <option value="degree">Degree</option>
                            <option value="course">Personal Coursework</option>
                            <option value="professional">Professional</option>
                        </select>
                    </div>
                    <div class="input-field col s12 m7">
                        <input id="certification" type="text" name="certification" v-model="modals.credential.certification" required>
                        <label for="certification">Certification e.g. Ph.D)</label>
                    </div>
                    <div class="input-field col s12 m5">
                        <select name="year" id="year" class="browser-default" v-model="modals.credential.year" required>
                            <option disabled="disabled" value="">Certification Year</option>
                            @for ($i = date('Y'); $i >= 1980; $i--))
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="input-field col s12">
                        <textarea id="description" name="description" class="materialize-textarea"
                                  v-model="modals.credential.description"></textarea>
                        <label for="description">Description</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="progress" v-if="modals.credential.is_processing">
                <div class="indeterminate"></div>
            </div>
            <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat" v-if="!modals.credential.is_processing">Close</a>
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="save_credential"
                    value="1" v-if="!modals.credential.is_processing">Save Credential</button>
        </div>
    </form>
</div>