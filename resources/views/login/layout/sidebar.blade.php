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
                        <i class="fas fa-users-cog"></i>
                        <p> Admin
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="collapse show" id="sidebarDropdownList">
                        <ul class="nav">
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
                            <li class="nav-item{{ $activePage == 'adminAddAdminUser' ? ' active' : '' }}">
                                <a class="nav-link" href="{{ route('login.admin.addAdminUser') }}">
                                    <span class="sidebar-mini"> + </span>
                                    <span class="sidebar-normal">Add admin user</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
            @if (userTypeAccess(['business admin']))
                <li class="nav-item {{ ($activePage == 'businessAdminDashboard' || $activePage == 'user-management') ? ' active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#sidebarDropdownList" aria-expanded="true">
                        <i class="fas fa-users-cog"></i>
                        <p> Business admin
                            <b class="caret"></b>
                        </p>
                    </a>
                    <div class="collapse show" id="sidebarDropdownList">
                        <ul class="nav">
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

            <li class="nav-item{{ $activePage == 'request' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('login.request.view') }}">
                    <i class="material-icons">question_answer</i>
                    <p>Request</p>
                </a>
            </li>
            <li class="nav-item{{ $activePage == 'discover' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('login.chatroom.discover') }}">
                    <i class="fas fa-user-tie"></i>
                    <p>Discover</p>
                </a>
            </li>
            <li class="nav-item{{ $activePage == 'contacts' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('login.chatroom.contacts') }}">
                    <i class="fas fa-address-book"></i>
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
                        <span class="sidebar-normal">Loading... </span>
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

            tempHtml += '<li class="nav-item ' + active +'">';
            tempHtml += '<a class="nav-link" href="' + link +'">';
            if(item.unseen){
                tempHtml += '<span class="sidebar-mini user-name-icon sidebar-noti"> ' + item.unseen +' </span>';
            }else{
                tempHtml += '<span class="sidebar-mini user-name-icon"> ' + item.initials +' </span>';
            }
            tempHtml += '<span class="sidebar-normal">' + item.name.substring(0, 23) +' </span>';
            if(item['typing'].length){
                temp = "";
                item['typing'].forEach(element => {
                    temp += element + ", ";
                });
                temp = temp.substr(0, temp.length -2);
                tempHtml += '<span class="sidebar-normal" style="padding-left: 45px;"><small>' + temp.substring(0, 23) +' is typing </small></span>';
            }
            tempHtml += '</a></li>';
        });
        $('#chatroom').html(tempHtml);
    }

    function getChatroomList(){
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('ajax.chatroom.getChatroomList') }}",
            method: 'post',
            success: function(response){
                chatroomList = response;
                outputChatroomList(chatroomList);
                pushNoti(chatroomList[0].name, "Start chatting with "+ chatroomList[0].name, getChatroomURL(response['chatroomUniqid']), false);
            }
        });
    }

    $(function() {
        currentChatroomName = '';
        @php
            if(isset($currentChatroom)){
                echo 'currentChatroomName = "' . $currentChatroom .'"';
            }
        @endphp
        
        outputChatroomList(chatroomList);
        
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
    });

</script>

@endpush