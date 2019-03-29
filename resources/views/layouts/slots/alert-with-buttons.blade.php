<div id="card-alert" class="card cyan">
    <div class="card-content white-text">
        <span class="card-title white-text darken-1">{{ $title }}</span>
        <p>{!! $slot !!}</p>
    </div>
    <div class="card-action cyan darken-2">
        {!! $buttons !!}
    </div>
    <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>