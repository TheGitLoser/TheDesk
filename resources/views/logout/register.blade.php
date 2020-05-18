@extends('logout.layout.app', ['activePage' => 'logout.register', 'title' => "The Desk"])

@section('content')
<div class="container" style="height: auto;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">
            <h1 class="text-white text-center">Welcome to The Desk.</h1>
        </div>
        
        <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
            <form class="form" id="form" method="POST">
                    @csrf
                
                    <div class="card card-login card-hidden mb-3">
                        <div class="card-header card-header-primary text-center">
                            <h4 class="card-title"><strong>Register</strong></h4>
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
                        <div class="card-body ">
                            <p class="card-description text-center">Welcome</p>
                            <div class="bmd-form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="material-icons">face</i>
                                        </span>
                                    </div>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name..." required>
                                </div>
                            </div>
                            <div class="bmd-form-group mt-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="material-icons">face</i>
                                        </span>
                                    </div>
                                    <input type="text" name="name" id="displayId" class="form-control" placeholder="Display ID..." required>
                                </div>
                            </div>
                            <div class="bmd-form-group mt-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="material-icons">email</i>
                                        </span>
                                    </div>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email..." required>
                                </div>
                            </div>
                            <div class="bmd-form-group mt-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="material-icons">lock_outline</i>
                                        </span>
                                    </div>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password..." required>
                                </div>
                            </div>
                            <div class="bmd-form-group mt-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="material-icons">lock_outline</i>
                                        </span>
                                    </div>
                                    <input type="password" name="passwordConfirmation" id="passwordConfirmation" class="form-control" placeholder="Confirm Password..." required>
                                </div>
                            </div>
                            <div class="bmd-form-group mt-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="material-icons">phone</i>
                                        </span>
                                    </div>
                                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="Phone..." required>
                                </div>
                            </div>
                            <div class="bmd-form-group mt-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="material-icons">calendar_today</i>
                                        </span>
                                    </div>
                                    <input type="date" name="DOB" id="DOB" class="form-control" placeholder="Date of birth..." required>
                                </div>
                            </div>
                            {{-- <div class="form-check mr-auto ml-3 mt-3">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" id="policy" name="policy" required>
                                    <span class="form-check-sign">
                                        <span class="check"></span>
                                    </span>
                                    I agree with the <a href="#">Privacy Policy</a>
                                </label>
                            </div> --}}
                        </div>
                        <div class="text-center mx-auto text-danger font-weight-bold" id="errorMsg"></div>
                        <div class="card-footer justify-content-center">
                            <button type="submit" class="btn btn-primary btn-link btn-lg">Create account</button>
                        </div>
                    </div>
                </form>

            <div class="row">
                <div class="col-6">
                    
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('logout.login') }}" class="text-light">
                        <small>Login existing account</small>
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
            url: "{{ route('ajax.register') }}",
            method: 'post',
            data: {
                name: $('#name').val(),
                displayId: $('#displayId').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                passwordConfirmation: $('#passwordConfirmation').val(),
                phone: $('#phone').val(),
                DOB: $('#DOB').val(),
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