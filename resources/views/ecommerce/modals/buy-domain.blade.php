<div id="buy-domain-modal" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Buy a Domain</h4>
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <input id="domain" type="text" name="domain" maxlength="50" v-model="domain" required
                               v-on:keyup="removeStatus()">
                        <label for="domain">Desired Domain</label>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <select class="browser-default" id="extension" name="extension" v-model="extension">
                            <option value="com">.com</option>
                            <option value="com.ng">.com.ng</option>
                        </select>
                    </div>
                </div>
                <div class="col s12">
                    <div class="card darken-1" v-bind:class="{green: is_available, red: !is_available && is_queried}">
                        <div class="card-content" v-bind:class="{'white-text': (is_available || !is_available) && is_queried}">
                            <p class="flow-text">
                                @{{ actual_domain }}
                            </p>
                        </div>
                    </div>
                    <div class="progress" v-if="is_querying">
                        <div class="indeterminate"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" v-on:click.prevent="checkAvailability()" class="btn-flat waves-green" v-if="!is_querying">Check Availability</a>
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="purchase_domain"
                    value="1" v-if="is_available">Purchase</button>
        </div>
    </form>
</div>