<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <a class="navbar-brand" href="/" id="pageTitle">{{ $title }}</a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end">
            <form class="navbar-form">
            </form>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login.home') }}">
                        <i class="material-icons">dashboard</i>
                        <p class="d-lg-none d-md-block">
                            Home
                        </p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="" id="navbarDropdownMenuLink" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="material-icons">notifications</i>
                        <span class="notification" id="notificationCount"></span>
                        <p class="d-lg-none d-md-block">
                            new message
                        </p>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right unseenMsgNotification" aria-labelledby="navbarDropdownMenuLink" id="notification">
                        {{-- <a class="dropdown-item" href="#">{{ __('Another One') }}</a> --}}
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#pablo" id="navbarDropdownProfile" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="material-icons">person</i>
                        <p class="d-lg-none d-md-block">
                            Account
                        </p>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                        <div class="dropdown-item">Hi, @php echo session("user.info.name"); @endphp</div>
                        <a class="dropdown-item" href="{{ route('login.account.profile') }}"> Profile</a>
                        <a class="dropdown-item" href="{{ route('login.account.editPassword') }}"> Change Password</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('backend.logout') }}"> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

@push('js')
<script>
    var unseenMessage = {!! App\Http\Controllers\ChatroomController::getUnseenMessage() !!};
    function outputNotification(unseenMessage){
        tempHtml = "";
        $.each(unseenMessage, function(i, item) {
            var link = '{{ route('login.chatroom.chat',['unique_id'=> '']) }}/' + item.chatroomUniqid;
            if(item.chatroomType == "DM" ){
                tempMessage = item.senderName + ':  ' + item.message;
            }else{
                tempMessage = item.senderName + ' (' + item.chatroomName + '):<br>' + item.message;
            }
            tempHtml += '<a class="dropdown-item" href="' + link +'">' + tempMessage + "</a>";
        });
        $('#notification').html(tempHtml);

        if(unseenMessage.length){
            $('#notificationCount').show();
            $('#notificationCount').html(unseenMessage.length);
        }else{
            $('#notificationCount').hide();
        }

    }

    $(function(){
        outputNotification(unseenMessage);
    });
</script>
@endpush