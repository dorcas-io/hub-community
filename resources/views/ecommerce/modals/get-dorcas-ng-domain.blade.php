<div id="get-dorcas-ng-domain" class="modal">
    <form class="col s12" action="" method="post">
        {{ csrf_field() }}
        <div class="modal-content">
            <h4>Reserve SubDomain</h4>
            <div class="row">
                <div class="col s12 m6">
                    <div class="input-field col s12">
                        <input id="domain" type="text" name="domain" maxlength="80" v-model="domain" required
                               v-on:keyup="removeStatus()">
                        <label for="domain">Desired Domain</label>
                    </div>
                    <div class="progress" v-if="is_querying">
                        <div class="indeterminate"></div>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="card darken-1" v-bind:class="{green: is_available, red: !is_available && is_queried}">
                        <div class="card-content" v-bind:class="{'white-text': (is_available || !is_available) && is_queried}">
                            <p class="flow-text">
                                https://@{{ actual_domain }}.{{ get_dorcas_domain() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" v-on:click.prevent="checkAvailability()" class="btn-flat waves-green" v-if="!is_querying">Check Availability</a>
            <button type="submit" class="modal-action waves-effect waves-green btn-flat" name="reserve_subdomain"
                    value="1" v-if="is_available">Reserve</button>
        </div>
    </form>
</div>