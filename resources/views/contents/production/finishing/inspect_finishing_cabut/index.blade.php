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
        $(function() {
            var table = '';
            view();
        });

        function view() {
            var URL = 'inspect_finishing_cabut/view';
            $.ajax({
                url: URL,
                type: "get",
                dataType: "html",
                success: function(html) {
                    $('#mainWrapper').html(html);
                    table();
                },
                error: function() {
                    alert("Error");
                },
            });
        }

        function simpan(this_) {
            var form = $('#form')[0];
            var formData = new FormData(form);
            $.ajax({
                url: `inspect_finishing_cabut/simpan`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(respon) {
                    if (respon.success == true) {
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
                        url: `inspect_finishing_cabut/hapus/${id}`,
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
