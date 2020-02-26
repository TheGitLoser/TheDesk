@php
$selectedUser = json_decode($selectedUser, true);
$selectedUserUniqueId = [];
foreach ($selectedUser as $item) {
    array_push($selectedUserUniqueId, $item['unique_id']);
}
@endphp

@extends('login.layout.app', ['activePage' => '', 'title' => ''])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form method="post" id="form">
                    @csrf
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Create new channel</h4>
                            <p class="card-category"></p>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Channel name</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="text" id="name" placeholder="Channel name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label" for="input-password-confirmation">User to be added</label>
                                <div class="col-sm-7">
                                    @foreach ($selectedUser as $item)
                                        <div class="row" style="padding-top: 5px;">
                                            <div class="col">
                                                {{ $item['name'] }} @ {{ $item['display_id'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label" for="input-password-confirmation">description</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <textarea name="profile" id="description" rows="10" style="width:100%"></textarea>
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
            url: "{{ route('ajax.createChannel') }}",
            method: 'post',
            data: {
                name: $('#name').val(),
                description: $('#description').val(),
                selectedUser: {!! json_encode($selectedUserUniqueId) !!}
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