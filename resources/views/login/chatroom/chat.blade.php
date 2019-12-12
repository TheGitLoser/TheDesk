@extends('login.layout.app', ['activePage' => 'contacts', 'title' => 'My contacts'])

@section('content')
<div class="content">
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
                        <div class="row message-item">
                            <div class="col-1">
                                <img src="https://placehold.it/50/FA6F57/fff&amp;text=LS" alt="LS"
                                    class="rounded-circle mx-auto d-block">
                            </div>
                            <div class="col-9">
                                {!! $chatroom !!}

                                {!! $chatroomUser !!}{!! $message !!}
                            </div>
                        </div>
                        <div class="row message-item">
                            <div class="col-2"></div>
                            <div class="col-9">
                                {!! $chatroom !!}{!! $chatroomUser !!}{!! $message !!}
                            </div>
                            <div class="col-1">
                                <img src="https://placehold.it/50/FA6F57/fff&amp;text=RS" alt="RS"
                                    class="rounded-circle mx-auto d-block">
                            </div>
                        </div>

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
        $('.message-body').height($(window).height() * 0.6);
        $( "footer" ).remove("footer");
    })
</script>
<script>
    var Server;
        chatroom = {!! $chatroom !!};
        var myUniqid = "@php echo getMyUniqid(); @endphp";
        var initSocket = {type: "initChatroom",
                            chatroomUniqid: chatroom['unique_id'],
                            myUniqid: myUniqid};
        var messageSend = {type: "sendMessage",
                            chatroomUniqid: chatroom['unique_id'],
                            myUniqid: myUniqid};

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
                    messageSend['message'] = $('#inputMessage').val();
    
                    send(JSON.stringify(messageSend));
                    $(this).removeAttr("disabled");
                }
            });

			// message received
			Server.bind('message', function( payload ) {
				console.log( payload );
                response = JSON.parse(payload);
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
    $(function() {
        var message = {!! $message !!};
        var outputHtml ='';
        $.each(message, function(i, item) {
            if(item['type'] == 'myMessage'){
                outputHtml += messageRight(item);
            }else if(item['type'] == 'sameType'){
                outputHtml += messageRight(item);
            }else{
                //oppositeType
                outputHtml += messageLeft(item);
            }
        });
        $('#message-body').html('');
        $('#message-body').append(outputHtml);

    });

</script>

@endpush