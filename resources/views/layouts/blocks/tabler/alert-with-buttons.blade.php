<div class="alert alert-success alert-dismissible">
  <button data-dismiss="alert" class="close"></button>
  <h4>{{ $title }}</h4>
  <p>
    {!! $slot !!}
  </p>
  <div class="btn-list">
    {!! $buttons !!}
  </div>
</div>