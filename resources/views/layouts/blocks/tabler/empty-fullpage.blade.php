
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">{{ $title }}</h3>
        <div class="card-options">
          {!! $buttons !!}
        </div>
      </div>
      <div class="card-body">
        {!! $slot !!}
      </div>
    </div>
