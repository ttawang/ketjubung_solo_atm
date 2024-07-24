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
            view(mode);
        });

        var mode = 'parent';
        var table = '';

        function view(mode, id = null) {
            var URL = '';
            if (!id) {
                URL = `inspect_grey/view/${mode}`;
            } else {
                URL = `inspect_grey/view/${mode}/${id}`;

            }
            $.ajax({
                url: URL,
                type: "get",
                dataType: "html",
                success: function(html) {
                    $('#mainWrapper').html(html);
                    if (id) {
                        table(mode, id);
                    } else {
                        table(mode);
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
            var id_inspecting_grey = this_.data('id_inspecting_grey');
            if (id_inspecting_grey == 0) {
                view(mode, id);
            } else {
                $.ajax({
                    url: `{{ url('production/inspect_grey_2/view/${id_inspecting_grey}') }}`,
                    type: "get",
                    dataType: "html",
                    success: function(html) {
                        $('#mainWrapper').html(html);
                        table(id_inspecting_grey)
                    },
                    error: function() {
                        alert("Error");
                    },
                });
            }

        }

        function parent(this_) {
            mode = 'parent';
            view(mode);
        }

        function simpan(this_) {
            var form = $('#form')[0];
            var formData = new FormData(form);
            $.ajax({
                url: `inspect_grey/simpan`,
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

        function simpanKualitas(this_) {
            var form = $('#form')[0];
            var formData = new FormData(form);
            var id_inspecting_grey = this_.data('id_inspecting_grey');
            if (id_inspecting_grey == 0) {
                var uri = `inspect_grey/simpan`;
            } else {
                var uri = `{{ url('production/inspect_grey_2/simpan-kualitas/${id_inspecting_grey}') }}`;
            }
            $.ajax({
                url: uri,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(respon) {
                    if (respon.success == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: respon.message,
                        }).then((result) => {

                        });
                    } else {
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
                        }).then((result) => {});
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
                    var id_inspecting_grey = this_.data('id_inspecting_grey');
                    if (id_inspecting_grey == 0) {
                        var uri = `inspect_grey/hapus/${mode}/${id}`;
                    } else {
                        var uri = `{{ url('production/inspect_grey_2/hapus/${id_inspecting_grey}') }}`;
                    }
                    $.ajax({
                        url: uri,
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
