<div class="sidebar" data-color="orange" data-background-color="white"
    data-image="{{ asset('material') }}/img/sidebar-1.jpg">
    <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"
  
        Tip 2: you can also add an image using data-image tag
    -->
    <div class="logo">
        <a href="{{ route('login.home') }}" class="simple-text logo-normal">
            The Desk
        </a>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="nav-item{{ $activePage == 'discover' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('login.chatroom.discover') }}">
                    <i class="material-icons">dashboard</i>
                    <p>Discover</p>
                </a>
            </li>
            <li class="nav-item{{ $activePage == 'contacts' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('login.chatroom.contacts') }}">
                    <i class="material-icons">dashboard</i>
                    <p>Contacts</p>
                </a>
            </li>

            {{-- start chatroom --}}
            <li class="nav-item">
                <div class="sidebar-search">
                    <form class="form-inline" action="/action_page.php">
                        <input type="text" class="form-control sidebar-search-input" placeholder="Search...">
                        <button type="submit" class="btn btn-white btn-round btn-just-icon">
                            <i class="material-icons">search</i>
                            <div class="ripple-container"></div>
                        </button>
                    </form>
                </div>
            </li>
            <div id="chatroom">
                <li class="nav-item{{ $activePage == 'profile' ? ' active' : '' }}">
                    <a class="nav-link" href="">
                        <span class="sidebar-mini user-name-icon"> </span>
                        <span class="sidebar-normal">Loding... </span>
                    </a>
                </li>
            </div>
        </ul>
    </div>
</div>

@push('js')
<script>
    var chatroomList = {!! App\Http\Controllers\ChatroomController::getChatroomList() !!};
    $(function() {
        var currentChatroomName;
        @php
            if(isset($currentChatroom)){
                echo 'currentChatroomName = "' . $currentChatroom .'"';
            }
        @endphp
        
        var tempHtml = "";
        $.each(chatroomList, function(i, item) {
            var link = '{{ route('login.chatroom.chat',['unique_id'=> '']) }}/' + item.unique_id;
            if(currentChatroomName == item.unique_id){
                active = 'active';
            }else{
                active = '';
            }
            var initials = item.initials;
            var chatroomName = item.name;

            tempHtml += '<li class="nav-item ' + active +'">';
            tempHtml += '<a class="nav-link" href="' + link +'">';
            tempHtml += '<span class="sidebar-mini user-name-icon"> ' + initials +' </span>';
            tempHtml += '<span class="sidebar-normal">' + chatroomName +' </span>';
            tempHtml += '</a></li>';
        });
        $('#chatroom').html(tempHtml);
    });
    // search chatroom
    $('#sdf').on('keypress', function (e) {
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

</script>

@endpush