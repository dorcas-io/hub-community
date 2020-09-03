<div id="help-modal" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h4>Welcome {{ !empty($dorcasUser) ? $dorcasUser->firstname: '' }}</h4>
        <p class="flow-text">
            Here's a video to guide you on what to do next.
        </p>
        <div class="row">
            <div class="col s12">
                @section('help_modal_content')
                    @section('help_modal_content')
                        @component('layouts.slots.video-embed')
                            //www.youtube.com/embed/WNgsHWOpoF4?rel=0
                        @endcomponent
                    @endsection
                @show
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Close</a>
    </div>
</div>