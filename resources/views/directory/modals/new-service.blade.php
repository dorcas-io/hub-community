<div id="new-service" class="modal">
    <form class="col s12" action="" method="post" v-on:submit.prevent="addService">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Add a Service</h4>
            <div class="row">
                <div class="col s12 m12 mb-4">
                    <div class="input-field col s12 m6">
                        <input id="service_title" type="text" name="service_title" v-model="modals.service.title" required>
                        <label for="service_title">Title</label>
                    </div>
                    <div class="input-field col s12 m6">
                        <input id="service_categories" type="text" name="service_categories" v-model="modals.service.extra_category">
                        <label for="service_title">Additional Categories (separate by comma)</label>
                    </div>
                    <div class="input-field col s12 m3">
                        <select name="service_type" id="service_type" class="browser-default" v-model="modals.service.type" required>
                            <option disabled="disabled" value="">Service Type</option>
                            <option value="free">Free</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                    <div class="input-field col s12 m3">
                        <select name="service_type" id="service_type" class="browser-default" v-model="modals.service.frequency" required>
                            <option disabled="disabled" value="">Payment Frequency</option>
                            <option value="hour">per Hour</option>
                            <option value="day">per Day</option>
                            <option value="week">per Week</option>
                            <option value="month">per Month</option>
                            <option value="standard">Standard (per Job)</option>
                        </select>
                    </div>
                    <div class="input-field col s12 m3">
                        <select name="service_currency" id="service_currency" class="browser-default" v-model="modals.service.currency" required>
                            <option disabled="disabled" value="">Currency</option>
                            <option value="EUR">Euro</option>
                            <option value="GBP">Pound Sterling</option>
                            <option value="NGN">Nigerian Naira</option>
                            <option value="USD">US Dollar</option>
                        </select>
                    </div>
                    <div class="input-field col s12 m3">
                        <input id="service_cost" type="number" step="0.01" min="0" name="service_cost" v-model="modals.service.amount" required>
                        <label for="service_cost">Service Cost</label>
                    </div>
                    <div class="input-field col s12 m6" v-if="categories.length > 0">
                        <select name="categories[]" id="categories" class="browser-default"
                                v-model="modals.service.categories" multiple size="7" style="height: 6rem !important;">
                            <option disabled="disabled" value="">Select One or more categories</option>
                            @if (!empty($categories))
                                @foreach ($categories as $category)
                                    @if (!empty($category->parent))
                                        @continue
                                    @endif
                                    <option value="{{ $category->id }}">{{ title_case($category->name) }}</option>
                                    @if (!empty($category->children))
                                            <optgroup label="{{ $category->name }}">
                                                @foreach ($category->children['data'] as $subCat)
                                                    <option value="{{ $subCat->id }}">{{ title_case($subCat->name) }}</option>
                                                @endforeach
                                            </optgroup>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="progress" v-if="modals.service.is_processing">
                <div class="indeterminate"></div>
            </div>
            <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat"
               v-if="!modals.service.is_processing" v-on:click="cancelServiceEdit">Cancel</a>
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="save_experience"
                    value="1" v-if="!modals.service.is_processing">Save Service</button>
        </div>
    </form>
</div>