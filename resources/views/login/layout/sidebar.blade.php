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
            @if (userTypeAccess(['admin']))
                <li class="nav-item {{ ($activePage == 'businessAdminDashboard' || $activePage == 'user-management') ? ' active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#sidebarDropdownList" aria-expanded="true">
                        <i><img style="width:25px" src="{{ asset('material') }}/img/laravel.svg"></i>
                        <p> Admin
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="collapse show" id="sidebarDropdownList">
                        <ul class="nav">
                            {{-- <li class="nav-item{{ $activePage == 'adminDashboard' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('login.admin.dashboard') }}">
                                    <span class="sidebar-mini"> D </span>
                                    <span class="sidebar-normal">Dashboard</span>
                                </a>
                            </li> --}}
                            <li class="nav-item{{ $activePage == 'adminViewBusinessPlan' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('login.admin.viewBusinessPlan') }}">
                                    <span class="sidebar-mini"> B </span>
                                    <span class="sidebar-normal">Business list</span>
                                </a>
                            </li>
                            <li class="nav-item{{ $activePage == 'adminCreateBusinessPlan' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('login.admin.createBusinessPlan') }}">
                                    <span class="sidebar-mini"> C </span>
                                    <span class="sidebar-normal">Create business plan</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
            @if (userTypeAccess(['business admin']))
                <li class="nav-item {{ ($activePage == 'businessAdminDashboard' || $activePage == 'user-management') ? ' active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#sidebarDropdownList" aria-expanded="true">
                        <i><img style="width:25px" src="{{ asset('material') }}/img/laravel.svg"></i>
                        <p> Business admin
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="collapse show" id="sidebarDropdownList">
                        <ul class="nav">
                            {{-- <li class="nav-item{{ $activePage == 'businessAdminDashboard' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('login.businessAdmin.dashboard') }}">
                                    <span class="sidebar-mini"> D </span>
                                    <span class="sidebar-normal">Dashboard</span>
                                </a>
                            </li> --}}
                            <li class="nav-item{{ $activePage == 'businessAdminViewUser' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('login.businessAdmin.viewUser') }}">
                                    <span class="sidebar-mini"> ~ </span>
                                    <span class="sidebar-normal">User list</span>
                                </a>
                            </li>
                            <li class="nav-item{{ $activePage == 'businessAdminAddUser' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('login.businessAdmin.addUser') }}">
                                    <span class="sidebar-mini"> + </span>
                                    <span class="sidebar-normal">Add user</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            <li class="nav-item{{ $activePage == 'businessUserHome' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('login.home') }}">
                    <i class="material-icons">dashboard</i>
                    <p>Request</p>
                </a>
            </li>
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
                    <form id="searchChatroomListForm" class="form-inline">
                        <input type="text" class="form-control sidebar-search-input" id="searchChatroomList"
                            placeholder="Search...">
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
    
    function outputChatroomList(chatroomList){
        tempHtml = "";
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
    }
    $(function() {
        currentChatroomName = '';
        @php
            if(isset($currentChatroom)){
                echo 'currentChatroomName = "' . $currentChatroom .'"';
            }
        @endphp
        
        outputChatroomList(chatroomList);
    });
    // search chatroom
    $("#searchChatroomList").on("keyup", function() {
        var value = $(this).val();
        tempChatroomList = [];
        $.each(chatroomList, function(i, item) {
            if(item.name.toLowerCase().includes(value.toLowerCase())){
                tempChatroomList.push(item);
            }
        });
        outputChatroomList(tempChatroomList);
    });
    $('#searchChatroomListForm').submit(function(e){
        console.log($('#searchChatroomList').val());

    });

</script>

@endpush