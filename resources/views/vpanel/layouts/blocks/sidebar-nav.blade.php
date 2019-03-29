<div class="sidebar-sets" id="sidebar-nav">
    <div class="set">
        <div class="title">Clients</div>
        <ul>
            <li>
                <a href="{{ route('vpanel.businesses') }}" v-bind:class="{'active': selectedMenu !== null && selectedMenu == 'businesses'}">Businesses</a>
            </li>
        </ul>
    </div>
    <div class="set">
        <div class="title">{{ !empty($partner) ? $partner->name : '' }}</div>
        <ul>
            <li>
                <a href="{{ route('vpanel.users') }}" v-bind:class="{'active': selectedMenu !== null && selectedMenu == 'members'}">Members</a>
            </li>
            <li>
                <a href="{{ route('vpanel.users.managers') }}" v-bind:class="{'active': selectedMenu !== null && selectedMenu == 'managers'}">Managers</a>
            </li>
        </ul>
    </div>
    <div class="set">
        <div class="title">Invites</div>
        <ul>
            <li>
                <a href="{{ route('vpanel.invites') }}" v-bind:class="{'active': selectedMenu !== null && selectedMenu == 'invites'}">Invites</a>
            </li>
        </ul>
    </div>
    <div class="set">
        <div class="title">Settings</div>
        <ul>
            <li>
                <a href="{{ route('vpanel.customise') }}" v-if="typeof partner.id !== 'undefined'"
                   v-bind:class="{'active': selectedMenu !== null && selectedMenu == 'customisation'}">Customisation</a>
            </li>
            <li>
                <a href="{{ route('vpanel.settings') }}"
                   v-bind:class="{'active': selectedMenu !== null && selectedMenu == 'settings'}">Account Settings</a>
            </li>
        </ul>
    </div>
    <div class="set abs-bottom">
        <ul>
            <li><a href="{{ url('/logout') }}">Sign Out</a></li>
        </ul>
    </div>
</div>
