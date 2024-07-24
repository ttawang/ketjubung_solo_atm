@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading"></div>
        <div id="formWrapper"></div>
        <div class="panel-body" id="mainWrapper">
            <div class="form-group row">
                <div class="col-md-12">
                    {!! App\Helpers\Template::tools(['tambah', 'refresh']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0"
                        id="tablePengirimanDyeingGresik">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal</th>
                                <th>No. SPK</th>
                                <th>Tipe</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-loading">
            <div class="loader loader-grill"></div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            tableAjax = $('#tablePengirimanDyeingGresik').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: "{{ getCurrentRoutes() }}",
                lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]],
                columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'tanggal_custom',
                    name: 'tanggal_custom',
                    searchable: false
                }, {
                    data: 'nomor',
                    name: 'nomor',
                    searchable: false
                }, {
                    data: 'tipe_custom',
                    name: 'tipe_custom',
                    searchable: false
                },{
                    data: 'catatan',
                    name: 'catatan',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false
                }],
                initComplete: () => {

                }
            })
        })

        function goToDetail(id) {
            detailForm({
                url: `{{ url('helper/detailForm') }}/${id}`,
                data: {
                    id: id,
                    model: 'PengirimanDyeingGresik'
                },
                callback: () => {
                    // initSelect2(false, false);
                }
            })
        }

        function initDetailTable(id, data) {
            let route = `{{ getCurrentRoutes('show', ['%id']) }}`;
            let routeWithParam = route.replace('%id', id);
            tableAjaxDetail = $(`#tablePengirimanDyeingGresikDetail`).DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                retrieve: true,
                responsive: true,
                ordering: false,
                ajax: `${routeWithParam}`,
                lengthMenu: [[15, 25, 50, -1], [15, 25, 50, "All"]],
                columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (render, type, row, meta) => {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: 'tanggal_custom',
                    name: 'tanggal',
                    searchable: false
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang',
                    searchable: false
                }, {
                    data: null,
                    name: 'volume_1',
                    searchable: false,
                    render: (data) => {
                        return `${formatNumber(data.volume_1, data.satuan_utama)} ${data.satuan_utama}`;
                    }
                }, {
                    data: null,
                    name: 'volume_2',
                    searchable: false,
                    render: (data) => {
                        return (data.id_satuan_2) ? `${formatNumber(data.volume_2, data.satuan_pilihan)} ${data.satuan_pilihan}` : '-';
                    }
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false
                }],
                initComplete: () => {
                    $('.panel').removeClass('is-loading');
                }
            })
        }

        function changeGudang(element) {
            let isEdit = $('#idDetail').val() != '';
            if (!isEdit) {
                let value = element.val() || 9999;
                $('#select_gudang').val(value);
                select2Element('select_barang', true);
                $('input[name="input[id_barang]"]').val('');
                $('#select_barang').empty().val('');
            }
        }

        function changeBarang(element) {
            if (element.val() == null) {
                $('input[name="input[id_barang]"]').val('');
                $('#select_barang').empty().val('');
                return;
            }

            let value = element.select2('data')[0];
            let idBarang = value.id_barang;
            let idWarna = value.id_warna;
            let namaWarna = value.nama_warna;
            let valueStokUtama = (value.stok_utama == '0' || value.stok_utama == null) ? '' : value.stok_utama;
            let valueStokPilihan = (value.stok_pilihan == '0' || value.stok_pilihan == null) ? '' : value.stok_pilihan;
            let volume1 = value.volume_1 || 0;
            let volume2 = value.volume_2 || 0;

            $('#wrapperFieldWarna').html('')
            if (idWarna) {
                $('#wrapperFieldWarna').html(`<div class="form-group"><label>Warna</label><input type="hidden" name="input[id_warna]" value="${idWarna}">
                <input type="text" class="form-control" id="nama_warna" value="${namaWarna}" readonly></div>`);
            }

            $('input[name="input[id_barang]"]').val(idBarang);
            $('input[name="input[volume_1]"]').val(volume1 || valueStokUtama);
            $('input[name="input[volume_2]"]').val(volume2 || valueStokPilihan);

            if (typeof(value.stok_utama) != 'undefined' && typeof(value.stok_pilihan) != 'undefined') {
                setSuggestionStok(valueStokUtama || 0, valueStokPilihan || 0);
            }
        }
    </script>
@endsection
