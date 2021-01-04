                    @php
                        $acceptable_modules = array('modules-access-grants','modules-access-requests',
                        'modules-app-store','modules-assistant','modules-customers','modules-dashboard',
                        'modules-dashboard-vpanel','modules-ecommerce','modules-finance','modules-ops',
                        'modules-integrations','modules-library','modules-marketplace','modules-people',
                        'modules-sales','modules-service-profile','modules-service-requests','modules-settings',
                        'addons','modules-analytics');

                        $modules_permission_key = array(
                            'modules-access-grants' => '',
                            'modules-access-requests' => 'services',
                            'modules-app-store' => 'appstore',
                            'modules-assistant' => '',
                            'modules-customers' => 'customers',
                            'modules-dashboard' => 'dashboard',
                            'modules-dashboard-vpanel' => '',
                            'modules-ecommerce' => 'ecommerce',
                            'modules-finance' => 'finance',
                            'modules-ops' => 'operations',
                            'modules-integrations' => 'integrations',
                            'modules-library' => '',
                            'modules-marketplace' => '',
                            'modules-people' => 'people',
                            'modules-sales' => 'sales',
                            'modules-service-profile' => 'services',
                            'modules-service-requests' => 'services',
                            'modules-settings' => 'settings',
                            'modules-analytics' => 'analytics',
                            'addons' => 'addons'
                        );
                        //dd(config('navigation-menu'));
                    @endphp

                    @foreach(config('navigation-menu') as $key => $value)
                        @if ( in_array($key,$acceptable_modules) && $value['navbar'] && ($value['dashboard']==$viewMode || $value['dashboard']=='all') )
                            @php
                            if ( count($value['sub-menu'])>0 && ( empty($value['sub-menu-display']) || (isset($value['sub-menu-display']) && $value['sub-menu-display']==='show') ) ) { $dropdown=" dropdown"; $dropdownToggle = "dropdown"; } else { $dropdown=""; $dropdownToggle = ""; }
                            @endphp
                            <li class="nav-item{{ $dropdown }}" id="nav_item_{{ $key }}" v-if="enabledUis.indexOf('{{ $modules_permission_key[$key] }}') !== -1">
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