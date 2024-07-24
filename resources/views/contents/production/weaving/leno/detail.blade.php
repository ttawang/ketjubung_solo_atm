<div class="col-md-12">
    @if ($data->validated_at)
        <h5 class="text-right">Tanggal Validasi :
            &nbsp;<em>{{ tglIndoFull($data->validated_at) }}</em><i class="icon md-check-circle ml-2 text-success"></i></h5>
    @endif
    <div class="row text-center">
        <div class="col-md-12">
            <h3><span class="badge badge-outline badge-primary">Nomor : {{ $data->nomor }}</span></h3>
        </div>
    </div>
</div>
<br>
<div class="nav-tabs-horizontal" data-plugin="tabs">
    <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-toggle="tab" role="tab" data-tab="input" onclick="tab($(this))">
                <i class="icon md-format-valign-bottom mr-2"></i> Input
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-toggle="tab" role="tab" data-tab="output" onclick="tab($(this))">
                <i class="icon md-format-valign-top mr-2"></i> Output
            </a>
        </li>
    </ul>
    <div class="tab-content py-20">
        <div class="tab-pane active" id="tab-content" role="tabpanel">
        </div>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-12">
        <button type="button" class="btn btn-default btn-sm waves-effect waves-classic" onclick="parent($(this));">
            <i class="icon md-arrow-left mr-2"></i> Kembali
        </button>
        {{-- <button type="button" class="btn btn-warning btn-sm waves-effect waves-classic float-right" onclick="cetak($(this));">
            <i class="icon md-print mr-2"></i> Cetak
        </button> --}}
    </div>
</div>

<script type="text/javascript">
    $(function() {
        tipe = 'input';
        id = '{{ $data->id }}';
        viewDetail(id, tipe);
    });

    function viewDetail(id = null, tipe = null) {
        var URL = '';
        URL = `leno/view/detail/${id}/${tipe}`;
        $.ajax({
            url: URL,
            type: "get",
            dataType: "html",
            success: function(html) {
                $('#tab-content').html(html);
                tableDetail(id, tipe);
            },
            error: function() {
                alert("Error");
            },
        });
    }

    function tab(this_) {
        tipe = this_.data('tab')
        viewDetail(id, tipe);
    }
</script>
