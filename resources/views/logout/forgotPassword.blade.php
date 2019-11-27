@extends('logout.layout.app', ['activePage' => 'logout.forgotPassword', 'title' => "The Desk"])

@section('content')
<div class="container" style="height: auto;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">
            <h1 class="text-white text-center">Welcome to Material Dashboard FREE Laravel Live Preview.</h1>
        </div>
        
        <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
            <form class="form" method="POST" action="#">
                @csrf
            
                <div class="card card-login card-hidden mb-3">
                    <div class="card-header card-header-primary text-center">
                        <h4 class="card-title"><strong>Forgot Password</strong></h4>
                    </div>
                    <div class="card-body">
                        
                        <p class="card-description text-center">Disabled</p>
    
                        <div class="bmd-form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="material-icons">email</i>
                                    </span>
                                </div>
                                <input type="email" name="email" class="form-control" placeholder="Email..." required>
                            </div>
                            @if ($errors->has('email'))
                            <div id="email-error" class="error text-danger pl-3" for="email" style="display: block;">
                                <strong>{{ $errors->first('email') }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer justify-content-center">
                        <button type="submit" class="btn btn-primary btn-link btn-lg">Send Password Reset Link</button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-6">
                    <a href="{{ route('logout.register') }}" class="text-light">
                        <small>Create new account</small>
                    </a>
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