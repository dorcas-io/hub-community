<!-- Floating Action Button -->
<div class="fixed-action-btn" style="bottom: 50px; right: 19px;">
    <a class="btn-floating btn-large blue darken-3 pulse">
        <i class="material-icons">assistant</i>
    </a>
    <ul>
        @if (!empty($dorcasSubdomain))
            <li>
                <a href="{{ $dorcasSubdomain . '/store' }}" target="_blank" class="btn-floating blue darken-3"
                   data-tooltip="Web Store">
                    <i class="material-icons">local_mall</i>
                </a>
            </li>
        @endif
        @if ((empty($viewMode) || $viewMode === 'business') && !empty($dorcasUser) && $dorcasUser->is_professional)
            <li>
                <a href="{{ route('apps.invoicing.orders.new') }}" class="btn-floating blue darken-3" data-tooltip="New Invoice">
                    <i class="material-icons">monetization_on</i>
                </a>
            </li>
            <li>
                <a href="{{ route('apps.inventory.new') }}" class="btn-floating blue darken-3" data-tooltip="New Product">
                    <i class="material-icons">style</i>
                </a>
            </li>
            <li>
                <a href="{{ route('apps.crm.customers.new') }}" class="btn-floating blue darken-3" data-tooltip="Add Customer">
                    <i class="material-icons">group_add</i>
                </a>
            </li>
        @elseif (!empty($viewMode) && $viewMode === 'professional'&& !empty($dorcasUser) && $dorcasUser->is_professional)
            <li>
                <a href="{{ route('directory.profile') }}" class="btn-floating blue darken-3" data-tooltip="Manage Profile">
                    <i class="material-icons">business_center</i>
                </a>
            </li>
        @endif
    </ul>
</div>
<!-- Floating Action Button -->