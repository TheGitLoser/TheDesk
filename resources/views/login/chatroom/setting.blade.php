@php
$chatroomDetails = json_decode($chatroom, true);
$chatroomUserDetails = json_decode($chatroomUser, true);

@endphp

@extends('login.layout.app', ['activePage' => '', 'title' => $chatroomDetails['name'], 'currentChatroom' => $chatroomDetails['unique_id'] ])

@section('content')
<div class="content" style="margin-top: 30px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card card-stats">
                    <div class="card-header card-header-primary card-header-icon">
                        <div class="card-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="card-category chatroom-info-left">

                            <h4>{{ $chatroomDetails['name'] }}</h4>
                            @foreach ($chatroomUserDetails as $userDetails)
                            {{ $userDetails['name'] }},
                            @endforeach
                        </div>
                        <div class="card-category chatroom-info-right"></div>

                        <div class="card-category" style="height: 0;">
                            {{ $chatroomDetails['description'] }}
                        </div>
                    </div>
                    <form class="form" id="form">
                    <div class="card-body message-body" id="message-body">
                        <table class="table table-responsive w-100 d-block d-md-table">
                            <tr><td>Chat room type</td><td>Direct message</td></tr>
                            <tr><td>Create at</td><td>{{ $chatroomDetails['create_at'] }}</td></tr>
                            <tr><td>Last update at</td><td>{{ $chatroomDetails['update_at'] }}</td></tr>
                            <tr><td>Description</td>
                                <td>
                                    @csrf
                                    <div class="form-group">
                                        <textarea id="description" rows="10" style="width:90%">{{$chatroomDetails['description']}}</textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer row">
                        <div class="col-12"></div>
                        <div class="text-center mx-auto text-danger font-weight-bold" id="errorMsg"></div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">

            @foreach ($chatroomUserDetails as $userDetails)

            <div class="col">
                <div class="card">
                    @if ($userDetails['currentUser'] == true)
                        <div class="card-header card-header-success">
                    @else
                        <div class="card-header card-header-info">
                    @endif
                        <div class="row">
                            <div class="col-10">
                                <h4 class="card-title">{{ $userDetails['name'] }}</h4>
                            </div>
                            <div class="col-2">
                                @if ($userDetails['contact'] == false && $userDetails['currentUser'] == false)
                                    <a href="{{ route('backend.chatroom.addContact',['uniqueId'=> $userDetails['unique_id']]) }}">
                                    <button type="button" title="Add to contact list" class="btn btn-primary btn-link btn-sm" style="padding:0px;">
                                    <i class="material-icons td-icon">playlist_add</i>
                                    </button></a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body ">
                        <table class="table table-responsive w-100 d-block d-md-table">
                            <tbody>
                                <tr><td>Name</td><td>{{ $userDetails['name'] }}</td></tr>
                                <tr><td>Display ID</td><td>{{ $userDetails['display_id'] }}</td></tr>
                                <tr><td>Email</td><td>{{ $userDetails['email'] }}</td></tr>
                                <tr><td>Phone</td><td>{{ $userDetails['phone'] }}</td></tr>
                                <tr><td>Date of Birth</td><td>{{ date_format(date_create($userDetails['DOB']),"Y-m-d") }}</td></tr>
                                <tr><td>Profile</td><td>{{ $userDetails['profile'] }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @endforeach
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
    $("#pageTitle").hide();
</script>
@endpush

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
            url: "{{ route('ajax.chatroom.setting', ['mode'=>'direct']) }}",
            method: 'post',
            data: {
                uniqid: "{{ $chatroomDetails['unique_id'] }}",
                description: $('#description').val()
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