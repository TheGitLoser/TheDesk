@extends('login.layout.app', ['activePage' => '', 'title' => ''])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <form method="post" id="form" class="form-horizontal">
                    @csrf
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
                                        <input class="form-control" type="password" id="currentPassword" placeholder="Current Password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label" for="input-password">New Password</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="password" id="newPassword" placeholder="New Password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label" for="input-password-confirmation">Confirm New
                                    Password</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="password" id="passwordConfirmation" placeholder="Confirm New Password" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mx-auto text-danger font-weight-bold" id="errorMsg"></div>
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
            url: "{{ route('ajax.editPassword') }}",
            method: 'post',
            data: {
                currentPassword: $('#currentPassword').val(),
                newPassword: $('#newPassword').val(),
                passwordConfirmation: $('#passwordConfirmation').val()
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