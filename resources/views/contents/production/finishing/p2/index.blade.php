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
            mode = 'parent';
            var table = '';
            view(mode);
        });

        function view(mode, id = null) {
            var URL = '';
            if (!id) {
                URL = `p2/view/${mode}`;
            } else {
                URL = `p2/view/${mode}/${id}`;

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
                        url: `p2/hapus/${id}/${mode}`,
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
