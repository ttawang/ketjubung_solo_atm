<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
@include('layouts.header')

<body style="zoom: 90%;"
    class="animsition {{ isset($isLogin) ? 'page-login-v2 layout-full page-dark' : 'site-navbar-small dashboard' }} {{ $isAside ? 'page-aside-fixed page-aside-right' : '' }} {{ $isMenubarFold ? 'site-menubar-fold site-menubar-keep' : '' }}">

    @if (!isset($isLogin))
        @include('layouts.nav')
    @endif

    <div class="page" data-animsition-in="fade-in" data-animsition-out="fade-out">

        @if ($isAside ?? false)
            @include('layouts.aside')
            <div class="page-main">
        @endif

        @if (!empty($breadcumbs))
            <div class="page-header">
                <ol class="breadcrumb">
                    @foreach ($breadcumbs as $breadcumb)
                        <li class="breadcrumb-item {{ $breadcumb['active'] }}">
                            <a href="{{ $breadcumb['link'] }}">{{ $breadcumb['nama'] }}</a>
                        </li>
                    @endforeach
                </ol>
                <h1 class="page-title title-breadcumb"></h1>
                <div class="page-header-actions">
                    @if ($name == 'distribusi_pakan')
                        <a href="{{ route('production.pakan.index') }}" target="_blank"
                            class="btn btn-round btn-warning waves-effect waves-classic">
                            <i class="icon md-truck mr-1" aria-hidden="true"></i>
                            <b>PAKAN (SHUTTLE/RAPPIER)</b>
                        </a>
                    @endif
                </div>
            </div>
        @endif
        <div class="page-content container-fluid">
            @yield('content')
        </div>
        @if ($isAside ?? false)
    </div>
    @endif
    </div>

    @include('layouts.footer')

</body>

</html>
