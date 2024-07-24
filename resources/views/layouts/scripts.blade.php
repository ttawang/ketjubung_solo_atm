<script type="text/javascript">
    function addForm(this_, isDetail = false, isModal = true, callback = null) {
        let data = this_.data();
        data.isDetail = isDetail;
        let route = `{{ getCurrentRoutes('create') }}`;
        if (typeof(data.route) !== 'undefined' && data.route != '') route = data.route;
        $.ajax({
            url: route,
            type: 'GET',
            data: data,
            dataType: 'json',
            success: (response) => {
                if (isModal) {
                    showForm({
                        content: response['render'],
                        callback: () => {
                            if (response['selected']) selectedOption(response['selected']);
                        }
                    });
                } else {
                    showFormView({
                        content: response['render'],
                        viewOnly: false,
                        callback: () => {
                            if (response['selected']) selectedOption(response['selected']);
                            if (callback != null) callback();
                        }
                    })
                }
            }
        })
    }

    function editForm(id, isDetail = false, this_) {
        let route = `{{ getCurrentRoutes('edit', ['%id']) }}`;
        let routeWithParam = route.replace('%id', id);
        let data = this_.data();
        if (typeof(data.route) !== 'undefined' && data.route != '') {
            let customRoute = data.route;
            routeWithParam = customRoute.replace('%id', id);
        }
        data.isDetail = isDetail;
        $.ajax({
            url: routeWithParam,
            type: 'GET',
            data: data,
            dataType: 'json',
            success: (response) => {
                showForm({
                    content: response['render'],
                    callback: () => {
                        selectedOption(response['selected']);
                    }
                });
            }
        })
    }

    function showFormView({
        content: content,
        viewOnly: viewOnly,
        callback: callback
    }) {
        mainWrapper.hide();
        formWrapper.html(content);
        initSelect2();
        if (callback != null) callback();
        $('.panel').removeClass('is-loading');
    }

    function showForm({
        title: title,
        content: content,
        size: size,
        textSubmit: textSubmit,
        textCancel: textCancel,
        callback: callback,
        cancelCallback: cancelCallback
    }) {
        jqueryConfirm = $.confirm({
            title: `<i class="icon md-assignment mr-2"></i> ${title ?? 'Form'}`,
            content: `${content}`,
            columnClass: 'col-md-4 col-md-offset-4',
            typeAnimated: true,
            type: 'dark',
            theme: 'material',
            onOpenBefore: function() {
                this.showLoading();
            },
            buttons: {
                cancel: {
                    text: textCancel ?? 'Cancel',
                    action: function() {
                        if (cancelCallback != null) cancelCallback();
                    }
                },
                submit: {
                    text: textSubmit ?? 'Submit',
                    btnClass: 'btn-blue',
                    action: function() {
                        const form = $(`.formInput`);
                        let reportValidity = form[0].reportValidity();
                        if (!reportValidity) return false;
                        form.submit();
                    }
                },
            },
            onContentReady: function() {
                this.$content.find('input.number-only').keypress(function(e) {
                    var txt = String.fromCharCode(e.which);
                    if (!txt.match(/[0-9.,]/)) {
                        return false;
                    }
                });

                initSelect2();
                if (callback != null) callback();

                let jc = this;
                let checkOnceClick = 0;
                $('.jconfirm-box').trigger('focus');
                $(document).keydown(function(event) {
                    if (event.keyCode == 13 && checkOnceClick == 0) {
                        event.preventDefault();
                        jc.$$submit.trigger('click');
                        ++checkOnceClick;
                    }
                });

                jqueryConfirm.hideLoading();
            }
        });
    }

    function excludePrefixFolder(tree) {
        let listTree = ['dyeing', 'penerimaan'];
        return listTree.includes(tree)
    }

    function detailForm({
        url: url,
        data: data,
        callback: callback
    }) {
        let formData = {
            prefix: excludePrefixFolder(currTree) ? '' : currTree,
            suffix: currName
        };

        formData = $.extend(formData, data);

        $.ajax({
            url: url,
            data: formData,
            type: 'GET',
            sync: false,
            success: (response) => {
                mainWrapper.hide();

                formWrapper.html(response['render']);

                if (callback != null) callback(response);

                if (!data.view) {
                    initDetailTable(data.id, response['data'])
                } else {
                    initSelect2();
                    selectedOption(response['selected']);
                    $('.panel').removeClass('is-loading');
                }
            },
            complete: () => {
                // $('.tableDetails').DataTable();
                $('.page-header-actions').html(`<button type="button" onclick="goToDetail(${data?.id});" class="btn btn-sm btn-icon btn-primary btn-round waves-effect waves-classic" data-toggle="tooltip" data-original-title="Refresh">
                    <i class="icon md-refresh-sync spin mr-1" aria-hidden="true"></i> Refresh Page
                </button>`);
            }
        })
    }

    function selectedOption(objects = {}) {
        for (element in objects) {
            if ($.isArray(objects[element])) {
                objects[element].forEach(item => {
                    $(`#${element}`).select2("trigger", "select", {
                        data: item
                    });
                });
            } else {
                $(`#${element}`).select2("trigger", "select", {
                    data: objects[element]
                });
            }
        }
    }

    function closeForm() {
        $('.page-header-actions').html('');
        formWrapper.html('');
        mainWrapper.show();
        tableAjax.ajax.reload(null, false);
        tableAjaxDetail?.destroy();
    }

    function cetakForm(element) {
        let params = encodeQueryData(element.data());
        let url = '';
        if (element.data('model') == 'PenerimaanBarang') url = `{{ url('cetak/penerimaanBarang') }}?${params}`;
        if (element.data('model') == 'PengirimanBarang') url = `{{ url('cetak/pengirimanBarang') }}?${params}`;
        if (element.data('model') == 'Dyeing') url = `{{ url('cetak/dyeing') }}?${params}`;
        if (element.data('model') == 'Tenun') url = `{{ url('cetak/tenun') }}?${params}`;
        if (element.data('model') == 'DistribusiPakan') url = `{{ url('cetak/distribusiPakan') }}?${params}`;
        if (element.data('model') == 'PengirimanSarung') url = `{{ url('cetak/pengirimanSarung') }}?${params}`;
        if (url != '') window.open(url);
    }

    function encodeQueryData(data) {
        const ret = [];
        for (let d in data)
            ret.push(encodeURIComponent(d) + '=' + encodeURIComponent(data[d]));
        return ret.join('&');
    }

    function refreshTable(isDetail) {
        (!isDetail) ? tableAjax?.ajax.reload(null, false): tableAjaxDetail?.ajax.reload(null, false);
    }

    function submitForm(event, this_) {
        event.preventDefault();
        let isDetail = $('input[name="isDetail"]').val();
        $.ajax({
            url: this_.attr('action'),
            type: this_.attr('method'),
            data: this_.serialize(),
            success: (message) => {
                toastr.success(message);
                refreshTable(isDetail == 'true');
            },
            complete: () => {}
        })
    }

    function deleteForm(id, isDetail = false, this_) {
        showConfirmDialog({
            icon: 'icon md-alert-triangle',
            title: 'Delete Data?',
            content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
            autoClose: true,
            formButton: 'delete',
            callback: () => {
                let routeDelete = "{{ getCurrentRoutes('destroy', ['%id']) }}";
                let routeWithParam = routeDelete.replace('%id', id);
                let data = this_.data();
                if (typeof(data.route) !== 'undefined' && data.route != '') {
                    let customRoute = data.route;
                    routeWithParam = customRoute.replace('%id', id);
                }
                $.ajax({
                    url: routeWithParam,
                    type: "POST",
                    data: {
                        isDetail: isDetail,
                        _method: "DELETE"
                    },
                    success: (responseText) => {
                        toastr.success(responseText);
                        refreshTable(isDetail == 'true' || isDetail == true);
                        $('#wrapperWarna, #wrapperWarnaReturn').html('');
                    },
                    complete: () => {}
                })
            }
        });
    }

    function deleteAll(element) {
        showConfirmDialog({
            icon: 'icon md-alert-triangle',
            title: 'Delete Data?',
            content: '<b>Konfirmasi</b> ini akan menghapus semua data yang terkait dengan data tersebut!.',
            autoClose: true,
            formButton: 'delete',
            callback: () => {
                $.ajax({
                    url: "{{ route('helper.deleteAll') }}",
                    type: "POST",
                    data: {
                        id: element.attr('data-id'),
                        model: element.attr('data-model'),
                        _method: "DELETE"
                    },
                    success: (responseText) => {
                        toastr.success(responseText);
                        closeForm();
                    },
                    error: (res) => {
                        toastr.error(res['responseText']);
                    },
                    complete: () => {}
                })
            }
        });
    }


    function initSelect2(reset = false, isOnModal = true, width = '100%') {
        $('.select2').each((index, element) => {
            let routes = $(element).attr('data-route');
            let tags = $(element).attr('tags');
            let allowClear = $(element).attr('allow-clear');
            let elDropdownParent = $(element).attr('dropdown-parent');
            let dropdownParent = (typeof(elDropdownParent) !== 'undefined') ? elDropdownParent : $('.formInput')
            if (reset) $(element).empty();
            if (typeof(routes) !== 'undefined') {
                $(element).select2({
                    placeholder: $(element).attr('data-placeholder'),
                    width: width,
                    closeOnSelect: true,
                    tags: typeof(tags) !== 'undefined' ?
                        $(element).attr('tags') == 'true' : false,
                    allowClear: typeof(allowClear) !== 'undefined' ?
                        $(element).attr('allow-clear') == 'true' : true,
                    dropdownParent: isOnModal ? dropdownParent : '',
                    ajax: {
                        url: $(element).attr('data-route'),
                        dataType: 'JSON',
                        beforeSend: () => {},
                        data: function(params) {
                            let objects = {
                                param: $.trim(params.term),
                                page: params.page || 1,
                                idGudang: $('#select_gudang').val()
                            };

                            let data = $(element).data();
                            removeMultipleObjects(data, ['select2', 'select2Id', 'placeholder',
                                'route'
                            ]);
                            objects = $.extend(objects, data);
                            return objects;
                        },
                        processResults: function(data) {
                            return {
                                results: data['data'],
                                pagination: data['pagination']
                            };
                        },
                        delay: 600,
                        complete: () => {},
                        error: () => {}
                    }
                })
            } else {
                $(element).select2({
                    placeholder: $(element).attr('data-placeholder'),
                    width: '100%',
                    closeOnSelect: true,
                    allowClear: typeof(allowClear) !== 'undefined' ?
                        $(element).attr('allow-clear') == 'true' : true,
                    dropdownParent: isOnModal ? $('.formInput') : ''
                })
            }
        });
    }

    function select2Element(element, reset = false, isDropdownParent = true, width = '100%') {
        if (reset) $(`#${element}`).empty();
        $(`#${element}`).select2({
            placeholder: $(`#${element}`).attr('data-placeholder'),
            width: width,
            closeOnSelect: true,
            dropdownParent: isDropdownParent ? $('.formInput') : '',
            ajax: {
                url: $(`#${element}`).attr('data-route'),
                dataType: 'JSON',
                beforeSend: () => {},
                data: function(params) {
                    let objects = {
                        param: $.trim(params.term),
                        page: params.page || 1,
                        idGudang: $('#select_gudang').val()
                    };

                    let data = $(`#${element}`).data();
                    removeMultipleObjects(data, ['select2', 'select2Id', 'placeholder', 'route']);
                    objects = $.extend(objects, data);
                    return objects;
                },
                processResults: function(data) {
                    return {
                        results: data['data'],
                        pagination: data['pagination']
                    };
                },
                delay: 300,
                complete: () => {},
                error: () => {}
            }
        })
    }

    function changeSatuan(element) {
        let value = element.val();
        let valueQty = element.attr('data-value-qty');
        let valueVolume = element.attr('data-value-volume');
        let fieldQty = `<div class="form-group">
            <label>Qty</label>
            <input type="text" value="${valueQty}" class="form-control number-only" name="input[qty]" required>
        </div>`;
        let fieldVolume = `<div class="form-group">
            <label>Volume</label>
            <input type="text" value="${valueVolume}" class="form-control number-only" name="input[volume]" required>
        </div>`
        let template = "";
        if (value == 2 || value != 3) template += fieldQty;
        if (value == 3 || value == 2) template += fieldVolume;
        $('#wrapperFieldSatuan').html(template);
    }

    function changeGudang(element) {
        let isEdit = $('#idDetail').val() != '';
        if (!isEdit) {
            let value = element.val() || 9999;
            $('#select_gudang').val(value);
            select2Element('select_barang', true);
            resetForm();
        }
    }

    function removeMultipleObjects(obj, props) {
        for (var i = 0; i < props.length; i++) {
            if (obj.hasOwnProperty(props[i])) {
                delete obj[props[i]];
            }
        }
    }

    function setSuggestionStok(volume1 = 0, volume2 = 0, isOutput = false) {
        let text = isOutput ? 'Volume Kirim' : 'Stok'
        $('#wrapperSuggestionStok1').html(`<div class="text-right">
            <span class="font-size-12">${text}: ${volume1}</span>
        </div>`);
        $('#wrapperSuggestionStok2').html(`<div class="text-right">
            <span class="font-size-12">${text}: ${volume2}</span>
        </div>`);
    }

    function resetForm() {
        $('input[name="input[volume_1]"]').val('');
        $('select[name="input[id_satuan_1]"]').empty().val('');
        $('#txt_satuan_1').val('');
        $('input[name="input[volume_2]"]').val('');
        $('select[name="input[id_satuan_2]"]').empty().val('');
        $('#txt_satuan_2').val('');
        $('#wrapperFieldSatuan2').html('');
        $('#wrapperFieldWarna').html('');
        $('#wrapperFieldMotif').html('');
        $('#wrapperSuggestionStok1').html('');
        $('#wrapperSuggestionStok2').html('');
        $('#wrapperFieldBeam').html('');
        $('#wrapperFieldSongket').html('');
        $('#wrapperFieldTanggalPotong').html('');
        $('#wrapperFieldTipePraTenun').html('');
        $('input[name="input[id_barang]"]').val('');
        $('#select_barang').empty().val('');
        $('input[name="input[volume_1]"]').prop('readonly', false);
        $('input[name="input[volume_2]"]').prop('readonly', false);
    }

    function resetFormBeam() {
        $('txt_no_kikw').val('');
        $('#txt_mesin').val('');
    }

    function fieldSizing(value) {
        $('#wrapperFieldSizing').html('');
        if (value != null && value != '') {
            $('#wrapperFieldSizing').html(`<div class="form-group">
                <label>Sizing</label>
                <input type="text" name="is_sizing" class="form-control" value="${value}" readonly />
            </div>`)
        }
    }

    function fieldVolume(idBeam, isPakan = false) {
        if (idBeam != '' && !isPakan) {
            $('input[name="input[volume_1]"]').prop('readonly', true);
            $('input[name="input[volume_2]"]').prop('readonly', true);
        } else {
            $('input[name="input[volume_1]"]').prop('readonly', false);
            $('input[name="input[volume_2]"]').prop('readonly', false);
        }
    }

    function validateForm(element, isRollback = false) {
        showConfirmDialog({
            icon: 'icon md-alert-triangle',
            title: 'Konfirmasi Validasi Form?',
            content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
            autoClose: true,
            formButton: (isRollback) ? 'delete' : 'saveorupdate',
            textButton: (isRollback) ? 'Batalkan Validasi' : 'Validasi',
            callback: () => {
                validate({
                    objects: element.data()
                });
            }
        });
    }

    function validate({
        objects: objects,
        callback: callback
    }) {
        $.ajax({
            url: `{{ route('helper.validateForm') }}`,
            type: 'POST',
            data: objects,
            success: (response) => {
                if (callback != null) callback();
                (objects.isShow == 'parent') ? tableAjax.ajax.reload(null, false): goToDetail(response['id']);
                toastr.success(response['message']);
            }

        })
    }

    function maxInputNumber(element) {
        let input = parseFloat(element.val());
        let maxValue = parseFloat(element.data('max'));
        if (typeof(maxValue) !== 'undefined' && input > maxValue) element.val(maxValue);
    }

    function formatNumber(value, satuan = '') {
        let satuanLowerCase = satuan?.toLowerCase() || '';
        return (satuanLowerCase == 'kg') ? accounting.formatNumber(value) : value || '';
    }

    function checkCodeStokopnames(code, key) {
        let array = [];
        array.class1 = ['PB', 'BBD', 'BO', 'DW', 'CF']; //TIDAK ADA WARNA
        array.class2 = ['DO', 'BHD', 'BBW', 'BBWS', 'BPR', 'BPS', 'BBWP']; //ADA WARNA
        array.class3 = ['BL', 'BS']; //ADA WARNA ADA BEAM
        array.class4 = ['BGIG', 'BGD', 'BGID', 'JS', 'P1', 'IP1', 'FC', 'IFC', 'DR', 'JG', 'P2', 'IP2',
            'JP2'
        ]; //ADA WARNA ADA BEAM ADA GRADE ADA KUALITAS
        return array[key].includes(code);
    }

    function approval() {
        showConfirmDialog({
            icon: 'icon md-alert-triangle',
            title: 'Terima Semua Data?',
            content: '<b>Konfirmasi</b> ini akan \'batal\' secara otomatis dalam 6 seconds jika kamu tidak melanjutkan aksi.',
            autoClose: true,
            formButton: 'saveorupdate',
            callback: () => {
                let idSelected = $("input[name='selected_id[]']:checked").map(function() {
                    return $(this).val();
                }).get();
                $.ajax({
                    url: `{{ route('helper.acceptAll') }}`,
                    type: 'POST',
                    data: {
                        id: idSelected
                    },
                    success: (message) => {
                        tableAjax.ajax.reload(null, false);
                        toastr.success(message);
                    }

                })
            }
        });
    }
</script>
