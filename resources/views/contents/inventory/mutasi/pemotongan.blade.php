<div class="col-md-12">
    <div class="col-md-12">
        <form class="form" method="post" autocomplete="off">
            @csrf
            <input type="hidden" name="mode" id="mode" value="pemotongan_sarung">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Tanggal</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" onchange="" name="tanggal" id="tanggal" required />
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Mesin</label>
                    <select id="mesin" style="width: 100%;" class="form-select select2" name="mesin[]" multiple="multiple">
                        @foreach ($mesin as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Motif</label>
                    <select id="motif" style="width: 100%;" class="form-select select2" name="motif[]" multiple="multiple">
                        @foreach ($motif as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Warna</label>
                    <select id="warna" style="width: 100%;" class="form-select select2" name="warna[]" multiple="multiple">
                        @foreach ($warna as $i)
                            <option value="{{ $i->id }}">{{ $i->alias }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-5">
                    <br>
                    <div class="btn-group" aria-label="Button group with nested dropdown" role="group">
                        <button type="button" class="btn btn-sm btn-primary waves-effect waves-classic" onclick="lihat($(this))">Lihat</button>
                        {{-- <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle waves-effect waves-classic" id="lihat_dropdown" data-toggle="dropdown" aria-expanded="false">
                                <i class="icon md-print mr-2"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="lihat_dropdown" role="menu">
                                <a class="dropdown-item" id="btn_lihat_pdf" role="menuitem">PDF</a>
                                <a class="dropdown-item" id="btn_lihat_excel" role="menuitem">Excel</a>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </form>
        <br>
        <div id="div-table">

        </div>
    </div>
</div>
<script>
    $(function() {
        $('.select2').select2({
            closeOnSelect: false
        });
        lihat();
    });

    function lihat(this_) {
        $.ajax({
            url: `mutasi/table`,
            type: "get",
            dataType: "html",
            data: {
                mode: $('#mode').val(),
                tanggal: $('#tanggal').val(),
                warna: $('#warna').val(),
                motif: $('#motif').val(),
                mesin: $('#mesin').val(),
            },
            success: function(html) {
                $('#div-table').html(html);
            },
            error: function() {
                alert("Error");
            },
        });
    }
</script>
