<div class="dropdown" id="dorcas-auth-options">
    <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
        <span class="avatar" style="background-image: url({{ !empty($company->logo) ? $company->logo : $dorcasUser->photo }})"></span>
        <span class="ml-2 d-none d-lg-block">
            <span class="text-default">{{ $dorcasUser->firstname . ' ' . $dorcasUser->lastname }}</span>
            <!-- <small class="text-muted d-block mt-1">{{ !empty($loggedInUserRole) ? $loggedInUserRole : 'Business' }}</small> -->
            <small class="text-muted d-block mt-1">{{ !empty($business->name) ? $business->name : 'Business' }}</small>
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">

        @if (!empty($business) && $business->plan['data']['name'] === 'starter')
        <!-- <a class="dropdown-item" href="{{ route('subscription') }}">
            <i class="dropdown-icon fa fa-arrow-up"></i> Upgrade Plan
        </a> -->
        @endif

        @if (!empty($vPanelUrl))
        <a class="dropdown-item" href="{{ $vPanelUrl }}" target="_blank">
            <i class="dropdown-icon fa fa-id-badge"></i> vPanel Dashboard
        </a>
        @endif


        <a class="dropdown-item" href="{{ route('settings-personal') }}">
            <i class="dropdown-icon fe fe-user"></i> Profile
        </a>
        <a class="dropdown-item" href="{{ route('welcome-overview') }}">
            <i class="dropdown-icon fe fe-power"></i> Overview &amp; Setup
        </a>
        <!--<a class="dropdown-item" href="#">
            <span class="float-right"><span class="badge badge-primary">6</span></span>
            <i class="dropdown-icon fe fe-mail"></i> Inbox
        </a>-->
        <!-- <div class="dropdown-divider"></div>
        <a v-if="viewMode !== 'business'" class="dropdown-item" href="{{ safe_href_route('dashboard') ? route('dashboard').'?views=business' : '#' }}">
            <i class="dropdown-icon fa fa-address-card"></i> Business Dashboard
        </a>
        <a v-if="viewMode !== 'professional' && loggedInUser.is_professional" class="dropdown-item" href="{{ safe_href_route('dashboard') ? route('dashboard').'?views=professional' : '#' }}">
            <i class="dropdown-icon fa fa-id-card"></i> Service Dashboard
        </a>
        <a v-if="viewMode !== 'vendor' && loggedInUser.is_vendor" class="dropdown-item" href="{{ safe_href_route('dashboard') ? route('dashboard').'?views=vendor' : '#' }}">
            <i class="dropdown-icon fa fa-id-card-o"></i> Vendor Dashboard
        </a> -->

        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ url('/logout') }}">
            <i class="dropdown-icon fe fe-log-out"></i> Sign out
        </a>
    </div>
</div>
