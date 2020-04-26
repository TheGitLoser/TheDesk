@extends('login.layout.app', ['activePage' => '', 'title' => 'My Profile'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form method="post" id="form" class="form-horizontal">
                    @csrf
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">My Profile</h4>
                            <p class="card-category"></p>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="text" id="name" placeholder="name" value="{{$profile['name']}}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Display ID</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="text" id="displayId" placeholder="Display ID" value="{{$profile['display_id']}}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Phone</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="tel" id="phone" value="{{$profile['phone']}}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Date of Birth</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="date" id="DOB" value="{{$profile['DOB']}}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Profile</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <textarea name="profile" id="profile" cols="50" rows="10">{{$profile['profile']}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mx-auto text-danger font-weight-bold" id="errorMsg"></div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-primary">Update profile</button>
                        </div>
                    </div>
                </form>
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
            url: "{{ route('ajax.updateProfile') }}",
            method: 'post',
            data: {
                name: $('#name').val(),
                displayId: $('#displayId').val(),
                phone: $('#phone').val(),
                DOB: $('#DOB').val(),
                profile: $('#profile').val()
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