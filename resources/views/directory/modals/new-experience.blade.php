<div id="new-experience" class="modal">
    <form class="col s12" action="" method="post" v-on:submit.prevent="addExperience">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Add Experience</h4>
            <div class="row">
                <div class="col s12 m12">
                    <div class="input-field col s12 m6">
                        <input id="company" type="text" name="company" v-model="modals.experience.company" required>
                        <label for="company">Company</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <input id="designation" type="text" name="designation" v-model="modals.experience.designation" required>
                        <label for="designation">Designation/Position</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <select name="year" id="year" class="browser-default" v-model="modals.experience.from_year" required>
                            <option disabled="disabled" value="">From Year</option>
                            @for ($i = 1980; $i <= date('Y'); $i++))
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="input-field col s12 m6">
                        <select name="year" id="year" class="browser-default" v-model="modals.experience.to_year">
                            <option disabled="disabled" value="">To Year</option>
                            @for ($i = date('Y'); $i >= 1980; $i--))
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="progress" v-if="modals.experience.is_processing">
                <div class="indeterminate"></div>
            </div>
            <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat" v-if="!modals.experience.is_processing">Close</a>
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="save_experience"
                    value="1" v-if="!modals.experience.is_processing">Save Experience</button>
        </div>
    </form>
</div>