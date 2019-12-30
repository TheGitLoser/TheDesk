@extends('login.layout.app', ['activePage' => '', 'title' => $chatroomDetails['name'], 'currentChatroom' =>
$chatroomUniqid ])

@section('content')
<div class="content" style="margin-top: 30px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card card-stats">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">content_copy</i>
                        </div>
                        <div class="card-category chatroom-info-left">
                            @php
                            $chatroomDetails = json_decode($chatroom, true);
                            $chatroomDetails['description'] = substr($chatroomDetails['description'],30);
                            $chatroomUserDetails = json_decode($chatroomUser, true);
                            @endphp
                            <h4>{{ $chatroomDetails['name'] }}</h4>
                            @foreach ($chatroomUserDetails as $userDetails)
                            {{ $userDetails['name'] }},
                            @endforeach
                        </div>
                        <div class="card-category" style="height: 0;">
                            {{ $chatroomDetails['description'] }}
                        </div>
                    </div>
                    <div class="card-body message-body" id="message-body">
                        Loading...

                    </div>
                    <div class="card-footer row">
                        <div class="col-12">
                            {{-- <form class="form col-12" id="message"> --}}
                            @csrf
                            <div class="form-group">
                                <input type="text" class="form-control col-10" id="inputMessage"
                                    placeholder="message...">
                            </div>
                            {{-- </form> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
{{-- socket --}}
<script>
    chatroomUniqid = '{{ $chatroomUniqid }}';

    chatroomUser = {!! $chatroomUser !!};
    var myUniqid = "@php echo getMyUniqid(); @endphp";
    var myUserType = "@php echo session('user.auth'); @endphp";
    var initSocket = {socketType: "initChatroom",
                        chatroomUniqid: chatroomUniqid,
                        myUniqid: myUniqid,
                        myUserType :myUserType};
    // for socket
    var currentChatroomUser = [];
    var responseFromDB;
    // for html output
    var participateInfo = [];
    $.each(chatroomUser, function(i, item) {
        // for socket
        var temp = {};
        temp['unique_id'] = item['unique_id'];
        temp['type'] = item['type'];
        currentChatroomUser.push(temp);
        // for html output
        participateInfo[item['unique_id']] = item;
    });
    var messageSend = {socketType: "sendMessage",
                        chatroomUniqid: chatroomUniqid,
                        myUniqid: myUniqid,
                        myUserType :myUserType,
                        currentChatroomUser: currentChatroomUser};

    $(function(){
        var socketUrl = '{{ $wsConnection }}';
        var Socket = new WebSocket(socketUrl);
        console.log(Socket.readyState);

        Socket.onopen = function(event){
            Socket.send(JSON.stringify(initSocket));
        }

        // send chatroom message
        $('#inputMessage').on('keypress', function (e) {
            if(e.which === 13){
                $(this).attr("disabled", "disabled");
                message = $('#inputMessage').val()
                messageSend['message'] = message;
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('login.chatroom.newMessage') }}",
                    method: 'post',
                    data: {
                        chatroomUniqid: chatroomUniqid, 
                        message: message
                    },
                    success: function(response){
                        responseFromDB =  response['output'];
                        messageSend['messageUniqid'] = responseFromDB['messageUniqid'];
                        messageSend['messageCreateAt'] = responseFromDB['messageCreateAt'];
                        Socket.send(JSON.stringify(messageSend));
                        console.log(messageSend);
                    }
                });
                $(this).val('');
                $(this).removeAttr("disabled");
            }
        });

        // when message comes form server
        Socket.onmessage = function(event){
            response = JSON.parse(event['data']);
            if(response['socketType'] == "newChatroomMessage"){
                outputMessage(response);
            }else{
console.log('noti only');
            }
            console.log();
        }

        Socket.onclose = function(event){
            console.log("Socket is closed now (onclose())");
        }
    });

</script>

{{-- output message to html --}}
<script>
    function messageLeft(tempMessage){
        var name = participateInfo[tempMessage['userUniqid']]['name'];
        var profilePic = participateInfo[tempMessage['userUniqid']]['profile_picture'];
        if(profilePic == null){
            var initials = participateInfo[tempMessage['userUniqid']]['initials'];
            profilePic = '<img src="https://placehold.it/50/FA6F57/fff&amp;text=' + initials + '" alt="' + initials + '" class="rounded-circle mx-auto d-block">'
        }

        var tempHtml = '';
        tempHtml += '<div class="row message-item">';
        tempHtml += '<div class="col-1" id="message:' + tempMessage["messageUniqid"] +'">';
        tempHtml += profilePic;
        tempHtml += '</div><div class="col-9 message-left">';
        tempHtml += tempMessage['message'] + '<br><small class="message-time">'+ name + ' @'+ tempMessage['messageCreateAt'] + '</small>';
        tempHtml += '</div></div>';
        return tempHtml;
        
    }
    function messageRight(tempMessage){
        var name = participateInfo[tempMessage['userUniqid']]['name'];
        var profilePic = participateInfo[tempMessage['userUniqid']]['profile_picture'];
        if(profilePic == null){
            var initials = participateInfo[tempMessage['userUniqid']]['initials'];
            profilePic = '<img src="https://placehold.it/50/FA6F57/fff&amp;text=' + initials + '" alt="' + initials + '" class="rounded-circle mx-auto d-block">'
        }

        var tempHtml = '';
        tempHtml += '<div class="row message-item">';
        tempHtml += '<div class="col-2"></div><div class="col-9 message-right" id="message:' + tempMessage["messageUniqid"] +'">';
        tempHtml += tempMessage['message'] + '<br><small class="message-time">'+ name + ' @'+ tempMessage['messageCreateAt'] + '</small>';
        tempHtml += '</div><div class="col-1">';
        tempHtml += profilePic;
        tempHtml += '</div></div>';
        return tempHtml;
    }
    function outputMessage(message){
        var outputHtml = '';
        if(chatroomUser.length == 2){
            if(message['userUniqid'] == myUniqid){
                outputHtml += messageRight(message);    
            }
            else{
                outputHtml += messageLeft(message);
            }
        }else{
            if(message['messageType'] == 'myMessage'){
                outputHtml += messageRight(message);
            }else if(message['messageType'] == 'sameType'){
                outputHtml += messageRight(message);
            }else{
                //oppositeType
                outputHtml += messageLeft(message);
            }
        }
        $('#message-body').append(outputHtml);
        $("#message-body").animate({ scrollTop: $("#message-body")[0].scrollHeight}, 1000);
    }
    $(function() {
        // style adjust
        $('.message-body').height($(window).height() * 0.65);
        $( "footer" ).remove("footer");
        $('a.navbar-brand').remove();

        message = {!! $message !!};
        $('#message-body').html('');
        $.each(message, function(i, item) {
            outputMessage(item);
        });
        $("#message-body").animate({ scrollTop: $("#message-body")[0].scrollHeight}, 1000);
    });

</script>

@endpush