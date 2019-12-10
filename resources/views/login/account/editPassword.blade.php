@extends('login.layout.app', ['activePage' => '', 'title' => 'Change Password'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <form method="post" action="http://server.localhost/Material%20Dashboard/public/profile/password"
                    class="form-horizontal">
                    <input type="hidden" name="_token" value="OGIm6e74J813CAVjrqZzTZUxzrKZenT3k1OC7bjc"> <input
                        type="hidden" name="_method" value="put">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Change password</h4>
                            <p class="card-category">Password</p>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                <label class="col-sm-2 col-form-label" for="input-current-password">Current
                                    Password</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" input="" type="password" name="old_password"
                                            id="input-current-password" placeholder="Current Password" value=""
                                            required="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label" for="input-password">New Password</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" name="password" id="input-password" type="password"
                                            placeholder="New Password" value="" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label" for="input-password-confirmation">Confirm New
                                    Password</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" name="password_confirmation"
                                            id="input-password-confirmation" type="password"
                                            placeholder="Confirm New Password" value="" required="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-primary">Change password</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

</div>
</div>
@endsection