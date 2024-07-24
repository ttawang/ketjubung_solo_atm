@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-body">
            <div class="nav-tabs-horizontal" data-plugin="tabs">
                <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-toggle="tab" role="tab" data-tab="rekap" onclick="tab($(this))">
                            <i class="icon md-format-valign-bottom mr-2"></i> Produksi Rekap
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" role="tab" data-tab="produksi" onclick="tab($(this))">
                            <i class="icon md-format-valign-bottom mr-2"></i> Produksi Detail
                        </a>
                    </li>
                    {{-- <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" role="tab" data-tab="pemotongan_sarung" onclick="tab($(this))">
                            <i class="icon md-format-valign-top mr-2"></i> Pemotongan Sarung
                        </a>
                    </li> --}}
                </ul>
                <div class="tab-content py-20">
                    <div class="tab-pane active" id="tab-content" role="tabpanel">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(function() {
            mode = 'rekap';
            var table = '';
            view(mode);
        });

        function view(mode) {
            var URL = `mutasi/view/${mode}`;
            $.ajax({
                url: URL,
                type: "get",
                dataType: "html",
                success: function(html) {
                    $('#tab-content').html(html);
                },
                error: function() {
                    alert("Error");
                },
            });
        }

        function tab(this_) {
            mode = this_.data('tab')
            view(mode);
        }
    </script>
@endsection
