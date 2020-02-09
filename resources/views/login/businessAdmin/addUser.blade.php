@extends('login.layout.app', ['activePage' => 'businessAdminAddUser', 'title' => ''])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <form method="post" id="form" class="form-horizontal">
                    <input type="hidden" name="_token" value="OGIm6e74J813CAVjrqZzTZUxzrKZenT3k1OC7bjc"> <input
                        type="hidden" name="_method" value="put">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Create new account under your business plan</h4>
                            <p class="card-category"></p>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="text" id="name" placeholder="Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Display ID</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="text" id="displayId" placeholder="Display ID" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="email" id="email" placeholder="Email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="password" id="password" placeholder="Password" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mx-auto text-danger font-weight-bold" id="errorMsg"></div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-primary">Create</button>
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
            url: "{{ route('ajax.createBusinessUser') }}",
            method: 'post',
            data: {
                name: $('#name').val(),
                displayId: $('#displayId').val(),
                email: $('#email').val(),
                password: $('#password').val()
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