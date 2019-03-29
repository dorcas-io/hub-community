<div class="row">
    <div class="col s12">
        <div class="card-panel grey-text" style="min-height: 400px;">
            <h1 class="center-align"><i class="material-icons large">{{ $icon }}</i></h1>
            <p class="center-align {{ $paragraphClass or 'flow-text' }}">
                {!! $slot !!}
            </p>
            <div class="center-align">
                {!! $buttons !!}
            </div>

        </div>
    </div>
</div>