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
                    <form id="form">
                    @csrf
                    <div class="card-body message-body" id="message-body">
                        <table class="table table-responsive w-100 d-block d-md-table">
                            <tr><td>name</td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="name" value="{{$chatroomDetails['name']}}" required>
                                    </div>
                                </td>
                            </tr>
                            <tr><td>Chat room type</td><td>{{ $chatroomDetails['type'] }}</td></tr>
                            <tr><td>Create at</td><td>{{ $chatroomDetails['create_at'] }}</td></tr>
                            <tr><td>Last update at</td><td>{{ $chatroomDetails['update_at'] }}</td></tr>
                            <tr><td>Description</td>
                                <td>
                                    <div class="form-group">
                                        <textarea id="description" rows="10" style="width:90%">{{$chatroomDetails['description']}}</textarea>
                                    </div>
                                </td>
                            </tr>
                            <tr><td>User side</td>
                                <td>
                                    <div class="form-group bmd-form-group">
                                        @if ($chatroomDetails['type'] == "Group")
                                            <div class="row">
                                                <div class="col">
                                                    <h5 class="card-title">
                                                    Switch to Channel in order to select participant's side
                                                    </h5>
                                                </div>
                                            </div>
                                        @else
                                            <div class="row">
                                                <div class="col">
                                                    <h5 class="card-title">
                                                    Select the side of participant shown in chat room
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="row" style="padding-top: 5px;">
                                                <div class="col-2">
                                                    <h5>
                                                        Opposite side
                                                    </h5>
                                                </div>
                                                <div class="col-8">
                                                </div>
                                                <div class="col-2">
                                                    <h5>
                                                        My side
                                                    </h5>
                                                </div>
                                            </div>
                                            @foreach ($chatroomUserDetails as $item)
                                                <div class="form-group bmd-form-group">
                                                    <div class="row" style="padding-top: 5px;">
                                                        <div class="col-2">
                                                            @if ($item['side'] != $mySide)
                                                                <input class="form-control" type="radio" name="{{$item['unique_id']}}" value="0" required checked>
                                                            @else
                                                                <input class="form-control" type="radio" name="{{$item['unique_id']}}" value="0" required>
                                                            @endif
                                                        </div>
                                                        <div class="col-8">
                                                            {{ $item['name'] }} @ {{ $item['display_id'] }}
                                                        </div>
                                                        <div class="col-2">
                                                            @if ($item['side'] == $mySide)
                                                                <input class="form-control" type="radio" name="{{$item['unique_id']}}" value="1" required checked>
                                                            @else
                                                                <input class="form-control" type="radio" name="{{$item['unique_id']}}" value="1" required>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

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
                            <a href="{{route('login.chatroom.channelAddUser', ['unique_id'=>$chatroomDetails['unique_id']])}}" style="color: #fafafa;">
                                <button type="button" class="btn btn-info">Add User</button>
                            </a>
                            <a href="{{route('backend.chatroom.switchType', ['unique_id'=>$chatroomDetails['unique_id']])}}" style="color: #fafafa;">
                                @if ($chatroomDetails['type'] == "Channel")
                                    <button type="button" class="btn btn-secondary">Switch to Group</button>
                                @else
                                    <button type="button" class="btn btn-secondary">Switch to Channel</button>
                                @endif
                            </a>
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
                        <h4 class="card-title">{{ $userDetails['name'] }}</h4>
                        <p class="card-category"></p>
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
        userSide = {};
        @foreach ($chatroomUserDetails as $item)
            userSide['{{$item['unique_id']}}'] = $('input[name={{$item["unique_id"]}}]:checked').val();
        @endforeach

        e.preventDefault();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('ajax.chatroom.setting', ['mode'=>'channel']) }}",
            method: 'post',
            data: {
                uniqid: "{{ $chatroomDetails['unique_id'] }}",
                name: $('#name').val(),
                description: $('#description').val(),
                userSide: userSide
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