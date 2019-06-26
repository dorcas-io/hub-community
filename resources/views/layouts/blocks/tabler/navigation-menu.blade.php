                    @php
                        $acceptable_modules = array('modules-access-grants','modules-access-requests','modules-app-store','modules-assistant','modules-customers','modules-dashboard','modules-dashboard-vpanel','modules-ecommerce','modules-finance','modules-integrations','modules-library','modules-marketplace','modules-people','modules-sales','modules-service-profile','modules-service-requests','modules-settings','addons');
                    @endphp
                    @foreach(config('navigation-menu') as $key => $value)
                        @if ( in_array($key,$acceptable_modules) && $value['navbar'] && ($value['dashboard']==$viewMode || $value['dashboard']=='all') )
                            @php
                            if (count($value['sub-menu'])>0) { $dropdown=" dropdown"; $dropdownToggle = "dropdown"; } else { $dropdown=""; $dropdownToggle = ""; }
                            @endphp
                            <li class="nav-item{{ $dropdown }}">
                                <a href="{{ $value['clickable'] && safe_href_route($value['route']) && (isset($selectedMenu) && $key !== $selectedMenu) ? route($value['route']) : 'javascript:void(0)' }}" class="nav-link" v-bind:class="{'active': selectedMenu === '{{ $key }}'}" data-toggle="{{ $dropdownToggle }}"><i class="{{ isset($value['icon']) ? $value['icon'] : 'fe fe-settings' }}"></i> {{ $value['title'] }}</a>
                                @if (!empty($dropdown))
                                <div class="dropdown-menu dropdown-menu-arrow">
                                    @foreach($value['sub-menu'] as $sk => $sv)
                                        @if ( empty($sv['visibility']) || ( isset($sv['visibility']) && $sv['visibility']==='show' ) )
                                            <a href="{{ safe_href_route($sv['route']) ? route($sv['route']) : 'javascript:void(0)' }}" class="dropdown-item "><i class="{{ isset($sv['icon']) ? $sv['icon'] : 'fe fe-settings' }}"></i> {{ $sv['title'] }}</a>
                                        @endif
                                    @endforeach
                                </div>
                                @endif
                            </li>
                        @endif
                    @endforeach