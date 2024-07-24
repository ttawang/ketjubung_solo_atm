@extends('layouts.main', ['isLogin' => true, 'isAside' => false, 'isMenubarFold' => false])

@section('content')
    <div class="page-brand-info">
        <div class="brand">
            <img class="brand-img" src="{{ asset('img/logo@2x.png') }}" alt="...">
            <h2 class="brand-text font-size-40">CV. KETJUBUNG (ATM)</h2>
        </div>
        <p class="font-size-20">FXQC+F3J, Area Sawah, Kedungjeruk, Kec. Mojogedang, Kabupaten Karanganyar, Jawa Tengah 57752
        </p>
    </div>

    <div class="page-login-main">
        <div class="brand hidden-md-up">
            <img class="brand-img" src="{{ asset('img/logo@2x.png') }}" alt="...">
            <h3 class="brand-text font-size-40">CV.KETJUBUNG (ATM)</h3>
        </div>
        <h3 class="font-size-24">Sign In</h3>
        <p>Silahkan untuk mengisi form dibawah.</p>

        <form action="{{ url('login') }}" method="POST" onsubmit="submitPostLogin(event, $(this))" autocomplete="off">
            @csrf
            <div class="form-group form-material floating" data-plugin="formMaterial">
                <input type="text" class="form-control empty" id="inputEmail" name="email">
                <label class="floating-label" for="inputEmail">Username</label>
            </div>
            <div class="form-group form-material floating" data-plugin="formMaterial">
                <input type="password" class="form-control empty" id="inputPassword" name="password">
                <label class="floating-label" for="inputPassword">Password</label>
            </div>
            {{-- <div class="form-group clearfix">
                <div class="checkbox-custom checkbox-inline checkbox-primary float-left">
                    <input type="checkbox" id="remember" name="checkbox">
                    <label for="inputCheckbox">Remember me</label>
                </div>
                <a class="float-right" href="javascript:void(0);">Forgot password?</a>
            </div> --}}
            <button type="submit" class="btn btn-primary btn-block"><i class="icon md-sign-in mr-2"></i> Sign in</button>
        </form>

        <footer class="page-copyright">
            <p>WEBSITE BY AGSATU</p>
            <p>© 2023. All RIGHT RESERVED.</p>
            <div class="social">
                <a class="btn btn-icon btn-round social-twitter mx-5" href="javascript:void(0)">
                    <i class="icon bd-twitter" aria-hidden="true"></i>
                </a>
                <a class="btn btn-icon btn-round social-facebook mx-5" href="javascript:void(0)">
                    <i class="icon bd-facebook" aria-hidden="true"></i>
                </a>
                <a class="btn btn-icon btn-round social-google-plus mx-5" href="javascript:void(0)">
                    <i class="icon bd-google-plus" aria-hidden="true"></i>
                </a>
            </div>
        </footer>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            @if (Session::has('message'))
                toastr.success("{{ Session::get('message') }}");
            @endif
        })

        function submitPostLogin(event, this_) {
            event.preventDefault();
            $.ajax({
                url: "{{ url('login') }}",
                type: this_.attr('method'),
                beforeSend: () => {
                    $('button[type="submit"] > i').removeClass('md-sign-in').addClass('md-spinner spin');
                },
                data: this_.serialize(),
                success: (response) => {
                    // toastr.success(response['message']);
                    toastr.info("Login sedang diproses, Harap Tunggu...", "Loading", {
                        progressBar: true
                    });
                    window.location.href = response['route'];
                },
                error: ({
                    responseText
                }) => {
                    toastr.error(responseText);
                },
                complete: () => {
                    $('button[type="submit"] > i').removeClass('md-spinner spin').addClass('md-sign-in');
                }
            })
        }
    </script>
@endsection
