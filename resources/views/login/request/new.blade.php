@php
    $business = json_decode($requestBusiness, true);
@endphp

@extends('login.layout.app', ['activePage' => 'Discover', 'title' => 'Submit new request'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form method="post" id="form" class="form-horizontal">
                    @csrf
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Request</h4>
                            <p class="card-category"></p>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Request to</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        {{ $business['name'] }}
                                    </div>
                                </div>
                            </div>
                            @if (session("user.info.businessPlanId") != $business['id'])
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">{{ $business['name'] }}'s profile</label>
                                    <div class="col-sm-7">
                                        <div class="form-group bmd-form-group">
                                        {{ $business['profile'] }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Request title</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <input class="form-control" type="text" id="title" placeholder="Title" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Request details</label>
                                <div class="col-sm-7">
                                    <div class="form-group bmd-form-group">
                                        <textarea name="profile" id="details" rows="10" style="width:100%" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mx-auto text-danger font-weight-bold" id="errorMsg"></div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-primary">Submit request</button>
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
            url: "{{ route('ajax.newRequest') }}",
            method: 'post',
            data: {
                unique_id: "{{ $business['unique_id'] }}",
                title: $('#title').val(),
                details: $('#details').val()
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