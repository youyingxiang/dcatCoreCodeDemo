<div class="main-menu">
    <div class="main-menu-content">
        <aside class="main-sidebar shadow">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mr-auto">
                        <a href="/" class="navbar-brand waves-effect waves-light">
                            <span class="logo-lg">测试</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar pb-3">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" style="padding-top: 10px">
                    {!! \App\Core\Support\Helper::adminSection(\App\Core\Support\AdminSection::LEFT_SIDEBAR_MENU) !!}
                </ul>
            </div>
        </aside>
    </div>
</div>
