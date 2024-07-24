@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-body">
            <div class="nav-tabs-horizontal" data-plugin="tabs">
                <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-toggle="tab" role="tab" data-tab="stok" onclick="tab($(this))">
                            <i class="icon md-format-valign-bottom mr-2"></i> Persediaan
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" role="tab" data-tab="beam" onclick="tab($(this))">
                            <i class="icon md-format-valign-top mr-2"></i> Persediaan Pemotongan Beam
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-toggle="tab" role="tab" data-tab="jasa_luar" onclick="tab($(this))">
                            <i class="icon md-format-valign-top mr-2"></i> Persediaan Jasa Luar
                        </a>
                    </li>
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
            mode = 'stok';
            var table = '';
            view(mode);
        });

        function view(mode) {
            var URL = `persediaan/view/${mode}`;
            $.ajax({
                url: URL,
                type: "get",
                dataType: "html",
                success: function(html) {
                    $('#tab-content').html(html);
                    table();
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
