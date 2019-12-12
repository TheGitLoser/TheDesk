@extends('login.layout.app', ['activePage' => '', 'title' => $chatroomDetails['name'], 'currentChatroom' => $chatroomUniqid ])

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
<script src="http://server.localhost/PHPWebSocket-Chat/fancywebsocket.js"></script>
<script>
    // style 
    $(function(){
        $('.message-body').height($(window).height() * 0.65);
        $( "footer" ).remove("footer");
        $('a.navbar-brand').remove();
    })
</script>
<script>
    var Server;
        chatroomUniqid = '{{ $chatroomUniqid }}';

        chatroomUser = {!! $chatroomUser !!};
        var myUniqid = "@php echo getMyUniqid(); @endphp";
        var myUserType = "@php echo session('user.auth'); @endphp";
        var initSocket = {socketType: "initChatroom",
                            chatroomUniqid: chatroomUniqid,
                            myUniqid: myUniqid,
                            myUserType :myUserType};
        var currentChatroomUser = [];
        $.each(chatroomUser, function(i, item) {
            var temp = {};
            temp['unique_id'] = item['unique_id'];
            temp['type'] = item['type'];
            currentChatroomUser.push(temp);
        });
        var messageSend = {socketType: "sendMessage",
                            chatroomUniqid: chatroomUniqid,
                            myUniqid: myUniqid,
                            myUserType :myUserType,
                            currentChatroomUser: currentChatroomUser};

		function send( text ) {
			Server.send( 'message', text );
    console.log(text);
		}
        $(function(){
			console.log('Connecting...');
			Server = new FancyWebSocket('ws://127.0.0.1:9300');
			
			Server.bind('open', function() {
                // init message
                send(JSON.stringify(initSocket));
				console.log( "Connected." );
			});

            // send chatroom message
            $('#inputMessage').on('keypress', function (e) {
                if(e.which === 13){
                    $(this).attr("disabled", "disabled");
                    // call ajax first
                    messageSend['messageUniqid'] = 'tbc';
                    messageSend['messageCreateAt'] = 'tbc';
                    messageSend['message'] = $('#inputMessage').val();
    
                    send(JSON.stringify(messageSend));
                    $(this).removeAttr("disabled");
                }
            });

			// message received
			Server.bind('message', function( response ) {
				console.log( response );
                response = JSON.parse(response);
                if(response['socketType'] == "newChatroomMessage"){
                    outputMessage(response);
                }else{
                    console.log('noti only');
                }

			});

			// server lost
			Server.bind('close', function( data ) {
				console.log( "Disconnected." );
			});


			Server.connect();
        });
</script>
@endpush

@push('js')
<script>
    
    function messageLeft(tempMessage){
        var tempHtml = '';
        tempHtml += '<div class="row message-item">';
        tempHtml += '<div class="col-1" id="message:' + tempMessage["messageUniqid"] +'">';
        tempHtml += '<img src="https://placehold.it/50/FA6F57/fff&amp;text=LS" alt="LS" class="rounded-circle mx-auto d-block">';
        tempHtml += '</div><div class="col-9 message-left">';
        tempHtml += tempMessage['message'] + '<br><small class="message-time">'+ tempMessage['messageCreateAt']+'</small>';
        tempHtml += '</div></div>';
        return tempHtml;
        
    }
    function messageRight(tempMessage){
        var tempHtml = '';
        tempHtml += '<div class="row message-item">';
        tempHtml += '<div class="col-2"></div><div class="col-9 message-right" id="message:' + tempMessage["messageUniqid"] +'">';
        tempHtml += tempMessage['message'] + '<br><small class="message-time">'+ tempMessage['messageCreateAt']+'</small>';
        tempHtml += '</div><div class="col-1">';
        tempHtml += '<img src="https://placehold.it/50/FA6F57/fff&amp;text=RS" alt="RS" class="rounded-circle mx-auto d-block">';
        tempHtml += '</div></div>';
        return tempHtml;
    }
    function outputMessage(message){
        var outputHtml = '';
        if(message['messageType'] == 'myMessage'){
            outputHtml += messageRight(message);
        }else if(message['messageType'] == 'sameType'){
            outputHtml += messageRight(message);
        }else{
            //oppositeType
            outputHtml += messageLeft(message);
        }
        $('#message-body').append(outputHtml);
    }
    $(function() {
        var message = {!! $message !!};
        $('#message-body').html('');
        $.each(message, function(i, item) {
            outputMessage(item);
        });
    });

</script>

@endpush