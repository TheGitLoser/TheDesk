@extends('logout.layout.app', ['activePage' => 'logout.login', 'title' => "The Desk"])

@section('content')
<div class="container" style="height: auto;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">
            <h1 class="text-white text-center">Welcome to The Desk.</h1>
        </div>

        {{-- login form --}}
        <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
            <form class="form" id="form" method="POST">
                @csrf

                <div class="card card-login card-hidden mb-3">
                    <div class="card-header card-header-primary text-center">
                        <h4 class="card-title"><strong>Login</strong></h4>
                        <div class="social-line">
                            {{-- <a href="#pablo" class="btn btn-just-icon btn-link btn-white">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#pablo" class="btn btn-just-icon btn-link btn-white">
                                <i class="fab fa-twitter-square"></i>
                            </a>
                            <a href="#pablo" class="btn btn-just-icon btn-link btn-white">
                                <i class="fab fa-google-plus-g"></i>
                            </a> --}}
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="card-description text-center">
                            Hello
                        </p>

                        <div class="bmd-form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">email</i>
                                    </span>
                                </div>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email..." required value='businessAdmin@example.com'>
                            </div>
                        </div>

                        <div class="bmd-form-group mt-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">lock_outline</i>
                                    </span>
                                </div>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password..." required value='123'>
                            </div>
                        </div>

                        <div class="form-check mr-auto ml-3 mt-3">
                            {{-- <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                Remember me
                                <span class="form-check-sign">
                                    <span class="check"></span>
                                </span>
                            </label> --}}
                        </div>
                    </div>
                    <div class="text-center mx-auto text-danger font-weight-bold" id="errorMsg"></div>
                    <div class="card-footer justify-content-center">
                        <button type="submit" class="btn btn-primary btn-link btn-lg">Lets Go</button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-6">
                    {{-- @if (Route::has('password.request')) --}}
                    {{-- <a href="{{ route('logout.forgotPassword') }}" class="text-light">
                        <small>Forgot password?</small>
                    </a> --}}
                    {{-- @endif --}}
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('logout.register') }}" class="text-light">
                        <small>Create new account</small>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
    $('#form').submit(function(e){
        e.preventDefault();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('ajax.login') }}",
            method: 'post',
            data: {
                email: $('#email').val(),
                password: $('#password').val(),
                remember: $('#remember').val()
            },
            success: function(response){
                if (response['output']['result'] == 'true') {
                    window.location = response['output']['redirect'];
                }else{
                    $('#errorMsg').text(response['output']['message']);
                    console.log(response['output']['message']);
                }
            }
        });
    });
</script>
@endpush