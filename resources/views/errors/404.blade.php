@extends('layouts.tabler-errors')
@section('body')
<div class="page-content">
    <div class="container text-center">
        <div class="display-1 text-muted mb-5"><i class="si si-exclamation"></i> 404</div>
        <h1 class="h2 mb-3 text-uppercase">Sorry but we couldn’t find what you're looking for :(</h1>
        <p class="h4 text-muted font-weight-normal mb-7">It seems that this content doesn’t exist.</p>
        <a class="btn btn-primary" href="{{ route('dashboard') }}">
            <i class="fe fe-home mr-2"></i>Try Starting Again
        </a>
    </div>
</div>
@endsection