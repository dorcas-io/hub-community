<div id="setup-modal" class="modal {{ !empty($isFirstConfiguration) ? 'non-dismissible' : '' }} modal-fixed-footer">
    <form class="col s12" action="{{ route('home') }}" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Welcome {{ !empty($dorcasUser) ? $dorcasUser->firstname: '' }}</h4>
            <p class="flow-text">
                Let's get {{ !empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : config('app.name') }} setup to serve you.
            </p>
            <div class="row">
                <div class="col s12">
                    <ul class="tabs tabs-fixed-width z-depth-1">
                        <li class="tab col s6"><a class="active" href="#setup-modal-business-info">Business Information</a></li>
                        <li class="tab col s6"><a href="#setup-modal-select-apps">Select Apps</a></li>
                    </ul>
                    <div id="setup-modal-business-info" class="col s12 pt-2">
                        <div class="row">
                            <div class="input-field col s12 m4">
                                <input placeholder="Business Name" id="business_name" name="business_name" type="text"
                                       class="validate" required v-model="business.name">
                                <label for="business_name">Business Name</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <select name="business_type" id="business_type" v-model="businessConfiguration.business_type" required>
                                    <option value="" disabled>Choose your Business Type</option>
                                    <option value="sole proprietorship">Sole Proprietorship</option>
                                    <option value="limited liability">Limited Liability</option>
                                </select>
                                <label for="business_name">Business Type</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <select name="business_sector" id="business_sector" v-model="businessConfiguration.business_sector" required>
                                    <option value="" disabled>Choose your Business Area</option>
                                    <option value="Aerospace">Aerospace</option>
                                    <option value="Agriculture">Agriculture</option>
                                    <option value="Banking & Financial Services">Banking & Financial Services</option>
                                    <option value="Chemical & Pharmaceutical">Chemical & Pharmaceutical</option>
                                    <option value="Computer & IT">Computer & IT</option>
                                    <option value="Construction">Construction</option>
                                    <option value="Consulting">Consulting</option>
                                    <option value="Defense">Defense</option>
                                    <option value="Education">Education</option>
                                    <option value="Energy">Energy</option>
                                    <option value="Electrical & Electronics">Electrical & Electronics</option>
                                    <option value="Entertainment">Entertainment</option>
                                    <option value="Food">Food</option>
                                    <option value="Insurance">Insurance</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Hospitality">Hospitality</option>
                                    <option value="Information, News & Media">Information, News & Media</option>
                                    <option value="Mining">Mining</option>
                                    <option value="Music & Film">Music & Film</option>
                                    <option value="Manufacturing">Manufacturing</option>
                                    <option value="Steel">Steel</option>
                                    <option value="Telecommunications">Telecommunications</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Water">Water</option>
                                    <option value="others">Others</option>
                                </select>
                                <label for="business_sector">Business Sector</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s4">
                                <select name="business_size" id="business_size" v-model="businessConfiguration.business_size" required>
                                    <option value="" disabled>Choose your Business Type</option>
                                    <option value="1">1 Person (Just You)</option>
                                    <option value="2 - 9">2 - 9 People</option>
                                    <option value="10 - 49">10 - 49 People</option>
                                    <option value="50 - 99">50 - 99 People</option>
                                    <option value="100+">100+ People</option>
                                </select>
                                <label for="business_size">Business Size</label>
                            </div>
                            <div class="input-field col s4">
                                <select name="business_country" id="business_country" v-model="businessConfiguration.country_id" required>
                                    <option value="" disabled>Choose your Country</option>
                                    @if (!empty($countries))
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="business_country">Country</label>
                            </div>
                            <div class="input-field col s4">
                                <select name="business_state" id="business_state" v-model="businessConfiguration.state_id">
                                    <option value="" disabled>Choose your State (Nigeria Only)</option>
                                    <option value="non-nigerian">Non-Nigerian</option>
                                    @if (!empty($states))
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <label for="business_state">State/Region</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s4">
                                <select name="currency" id="currency" v-model="businessConfiguration.currency" required>
                                    <option value="" disabled>Choose your Currency</option>
                                    @foreach ($isoCurrencies as $currency)
                                        <option value="{{ $currency['alphabeticCode'] }}">{{ $currency['currency'] }} - {{ $currency['alphabeticCode'] }}</option>
                                    @endforeach
                                </select>
                                <label for="currency">Currency</label>
                            </div>
                        </div>
                    </div>
                    <div id="setup-modal-select-apps" class="col s12">
                        <div class="row mt-4">
                            @foreach ($setupUiFields as $field)
                                <div class="col s12 m6">
                                    <div class="card">
                                        <div class="card-content">
                                            <span class="card-title">{{ $field['name'] }}</span>
                                            <div class="switch">
                                                <label>
                                                    Disabled
                                                    <input type="checkbox" name="selected_apps[]"
                                                           value="{{ $field['id'] }}" {{ !empty($field['enabled']) ? 'checked' : '' }}
                                                        {{ !empty($field['is_readonly']) ? 'disabled' : '' }}>
                                                    <span class="lever"></span>
                                                    Enabled
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="action"
                    value="save_preferences">Save Preferences</button>
        </div>
    </form>
</div>