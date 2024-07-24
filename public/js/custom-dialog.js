function showConfirmDialog({
    icon: icon,
    title: title,
    content: content,
    autoClose: autoClose,
    formButton: formButton,
    textButton: textButton,
    onContentReady: onContentReady,
    callback: callback
}) {
    let button = {}
    let type = "";
    let isAutoClose = (autoClose) ? 'batal|8000' : false;

    if (formButton === 'saveorupdate') {
        type = "green";
        button = {
            batal: {
                text: 'Batal'
            },
            submit: {
                text: textButton ?? 'Submit',
                btnClass: 'btn-success',
                action: function () {
                    if (callback != null) return callback();
                }
            }
        }
    }

    if (formButton === 'delete') {
        type = "red";
        button = {
            batal: {
                text: 'Batal'
            },
            submit: {
                text: textButton ?? 'Delete',
                btnClass: 'btn-danger waves-effect waves-classic',
                action: function () {
                    if (callback != null) return callback();
                }
            }
        }
    }

    if (formButton === 'export') {
        type = "orange";
        button = {
            batal: {
                text: 'Batal'
            },
            submit: {
                text: textButton ?? 'Export',
                btnClass: 'btn-warning',
                action: function () {
                    if (callback != null) return callback();
                }
            }
        }
    }

    $.confirm({
        icon: `${icon}`,
        title: `${title}`,
        content: `${content}`,
        autoClose: isAutoClose,
        typeAnimated: true,
        type: `${type}`,
        theme: 'material',
        buttons: button,
        onContentReady: function () {
            let jc = this;
            let checkOnceClick = 0;
            $('.jconfirm-box').trigger('focus');
            $(document).keydown(function (event) {
                if (event.keyCode == 13 && checkOnceClick == 0) {
                    event.preventDefault();
                    jc.$$submit.trigger('click');
                    ++checkOnceClick;
                }
            });
            if (onContentReady != null) onContentReady();
        }
    });
}