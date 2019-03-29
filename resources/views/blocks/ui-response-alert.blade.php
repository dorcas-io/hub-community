@if (!empty($uiResponse) && $uiResponse instanceof \App\Dorcas\Hub\Utilities\UiResponse\UiResponseInterface)
    <div class="container">
        <div class="row section">
            <div class="col s12">
                {!! $uiResponse->toHtml() !!}
            </div>
        </div>
    </div>
@elseif (count($errors) > 0)
    <div class="container">
        <div class="row section">
            <div class="col s12">
                <div id="card-alert" class="card gradient-45deg-red-pink">
                    <div class="card-content white-text">
                        <p>
                            <i class="material-icons">error</i>
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </p>
                    </div>
                    <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif