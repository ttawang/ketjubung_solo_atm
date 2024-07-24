@if (!isset($isLogin))
    <footer class="site-footer">
        <div class="site-footer-legal">© 2023 <a
                href="http://themeforest.net/item/remark-responsive-bootstrap-admin-template/11989202">{{ env('APP_NAME') }}</a>
        </div>
        <div class="site-footer-right">
            Crafted with <i class="red-600 icon md-favorite"></i> by <a
                href="https://themeforest.net/user/creation-studio">AGSATU</a>
        </div>
    </footer>
@endif

<script src="{{ asset('js/babel-external-helpers.js') }}"></script>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/animsition.min.js') }}"></script>
<script src="{{ asset('js/jquery.mousewheel.min.js') }}"></script>
<script src="{{ asset('js/jquery-asScrollbar.min.js') }}"></script>
<script src="{{ asset('js/jquery-asScrollable.min.js') }}"></script>
<script src="{{ asset('js/jquery-asHoverScroll.min.js') }}"></script>
<script src="{{ asset('js/waves.min.js') }}"></script>

<!-- Plugins -->
<script src="{{ asset('js/switchery.min.js') }}"></script>
<script src="{{ asset('js/intro.min.js') }}"></script>
<script src="{{ asset('js/screenfull.min.js') }}"></script>
<script src="{{ asset('js/jquery-slidePanel.min.js') }}"></script>

<!-- Scripts -->
<script src="{{ asset('js/Component.js') }}"></script>
<script src="{{ asset('js/Plugin.js') }}"></script>
<script src="{{ asset('js/Base.js') }}"></script>
<script src="{{ asset('js/Config.js') }}"></script>

<script src="{{ asset('js/Menubar.js') }}"></script>
<script src="{{ asset('js/GridMenu.js') }}"></script>
<script src="{{ asset('js/Sidebar.js') }}"></script>
<script src="{{ asset('js/PageAside.js') }}"></script>
<script src="{{ asset('js/menu.js') }}"></script>

<script src="{{ asset('js/colors.js') }}"></script>
<script src="{{ asset('js/tour.js') }}"></script>
{{-- <script>
    Config.set('assets', '../assets');
</script> --}}

<!-- Page -->
<script src="{{ asset('js/Site.js') }}"></script>
<script src="{{ asset('js/asscrollable.js') }}"></script>
<script src="{{ asset('js/slidepanel.js') }}"></script>
<script src="{{ asset('js/switchery.js') }}"></script>
<script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
<script src="{{ asset('js/toastr.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/custom-dialog.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ asset('js/accounting.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/input-group-file.js') }}"></script>

@if (isset($isLogin))
    <script src="{{ asset('js/login/jquery.placeholder.js') }}"></script>
    <script src="{{ asset('js/login/jquery-placeholder.js') }}"></script>
    <script src="{{ asset('js/login/material.js') }}"></script>

    <script type="text/javascript">
        toastr.options = {
            positionClass: 'toast-top-right'
        };
    </script>
@else
    @if ($isDatatable ?? false)
        {{-- <script src="{{ asset('js/jquery.multi-select.js') }}"></script>
        <script src="{{ asset('js/multi-select.js') }}"></script> --}}
        <script src="{{ asset('js/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('js/datatables/fixedHeader.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('js/datatables/fixedColumns.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('js/datatables/rowGroup.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('js/datatables/scroller.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('js/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('js/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('js/datatables/jquery-asRange.min.js') }}"></script>
        <script src="{{ asset('js/datatables/bootbox.min.js') }}"></script>
        <script src="{{ asset('js/datatables/datatables.js') }}"></script>
        <script>
            let jqueryConfirm, tableAjax, tableAjaxDetail, tableWarnaDetail;
            let callbackData = {};
            const {
                pathname
            } = window.location;
            $(document).ready(function() {
                $('#exampleAddRow').DataTable();
            })
        </script>
    @endif

    <script type="text/javascript">
        const mainWrapper = $('#mainWrapper');
        const formWrapper = $('#formWrapper');
        let currName, currTree;
        let state = '';
        $(document).ready(function() {
            currName = "{{ $name ?? 'dashboard' }}";
            currTree = "{{ $tree ?? '' }}";

            $('title').text(`{{ $title }} | Ketjubung Solo`)
            $('.title-breadcumb').html('{{ $title }}');

            $('.site-menu > .site-menu-item').removeClass('active');
            $('.site-menu > .site-menu-item').each(function(e) {
                let names = $(this).attr('name');
                let prefixAside = $(this).attr('prefixAside');
                let displayNames = $(this).attr('display-name');
                let displayFullNames = $(this).attr('display-name-full');
                if (names === currName || (prefixAside === currTree && prefixAside != '')) {
                    $(this).addClass('active');
                    if (prefixAside == '') {
                        $('.title-breadcumb').html(displayFullNames);
                        $('title').text(`${displayNames} | Ketjubung Solo`)
                    }

                    /*if (currTree !== '') {
                        let parentTree = $(this).closest('.site-menu-item');
                        parentTree.addClass('active open');
                        let elementTrees = parentTree.find('.site-menu-sub > .site-menu-item');
                        elementTrees.each(function(el) {
                            let namedTree = $(this).attr('name');
                            if (namedTree == currName) $(this).addClass('active');
                        })
                    } else {
                        $(this).addClass('active');
                        $('.title-breadcumb').html(displayFullNames);
                        $('title').text(`${displayNames} | Ketjubung Solo`)
                    }*/
                }
            })

            toastr.options = {
                positionClass: 'toast-bottom-right'
            };

            $.ajaxSetup({
                beforeSend: () => {
                    $('.panel').addClass('is-loading');
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: (res) => {
                    $('.panel').removeClass('is-loading');
                    toastr.error(res['responseText']);
                },
                complete: () => {
                    $('.panel').removeClass('is-loading');
                    $('.tooltip').remove();
                }
            });

            $('.select2').select2({
                width: '100%'
            });

            accounting.settings = {
                currency: {
                    symbol: "Rp", // default currency symbol is '$'
                    format: {
                        pos: "%s%v",
                        neg: "( %s%v )",
                        zero: "%s%v"
                    }, // controls output: %s = symbol, %v = value/number (can be object: see below)
                    decimal: ",", // decimal point separator
                    thousand: ".", // thousands separator
                    precision: 2 // decimal places
                },
                number: {
                    precision: 2, // default precision on numbers is 0
                    thousand: ".",
                    decimal: ","
                }
            }
        })

        function htmlDecode(input) {
            var e = document.createElement('div');
            e.innerHTML = input;
            return e.childNodes[0].nodeValue;
        }

        function addFormChangePassword(element) {
            $.confirm({
                title: '<i class="icon md-key mr-2"></i> Ganti Password',
                content: `<form action="{{ route('changePassword') }}" onsubmit="submitChangePassword(event, $(this));" method="POST" class="formInput">
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" class="form-control empty" id="inputPassword" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" class="form-control confirmation-not-match empty" onkeyup="($('#inputPassword').val() == this.value) ? $(this).removeClass('confirmation-not-match').addClass('confirmation') : $(this).removeClass('confirmation').addClass('confirmation-not-match');" id="inputPasswordConfirmation" required>
                    </div>
                </form>`,
                columnClass: 'col-md-4 col-md-offset-4',
                typeAnimated: true,
                type: 'dark',
                theme: 'material',
                onOpenBefore: function() {
                    this.showLoading(true);
                },
                buttons: {
                    cancel: function() {
                        //close
                    },
                    formSubmit: {
                        text: 'Submit',
                        btnClass: 'btn-blue',
                        action: function() {
                            const form = $(`.formInput`);
                            let reportValidity = form[0].reportValidity();
                            let reportConfirmation = $('#inputPassword').val() == $('#inputPasswordConfirmation').val();
                            if (!reportConfirmation) toastr.error('Konfirmasi Password Tidak Sama');
                            if (!reportValidity || !reportConfirmation) return false;
                            form.submit();
                        }
                    },
                },
                onContentReady: function() {
                    let jc = this;
                    let checkOnceClick = 0;
                    $('.jconfirm-box').trigger('focus');
                    $(document).keydown(function(event) {
                        if (event.keyCode == 13 && checkOnceClick == 0) {
                            event.preventDefault();
                            jc.$$formSubmit.trigger('click');
                            ++checkOnceClick;
                        }
                    });

                    this.hideLoading(true);
                }
            });
        }

        function submitChangePassword(event, element) {
            event.preventDefault();
            $.ajax({
                url: element.attr('action'),
                type: element.attr('method'),
                data: element.serialize(),
                success: (message) => {
                    toastr.success(message);
                },
                complete: () => {}
            })
        }
    </script>
    @if (!$custom)
        @include('layouts.scripts')
    @else
        @include('layouts.custom-script')
    @endif
@endif

<script>
    (function(document, window, $) {
        'use strict';

        var Site = window.Site;
        $(document).ready(function() {
            Site.run();
        });
    })(document, window, jQuery);
</script>

@yield('js')
