@if (userTopNav())
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/') }}" role="button">
            <i class="icon md-view-dashboard mr-2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('production.penerimaan_barang.index') }}" role="button">
            <i class="icon md-thumb-up mr-2"></i> Penerimaan Barang
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('production.pengiriman_barang.index') }}" role="button">
            <i class="icon md-truck mr-2"></i> Pengiriman Barang
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('production.distribusi_pakan.index') }}" role="button">
            <i class="icon md-device-hub mr-2"></i> Distribusi Pakan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('inventory.persediaan.index') }}" role="button">
            <i class="icon md-inbox mr-2"></i> Persediaan Barang
        </a>
    </li>
    {{-- <li class="nav-item">
        <a class="nav-link" href="{{ route('production.dyeing.index') }}" role="button">
            <i class="icon md-assignment-o mr-2"></i> Dyeing
        </a>
    </li> --}}
    <li class="nav-item dropdown dropdown-fw dropdown-mega">
        <a class="nav-link waves-effect waves-light waves-round" data-toggle="dropdown" href="#"
            aria-expanded="false" data-animation="fade" role="button"><i class="icon md-more-vert mr-2"></i> Tampilkan
            Lebih Banyak
            <i class="icon md-chevron-down ml-2" aria-hidden="true"></i>
        </a>
        <div class="dropdown-menu" role="menu">
            <div class="mega-content">
                <div class="form-group row">
                    @foreach ($menus as $menuHeaderTopNav => $menuItemTopNav)
                        @if ($menuHeaderTopNav != 'PRODUCTION')
                            @continue
                        @endif
                        <div class="col-md-12">
                            <h5><i class="icon md-assignment-o mr-2"></i> {{ $menuHeaderTopNav }}</h5>
                            <ul class="blocks-4">
                                @php $check = false; @endphp
                                @foreach ($menuItemTopNav as $keyTopNav => $itemTopNav)
                                @if ($itemTopNav['display_name']  == 'Weaving' || $itemTopNav['display_name']  == 'Inspecting' || $itemTopNav['display_name']  == 'Finishing') @continue @endif
                                @if ($loop->first || $check)
                                <li class="mega-menu m-0">
                                    <ul class="list-icons">
                                        @php
                                $check = false;
                            @endphp
                                @endif
                                            <li>
                                                <i class="{{ $itemTopNav['icon'] }}" aria-hidden="true"></i>
                                                <a class="text-primary" href="{{ $itemTopNav['link'] }}"
                                                    class=" waves-effect waves-light waves-round">
                                                    {{ $itemTopNav['display_name'] }}
                                                </a>
                                            </li>
                                    @if ($loop->iteration % 2 == 0 || $loop->last)
                                </ul>
                            </li>
                            @php
                                $check = true;
                            @endphp
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    @foreach ($menusTop as $prefixAside => $topNavs)
                        <div class="col-md-4">
                            <h5><i class="icon md-assignment-o mr-2"></i> {{ strtoupper($prefixAside) }}</h5>
                            <ul class="blocks-1">
                                <li class="mega-menu m-0">
                                    <ul class="list-icons">
                                        @foreach ($topNavs as $topNav)
                                            <li>
                                                <i class="md-chevron-right" aria-hidden="true"></i>
                                                <a class="text-primary" href="{{ $topNav['link'] }}"
                                                    class=" waves-effect waves-light waves-round">
                                                    {{ $topNav['display_name_full'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </li>
@endif
