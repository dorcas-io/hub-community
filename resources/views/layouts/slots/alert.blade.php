<div id="card-alert" class="card {{ $type or 'gradient-45deg-light-blue-cyan' }}">
    <div class="card-content white-text">
        <p>
            {!! $icon or '<i class="material-icons">info_outline</i>' !!} {!! $slot !!}
        </p>
    </div>
    <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<!-- gradient-45deg-green-teal : success -->
<!-- gradient-45deg-light-blue-cyan : info -->
<!-- gradient-45deg-red-pink : danger -->
<!-- gradient-45deg-amber-amber : warning -->