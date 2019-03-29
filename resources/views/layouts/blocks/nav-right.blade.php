<!-- START RIGHT SIDEBAR NAV-->
<aside id="right-sidebar-nav">
    <ul id="chat-out" class="side-nav rightside-navigation">
        <li class="li-hover">
            <div class="row">
                <div class="col s12 border-bottom-1 mt-5">
                    <ul class="tabs">
                        <li class="tab col s12" class="active">
                            <a href="#settings">
                                <span class="material-icons">settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div id="settings" class="col s12">
                    <h6 class="mt-5 mb-3 ml-3">QUICK SETTINGS</h6>
                    <ul class="collection border-none">
                        <li class="collection-item border-none">
                            <div class="m-0">
                                <span class="font-weight-600">Professionals Services</span>
                                <settings-toggle name="set_professional_status" :checked="user.is_professional"></settings-toggle>
                            </div>
                            <p>Enable this for your account.</p>
                        </li>
                        <li class="collection-item border-none">
                            <div class="m-0">
                                <span class="font-weight-600">Vendor Services</span>
                                <settings-toggle name="set_vendor_status" :checked="user.is_vendor"></settings-toggle>
                            </div>
                            <p>Enable this for your account.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
    </ul>
</aside>
<!-- END RIGHT SIDEBAR NAV-->