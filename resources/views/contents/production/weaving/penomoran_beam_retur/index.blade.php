@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading"></div>
        <div id="formWrapper"></div>
        <div class="panel-body" id="mainWrapper">
            <div class="form-group row">
                <div class="col-md-12">
                    {!! App\Helpers\Template::tools(['tambahForm', 'refresh']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="tableBeamRetur">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th width="80px">Tanggal</th>
                                <th>Beam (Retur)</th>
                                <th>Beam (Baru)</th>
                                <th>Tipe Beam</th>
                                <th>Volume</th>
                                <th>Catatan</th>
                                <th width="80px">Aksi</th>
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
        let timer;
        $(document).ready(function() {
            tableAjax = $('#tableBeamRetur').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ordering: false,
                ajax: "{{ getCurrentRoutes() }}",
                lengthMenu: [15, 25, 50, "All"],
                columns: [{
                    data: null,
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
                    data: 'nama_barang_retur',
                    name: 'nama_barang_retur',
                    searchable: false
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang',
                    searchable: false
                }, {
                    data: 'tipe_beam',
                    name: 'tipe_beam',
                    searchable: false
                }, {
                    data: 'volume_2',
                    name: 'volume_2',
                    searchable: false
                }, {
                    data: 'catatan',
                    name: 'catatan',
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false,
                    render: (render, display, row) => {
                        return (row.count_beam > 0) ?
                            '<span class="badge badge-outline badge-success">Sudah Dikirim</span>' :
                            render;
                    }
                }],
                initComplete: () => {

                }
            })
        })

        function showFormView(id = '') {
            detailForm({
                url: `{{ url('helper/detailFormView') }}/${id}`,
                data: {
                    id: id,
                    model: 'PenomoranBeamRetur',
                    view: true
                }
            })
        }

        function goToDetail(id) {
            showFormView(id);
        }

        function resetFormBeamRetur() {
            $('input[name="input[id_barang]"]').val('');
            $('input[name="input[id_satuan_1]"]').val('');
            $('input[name="input[id_satuan_2]"]').val('');
            $('input[name="input[id_beam]"]').val('');
            $('#no_beam').val('');
            $('#no_kikw').val('');
            $('#nama_warna').val('');
            $('#nama_motif').val('');
            $('#nama_mesin').val('');
            $('#tipe_pra_tenun_txt').val('');
            $('#sizing_txt').val('');
            $('#select_barang_baru').empty();
            $('#select_warna').empty();
            $('#select_motif').empty();
            $('#select_nomor_beam').empty();
            $('#select_mesin').empty();
            $('.volume_1').val('');
            $('.volume_2').val('');
        }

        function changeGudang(element) {
            let isEdit = $('#idDetail').val() != '';
            if (!isEdit) {
                let value = element.val() || 9999;
                $('#select_gudang').val(value);
                select2Element('select_barang', true);
                resetFormBeamRetur();
            }
        }

        function changeBeam(element) {
            if (element.val() == null) {
                resetFormBeamRetur();
                return;
            }

            let value = element.select2('data')[0];
            let idBarang = value.id_barang || '';
            let idWarna = value.id_warna || '';
            let idMotif = value.id_motif || '';
            let idSatuan1 = value.id_satuan_1 || '';
            let idSatuan2 = value.id_satuan_2 || '';
            let namaWarna = value.nama_warna || '';
            let namaMotif = value.nama_motif || '';
            // let namaSatuan1 = value.nama_satuan_1 || '';
            // let namaSatuan2 = value.nama_satuan_2 || '';
            let valueStokUtama = (value.stok_utama == '0' || value.stok_utama == null) ? '' : value.stok_utama;
            let valueStokPilihan = (value.stok_pilihan == '0' || value.stok_pilihan == null) ? '' : value.stok_pilihan;
            // let valueVolume1 = (value.volume_1 == '0' || value.volume_1 == null) ? '' : value.volume_1;
            // let valueVolume2 = (value.volume_1 == '0' || value.volume_2 == null) ? '' : value.volume_2;
            let currentCode = value.code;
            let isSizing = value.is_sizing || 'TIDAK';
            let idBeam = value.id_beam || '';
            let noBeam = value.no_beam || '';
            let idMesin = value.id_mesin || '';
            let namaMesin = value.nama_mesin || '';
            let noKikw = value.no_kikw || '';
            let tipePraTenun = value.tipe_pra_tenun || '';
            let namaBarang = value.nama_barang.split(' | ');

            $('input[name="input[id_barang]"]').val(idBarang);
            $('input[name="input[id_satuan_1]"]').val(idSatuan1);
            $('input[name="input[id_satuan_2]"]').val(idSatuan2);
            $('input[name="input[id_beam]"]').val(idBeam);
            $('input[name="input[id_mesin]"]').val(idMesin);
            $('input[name="input[id_warna]"]').val(idWarna);
            $('input[name="input[id_motif]"]').val(idMotif);
            $('input[name="input[code]"]').val(currentCode);

            $('#no_beam').val(noBeam);
            $('#no_kikw').val(noKikw);
            $('#nama_warna').val(namaWarna);
            $('#nama_motif').val(namaMotif);
            $('#nama_mesin').val(namaMesin);
            $('#tipe_pra_tenun_txt').val(tipePraTenun);
            $('#sizing_txt').val(isSizing);
            $('.volume_1').val(valueStokUtama);
            $('.volume_2').val(valueStokPilihan);

            let selectedOptionBaru = {
                select_barang_baru: {
                    id: idBarang,
                    text: namaBarang[2],
                },
                select_warna: {
                    id: idWarna,
                    text: namaWarna
                },
                select_motif: {
                    id: idMotif,
                    text: namaMotif
                },
                select_nomor_beam: {
                    id: idBeam,
                    text: noBeam
                },
                select_mesin: {
                    id: idMesin,
                    text: namaMesin
                }
            };
            // $('#no_kikw_baru').val(noKikw);
            selectedOption(selectedOptionBaru);
        }

        function onCheckNoKikw(element) {
            let param = element.val();
            clearTimeout(timer);
            timer = setTimeout(function validate() {
                $.ajax({
                    url: "{{ route('helper.getNomorKikw') }}",
                    type: "GET",
                    data: {
                        param: param
                    },
                    success: (data) => {
                        let oldKikw = element.data('no-kikw');
                        if (param == oldKikw) {
                            $('#txtAlertNoKikw').html(
                                `<small class="text-success">*) Nomor Kikw dapat digunakan!</small>`
                            );
                            $('#btnSubmit').prop('disabled', false);
                        } else {
                            if (data.length > 0) {
                                $('#txtAlertNoKikw').html(
                                    `<small class="text-danger">*) Nomor Kikw sudah digunakan!</small>`
                                );
                                $('#btnSubmit').prop('disabled', true);
                            } else {
                                $('#txtAlertNoKikw').html(
                                    `<small class="text-success">*) Nomor Kikw dapat digunakan!</small>`
                                );
                                $('#btnSubmit').prop('disabled', false);
                            }
                        }

                        if (param == '') $('#txtAlertNoKikw').html('');
                    }
                })
            }, 800);
        }

        function submitForm(event, this_) {
            event.preventDefault();
            showConfirmDialog({
                icon: 'icon md-alert-triangle',
                title: 'Konfirmasi Beam Nomor Baru?',
                content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
                autoClose: true,
                formButton: 'saveorupdate',
                textButton: 'Selesai',
                callback: () => {
                    $.ajax({
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        data: this_.serialize(),
                        success: (message) => {
                            toastr.success(message);
                            closeForm();
                        },
                        complete: () => {}
                    })
                }
            });

        }
    </script>
@endsection
