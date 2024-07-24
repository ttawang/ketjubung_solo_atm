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
                URL = `sizing/view/${mode}`;
            } else {
                URL = `sizing/view/${mode}/${id}`;

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

        function simpan(this_) {
            var form = $('#form')[0];
            var formData = new FormData(form);
            let reportValidity = form.reportValidity();

            if (!reportValidity) {
                $('#modal-kelola').modal('hide');
                Swal.fire({
                    icon: 'warning',
                    title: 'Gagal',
                    text: 'Pastikan Semua Form diisi!',
                }).then((result) => {
                    // table.ajax.reload();
                    $('#modal-kelola').modal('show');
                });
                return false;
            }

            $.ajax({
                url: `sizing/simpan`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(respon) {
                    if (respon.success == true) {
                        $('#modal-kelola').modal('hide');
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
                        url: `sizing/hapus/${id}/${mode}`,
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