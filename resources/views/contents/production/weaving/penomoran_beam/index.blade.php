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
            var URL = 'penomoran_beam/view';
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
                url: `penomoran_beam/simpan`,
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
                            backdrop: false
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
                            backdrop: false
                        }).then((result) => {
                            $('#modal-kelola').modal('show');
                            table.ajax.reload();
                        });
                    }
                }
            });
        }
    </script>
@endsection