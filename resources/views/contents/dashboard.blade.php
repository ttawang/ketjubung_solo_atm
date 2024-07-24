@extends('layouts.main', $menuAssets)

@section('css')
    <link rel="stylesheet" href="{{ asset('css/chartist.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery-jvectormap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chartist-plugin-tooltip.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/v1.min.css') }}">
@endsection

@section('content')
    <div class="row" data-plugin="matchHeight" data-by-row="true">
        <div class="col-xl-12">
            <div class="card card-shadow" id="widgetLineareaOne">
                <div class="card-block" style="height: 550px;">
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/jquery.matchHeight-min.js') }}"></script>
    <script src="{{ asset('js/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('js/chartist.min.js') }}"></script>
    <script src="{{ asset('js/chartist-plugin-tooltip.min.js') }}"></script>
    <script src="{{ asset('js/jquery-jvectormap.min.js') }}"></script>
    <script src="{{ asset('js/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('js/matchheight.js') }}"></script>
    <script src="{{ asset('js/jvectormap.js') }}"></script>
    <script src="{{ asset('js/peity.js') }}"></script>
    <script src="{{ asset('js/v1.js') }}"></script>
    
@endsection
