<div class="dropdown nav-item d-none d-flex" id="dashboard-switch" v-if="loggedInUser.is_professional || loggedInUser.is_vendor">
	<button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
		<span class="d-none d-md-inline">Switch Dashboard</span>
        <span class="d-inline d-md-none"> &nbsp; <i class="dropdown-icon fa fa-address-card"></i></span>
	</button>
	<div class="dropdown-menu">
		<a v-if="viewMode !== 'business'" class="dropdown-item" href="{{ safe_href_route('dashboard') ? route('dashboard').'?views=business' : '#' }}">
            <i class="dropdown-icon fa fa-address-card"></i> Business Dashboard
        </a>
        <a v-if="viewMode !== 'professional' && loggedInUser.is_professional" class="dropdown-item" href="{{ safe_href_route('dashboard') ? route('dashboard').'?views=professional' : '#' }}">
            <i class="dropdown-icon fa fa-id-card"></i> Service Dashboard
        </a>
        <a v-if="viewMode !== 'vendor' && loggedInUser.is_vendor" class="dropdown-item" href="{{ safe_href_route('dashboard') ? route('dashboard').'?views=vendor' : '#' }}">
            <i class="dropdown-icon fa fa-id-card-o"></i> Vendor Dashboard
        </a>
	</div>
</div>




