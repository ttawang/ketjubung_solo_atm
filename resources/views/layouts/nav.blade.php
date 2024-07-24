<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation">

    <div class="navbar-header">
        <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
            data-toggle="menubar">
            <span class="sr-only">Toggle navigation</span>
            <span class="hamburger-bar"></span>
        </button>
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
            data-toggle="collapse">
            <i class="icon md-more" aria-hidden="true"></i>
        </button>
        <div class="navbar-brand navbar-brand-center site-gridmenu-toggle ml-2" data-toggle="gridmenu">
            <img class="navbar-brand-logo" src="{{ asset('img/logo.png') }}" title="SINARBAJA">
            <span class="navbar-brand-text hidden-xs-down"> KETJUBUNG ATM</span>
        </div>
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-search"
            data-toggle="collapse">
            <span class="sr-only">Toggle Search</span>
            <i class="icon md-search" aria-hidden="true"></i>
        </button>
    </div>

    <div class="navbar-container container-fluid">
        <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
            <ul class="nav navbar-toolbar">
                <li class="nav-item hidden-float" id="toggleMenubar">
                    <a class="nav-link" data-toggle="menubar" href="#" role="button">
                        <i class="icon hamburger hamburger-arrow-left">
                            <span class="sr-only">Toggle menubar</span>
                            <span class="hamburger-bar"></span>
                        </i>
                    </a>
                </li>
                @include('layouts.top-nav')
            </ul>

            <!-- Navbar Toolbar Right -->
            <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
                <li class="nav-item dropdown">
                    <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false"
                        data-animation="scale-up" role="button">
                        <div class="row">
                            <div class="col-md-3">
                                <span class="avatar avatar-online">
                                    <img src="{{ asset('img/1.jpg') }}" alt="...">
                                    <i></i>
                                </span>
                            </div>
                            <label class="col-md-9 mt-2"
                                style="cursor: pointer; margin-bottom: 0px;">{{ $username }}</label>
                        </div>
                    </a>
                    <div class="dropdown-menu" role="menu">
                        {{-- <div class="dropdown-divider" role="presentation"></div> --}}
                        <a class="dropdown-item" href="javascript:void(0);" onclick="addFormChangePassword($(this));" role="menuitem"><i class="icon md-key" aria-hidden="true"></i> Ganti Password</a>
                        <form action="{{ route('logout') }}" method="POST" id="formLogout">@csrf</form>
                        <a class="dropdown-item" href="javascript:void(0);"
                            onclick="toastr.info('Sedang Logout, Harap Tunggu...', 'Loading', {progressBar: true}); $('#formLogout').submit();"
                            role="menuitem"><i class="icon md-power" aria-hidden="true"></i> Logout</a>
                    </div>
                </li>
            </ul>
            <!-- End Navbar Toolbar Right -->
        </div>

        <div class="collapse navbar-search-overlap" id="site-navbar-search">
            <form role="search">
                <div class="form-group">
                    <div class="input-search">
                        <i class="input-search-icon md-search" aria-hidden="true"></i>
                        <input type="text" class="form-control" name="site-search" placeholder="Search...">
                        <button type="button" class="input-search-close icon md-close"
                            data-target="#site-navbar-search" data-toggle="collapse" aria-label="Close"></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</nav>
<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    <li class="site-menu-category">Main Navigations</li>
                    <li class="site-menu-item" name="dashboard" display-name="Dashboard" display-name-full="Dashboard">
                        <a class="animsition-link" href="{{ url('/') }}">
                            <i class="site-menu-icon md-view-dashboard" aria-hidden="true"></i>
                            <span class="site-menu-title">Dashboard</span>
                        </a>
                    </li>
                    @if (isset($menus))
                        @foreach ($menus as $menuHeader => $menuItem)
                            <li class="site-menu-category">{{ $menuHeader }}</li>
                            @foreach ($menuItem as $item)
                                <li class="site-menu-item" name="{{ $item['name'] }}"
                                    prefixAside="{{ $item['prefix_aside'] }}"
                                    display-name="{{ $item['display_name'] }}"
                                    display-name-full="{{ $item['display_name_full'] }}">
                                    <a href="{{ $item['link'] }}" class="animsition-link">
                                        <i class="site-menu-icon {{ $item['icon'] ?? 'md-folder' }}"></i>
                                        <span class="site-menu-title">{!! $item['display_name'] !!}</span>
                                        @if (count($item['childs']) > 0)
                                            <span class="site-menu-arrow"></span>
                                        @endif
                                    </a>
                                    @foreach ($item['childs'] as $itemChild)
                                        <ul class="site-menu-sub">
                                            <li class="site-menu-item hidden-sm-down" name="{{ $itemChild['name'] }}"
                                                display-name="{{ $item['display_name'] }}"
                                                display-name-full="{{ $item['display_name_full'] }}">
                                                <a href="{{ $itemChild['link'] }}" class="animsition-link">
                                                    <span class="site-menu-title">{{ $itemChild['text'] }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    @endforeach
                                </li>
                            @endforeach
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="site-menubar-footer">
        <a href="javascript: void(0);" data-placement="top" data-toggle="tooltip" data-original-title="Settings">
            <span class="icon md-settings" aria-hidden="true"></span>
        </a>
        <a href="javascript: void(0);" data-placement="top" data-toggle="tooltip" data-original-title="Lock">
            <span class="icon md-eye-off" aria-hidden="true"></span>
        </a>
        <a href="javascript: void(0);" class="fold-show"
            onclick="toastr.info('Sedang Logout, Harap Tunggu...', 'Loading', {progressBar: true}); $('#formLogout').submit();"
            data-placement="top" data-toggle="tooltip" data-original-title="Logout">
            <span class="icon md-power" aria-hidden="true"></span>
        </a>
    </div>
</div>
