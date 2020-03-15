START HEADER -->
<header id="header" class="page-topbar" v-if="typeof pageMode !== 'undefined' && pageMode === 'default'">
    <!-- start header nav-->
    <div class="navbar-fixed">
        <nav class="navbar-color gradient-45deg-purple-light-blue gradient-shadow">
            <div class="nav-wrapper">
                @include('layouts.blocks.search-bar')
                <ul class="right hide-on-med-and-down">
                    <li>
                        <a href="#" class="waves-effect waves-block waves-light" onclick="showTour(); return false;">
                            <i class="material-icons">directions</i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="waves-effect waves-block waves-light toggle-fullscreen">
                            <i class="material-icons">settings_overscan</i>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="waves-effect waves-block waves-light profile-button"
                           data-activates="profile-dropdown">
                            <span class="avatar-status avatar-online">
                                <img src="{{ \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->photo : cdn('images/avatar/avatar-9.png') }}" alt="avatar">
                                <i></i>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="waves-effect waves-block waves-light chat-collapse"
                           data-activates="chat-out">
                            <i class="material-icons">format_indent_increase</i>
                        </a>
                    </li>
                </ul>
                <!-- notifications-dropdown -->
                <!-- profile-dropdown -->
                <!-- <ul id="profile-dropdown" class="dropdown-content">
                    <li>
                        <a href="{{ route('settings.personal') }}" class="grey-text text-darken-1">
                            <i class="material-icons">face</i> Profile
                        </a>
                    </li>
                    @if (!empty($business) && $business->plan['data']['name'] === 'starter')
                        <li>
                            <a href="{{ route('subscription') }}" class="grey-text text-darken-1">
                                <i class="material-icons">redeem</i> Upgrade Plan
                            </a>
                        </li>
                    @endif
                    <li v-if="viewMode === 'business' && showUiModalAccessMenu">
                        <a href="{{ route('home') }}?show_ui_wizard" class="grey-text text-darken-1">
                            <i class="material-icons">assistant</i> Setup Wizard
                        </a>
                    </li>
                    <li v-if="viewMode === 'business'">
                        <a href="#help-modal" class="grey-text text-darken-1 modal-trigger">
                            <i class="material-icons">help_outline</i> Help
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('settings') }}" class="grey-text text-darken-1">
                            <i class="material-icons">settings</i> Settings
                        </a>
                    </li>
                    @if (!empty($vPanelUrl))
                        <li>
                            <a href="{{ $vPanelUrl }}" class="grey-text text-darken-1">
                                <i class="material-icons">layers</i> vPanel Console
                            </a>
                        </li>
                    @endif
                    <li class="divider"></li>
                    <li v-if="viewMode !== 'business'">
                        <a href="{{ route('home') }}?view=business" class="grey-text text-darken-1">
                            <i class="material-icons">domain</i> Business Mode
                        </a>
                    </li>
                    <li v-if="viewMode !== 'professional' && user.is_professional">
                        <a href="{{ route('home') }}?view=professional" class="grey-text text-darken-1">
                            <i class="material-icons">contact_phone</i> Service Mode
                        </a>
                    </li>
                    <li v-if="viewMode !== 'vendor' && user.is_vendor">
                        <a href="{{ route('home') }}?view=vendor" class="grey-text text-darken-1">
                            <i class="material-icons">assignment</i> Vendor Mode
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/logout') }}" class="grey-text text-darken-1">
                            <i class="material-icons">keyboard_tab</i> Logout
                        </a>
                    </li>
                </ul> -->
            </div>
        </nav>
    </div>
</header>
<!-- END HEADER