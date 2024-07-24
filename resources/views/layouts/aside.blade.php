<div class="page-aside">
    <div class="page-aside-switch">
        <i class="icon md-chevron-left" aria-hidden="true"></i>
        <i class="icon md-chevron-right" aria-hidden="true"></i>
    </div>
    <div class="page-aside-inner page-aside-scroll">
        <div data-role="container">
            <div data-role="content">
                @if ($tree == 'weaving')
                    @php $countAside = count($menusAside[$tree]); @endphp
                    @foreach ($menusAside[$tree] as $i => $aside)
                        @if ($rolesName == 'preparatory' || $rolesName == 'weaving')
                            @if ($loop->first)
                                <section class="page-aside-section">
                                    <h5 class="page-aside-title">
                                        {{ ucwords($rolesName) }} Navigations
                                    </h5>
                            @endif
                            @if ($loop->first)
                                <div class="list-group">
                            @endif

                            <a class="list-group-item {{ $aside['name'] == $name ? 'active' : '' }}"
                                href="{{ $aside['link'] }}"><i class="icon {{ $aside['icon'] }}"
                                aria-hidden="true"></i>{{ $aside['display_name_full'] }}</a>

                        @if ($loop->last)
                        </div>
                        </section>
                        @endif
                        @else
                            @if ($loop->first)
                                <section class="page-aside-section">
                                    <h5 class="page-aside-title">
                                        Preparatory Navigations
                                    </h5>
                                @elseif ($countAside - 1 == $loop->iteration)
                                    <section class="page-aside-section">
                                        <h5 class="page-aside-title">
                                            Weaving Navigations
                                        </h5>
                            @endif

                            @if ($loop->first || $countAside - 1 == $loop->iteration)
                                <div class="list-group">
                            @endif

                            <a class="list-group-item {{ $aside['name'] == $name ? 'active' : '' }}"
                                href="{{ $aside['link'] }}"><i class="icon {{ $aside['icon'] }}"
                                aria-hidden="true"></i>{{ $aside['display_name_full'] }}</a>

                        @if ($loop->last || $countAside - 2 == $loop->iteration)
                        </div>
                        </section>
                        @endif
                    @endif
            @endforeach
        @elseif ($tree == 'finishing')
            @foreach ($menusAside[$tree] as $i => $aside)
                @if ($loop->first)
                    <section class="page-aside-section">
                        <h5 class="page-aside-title">
                            Transaction Navigations
                        </h5>
                    @elseif ($loop->iteration == 4)
                        <section class="page-aside-section">
                            <h5 class="page-aside-title">
                                Finishing Navigations
                            </h5>
                @endif
                @if ($loop->first || $loop->iteration == 4)
                    <div class="list-group">
                @endif
                <a class="list-group-item {{ $aside['name'] == $name ? 'active' : '' }}" href="{{ $aside['link'] }}"><i
                        class="icon {{ $aside['icon'] }}" aria-hidden="true"></i>{{ $aside['display_name_full'] }}</a>
                @if ($loop->last || $loop->iteration == 3)
        </div>
        </section>
        @endif
        @endforeach
    @else
        <section class="page-aside-section">
            <h5 class="page-aside-title">
                {{ ucwords($tree) }} Navigations
            </h5>
            <div class="list-group">
                @foreach ($menusAside[$tree] as $aside)
                    <a class="list-group-item {{ $aside['name'] == $name ? 'active' : '' }}"
                        href="{{ $aside['link'] }}"><i class="icon {{ $aside['icon'] }}"
                            aria-hidden="true"></i>{{ $aside['display_name_full'] }}</a>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</div>
</div>
</div>
