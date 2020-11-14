<!-- START FOOTER -->
<footer class="page-footer gradient-45deg-purple-light-blue">
    <div class="footer-copyright">
        <div class="container">
            <span>
                &copy; {{ date('Y') }}
                @if (!empty($partner))
                    <a class="grey-text text-lighten-4" href="{{ url('/') }}">{{ $partner->name }}</a> All rights reserved.
                @elseif (!empty($business) && $business->plan['data']['price_monthly']['raw'] > 0)
                    <a class="grey-text text-lighten-4" href="{{ $business->website ?: url('/') }}">{{ $business->name }}</a> All rights reserved.
                @else
                    <a class="grey-text text-lighten-4" href="{{ url('/') }}">{{ config('app.name') }}</a> All rights reserved.
                @endif
            </span>
        </div>
    </div>
</footer>
<!-- END FOOTER -->