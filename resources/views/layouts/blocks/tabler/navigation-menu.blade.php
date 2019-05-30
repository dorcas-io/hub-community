                    <!-- <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link" v-bind:class="{'active': selectedMenu === 'home'}">
                            <i class="fe fe-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('access-grants') }}" class="nav-link" v-bind:class="{'active': selectedMenu === 'access-grants'}"><i class="fe fe-unlock"></i> Access Grants</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="javascript:void(0)" class="nav-link" v-bind:class="{'active': selectedMenu === 'settings'}" data-toggle="dropdown">
                            <i class="fe fe-settings"></i> Settings
                        </a>
                        <div class="dropdown-menu dropdown-menu-arrow">
                            <a href="{{ route('settings.billing') }}" class="dropdown-item ">Subscription &amp; Billing</a>
                            <a href="{{ route('settings.business') }}" class="dropdown-item ">Business Profile Settings</a>
                            <a href="{{ route('settings.personal') }}" class="dropdown-item ">Account &amp; Profile</a>
                            <a href="{{ route('settings.security') }}" class="dropdown-item ">Security Settings</a>
                        </div>
                    </li> -->
                    @foreach(config('navigation-menu') as $key => $value)
                        @if ($value['navbar'])
                            @php
                            if (count($value['sub-menu'])>0) { $dropdown=" dropdown"; $dropdownToggle = "dropdown"; } else { $dropdown=""; $dropdownToggle = ""; }
                            @endphp
                            <li class="nav-item{{ $dropdown }}">
                                <a href="{{ $value['clickable'] && safe_href_route($value['route']) && (isset($selectedMenu) && $key !== $selectedMenu) ? route($value['route']) : 'javascript:void(0)' }}" class="nav-link" v-bind:class="{'active': selectedMenu === '{{ $key }}'}" data-toggle="{{ $dropdownToggle }}"><i class="{{ isset($value['icon']) ? $value['icon'] : 'fe fe-settings' }}"></i> {{ $value['title'] }}</a>
                                @if (!empty($dropdown))
                                <div class="dropdown-menu dropdown-menu-arrow">
                                    @foreach($value['sub-menu'] as $sk => $sv)
                                    <a href="{{ safe_href_route($sv['route']) ? route($sv['route']) : 'javascript:void(0)' }}" class="dropdown-item "><i class="{{ isset($sv['icon']) ? $sv['icon'] : 'fe fe-settings' }}"></i> {{ $sv['title'] }}</a>
                                    @endforeach
                                </div>
                                @endif
                            </li>
                        @endif
                    @endforeach