<div class="row">
    <div class="col s12">
        @if (!empty($pageUpgradeMessage) && !$isOnPaidPlan)
            @component('layouts.slots.alert')
                {!! str_replace(['@[[PartnerAppName]]'], [!empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : config('app.name')], $pageUpgradeMessage) !!}
            @endcomponent
        @endif
        @if (!empty($pageStandardMessage))
            @component('layouts.slots.alert')
                {!! str_replace(['@[[PartnerAppName]]'], [!empty($appUiSettings['product_name']) ? $appUiSettings['product_name'] : config('app.name')], $pageStandardMessage) !!}
            @endcomponent
        @endif
    </div>
</div>