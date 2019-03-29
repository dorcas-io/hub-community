<header>
    <div class="search-opt" id="search-bar">
        <form method="get" action="" v-on:submit.prevent="search">
            <div class="wrap">
                <span class="icon">
                    <i class="ion ion-ios-search"></i>
                </span>
                <input type="text" v-model="query" placeholder="Search businesses, users, etc...">
            </div>
        </form>
    </div>

    <div class="right-menu">
        <div class="alerts">
            <div class="wrap">
                <a class="display">
                    <span class="icon"><i class="ion ion-ios-notifications"></i></span>
                    <span class="knob"></span>
                </a>
            </div>
        </div>

        <div class="divider"></div>

        <div class="user">
            <div class="option">
                <span class="name">
                    @if (\Illuminate\Support\Facades\Auth::check())
                        {{ $dorcasUser->firstname . ' ' . $dorcasUser->lastname }}
                    @endif
                </span>
            </div>
            <span class="avi">
                <img src="{{ $dorcasUser->photo }}" alt="avatar" width="38" class="img-circle" height="38" />
            </span>
        </div>
    </div>
</header>
