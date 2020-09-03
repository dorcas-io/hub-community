<div class="alert {{ $type or 'alert-info' }}">
    <i class="{{ $icon or 'icon-hand-up' }}"></i><strong>{{ $title or 'Heads Up!' }}</strong> {!! $slot !!}
</div>