@extends('layouts.main', $menuAssets)

@section('content')
    <div class="panel panel-primary panel-line">
        <div class="panel-heading">&nbsp</div>
        <div class="panel-body" id="mainWrapper">
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        let timer;
        $(function() {
            mode = 'parent';
            var table = '';
            view(mode);
        });

        function view(mode, id = null) {
            var URL = '';
            if (!id) {
                URL = `pakan/view/${mode}`;
            } else {
                URL = `pakan/view/${mode}/${id}`;

            }
            $.ajax({
                url: URL,
                type: "get",
                dataType: "html",
                success: function(html) {
                    $('#mainWrapper').html(html);
                    if (mode == 'parent') {
                        tableParent();
                    }
                },
                error: function() {
                    alert("Error");
                },
            });
        }

        function detail(this_) {
            mode = 'detail';
            id = this_.data('id');
            view(mode, id);
        }

        function parent(this_) {
            mode = 'parent';
            view(mode);
        }

        function simpanNota(this_) {
            var form = $('#form')[0];
            var formData = new FormData(form);
            $('#modal-kelola').modal('hide');
            Swal.fire({
                title: "Apakah anda yakin ?",
                text: "Otomatis menambahkan benang warna pakan dari pengiriman sebelumnya",
                icon: "warning",
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: "Ya",
                denyButtonText: "Tidak, Hanya Nota",
                cancelButtonText: "Batal",
                reverseButtons: true,
            }).then(function(result) {
                if (!result.isDismissed) {
                    formData.append('is_auto', result.isConfirmed ? 'YA' : 'TIDAK');
                    $.ajax({
                        url: `pakan/simpan`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(respon) {
                            if (respon.success == true) {
                                // $('#modal-kelola').modal('hide');
                                closeModal();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: respon.message,
                                }).then((result) => {
                                    table.ajax.reload();
                                });
                            } else {
                                $('#modal-kelola').modal('hide');
                                let errorMessage = '';
                                $.each(respon.messages, function(fieldName, fieldErrors) {
                                    errorMessage += fieldErrors[0];
                                    if (fieldName !== Object.keys(respon.messages).slice(-1)[0]) {
                                        errorMessage += ', ';
                                    }
                                });
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                }).then((result) => {
                                    $('#modal-kelola').modal('show');
                                    table.ajax.reload();
                                });
                            }
                        }
                    });
                }
            })
        }

        function simpan(this_) {
            var form = $('#form')[0];
            var formData = new FormData(form);
            $.ajax({
                url: `pakan/simpan`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(respon) {
                    if (respon.success == true) {
                        // $('#modal-kelola').modal('hide');
                        closeModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: respon.message,
                        }).then((result) => {
                            table.ajax.reload();
                        });
                    } else {
                        $('#modal-kelola').modal('hide');
                        let errorMessage = '';
                        $.each(respon.messages, function(fieldName, fieldErrors) {
                            errorMessage += fieldErrors[0];
                            if (fieldName !== Object.keys(respon.messages).slice(-1)[0]) {
                                errorMessage += ', ';
                            }
                        });
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMessage,
                        }).then((result) => {
                            $('#modal-kelola').modal('show');
                            table.ajax.reload();
                        });
                    }
                }
            });
        }

        function hapus(this_) {
            this_.tooltip('dispose');
            Swal.fire({
                title: "Apakah anda yakin ?",
                text: "Data yang dihapus tidak dapat dikembalikan",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Hapus",
                cancelButtonText: "Close",
                reverseButtons: true,
            }).then(function(result) {
                if (result.value === true) {
                    var id = this_.data('id');
                    $.ajax({
                        url: `pakan/hapus/${id}/${mode}`,
                        type: 'get',
                        success: function(respon) {
                            if (respon.success == true) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: respon.message,
                                }).then((result) => {
                                    table.ajax.reload();
                                });
                            } else {
                                $('#modalKelola').modal('hide');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Data gagal dihapus',
                                }).then((result) => {
                                    table.ajax.reload();
                                });
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
