@extends('login.layout.app', ['activePage' => 'home', 'title' => 'Home'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            @if (userTypeAccess(['admin']))
                <div class="col-lg col-md-6 col-sm-6">
                    <a href="{{ route('login.admin.viewBusinessPlan') }}">
                        <div class="card card-stats">
                            <div class="card-header card-header-warning card-header-icon">
                                <div class="card-icon">
                                    <i class="material-icons">business_center</i>
                                </div>
                                <p class="card-category"></p>
                                <h3 class="card-title">Manage business plan</h3>
                            </div>
                            <div class="card-footer">
                                <div class="stats">
                                    View the existing business plan
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if (userTypeAccess(['business admin']))
                <div class="col-lg col-md-6 col-sm-6">
                    <a href="{{ route('login.businessAdmin.viewUser') }}">
                        <div class="card card-stats">
                            <div class="card-header card-header-warning card-header-icon">
                                <div class="card-icon">
                                    <i class="material-icons">store</i>
                                </div>
                                <p class="card-category"></p>
                                <h3 class="card-title">Manage your business plan</h3>
                            </div>
                            <div class="card-footer">
                                <div class="stats">
                                    View the business account under your business plan
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            <div class="col-lg col-md-6 col-sm-6">
                <a href="{{ route('login.request.view') }}">
                    <div class="card card-stats">
                        <div class="card-header card-header-success card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">question_answer</i>
                            </div>
                            @if ($newRequest == 0 )
                                <h3 class="card-title">Request</h3>
                            @else
                                <p class="card-category">Unreplied Request </p>
                                <h3 class="card-title">{{ $newRequest }}</h3>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                Inquiries message from others
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg col-md-6 col-sm-6">
                <a href="{{ route('login.chatroom.discover') }}">
                    <div class="card card-stats">
                        <div class="card-header card-header-danger card-header-icon">
                            <div class="card-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <p class="card-category"></p>
                            <h3 class="card-title">Discover</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                Discover new contacts
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg col-md-6 col-sm-6">
                <a href="{{ route('login.chatroom.contacts') }}">
                    <div class="card card-stats">
                        <div class="card-header card-header-primary card-header-icon">
                            <div class="card-icon">
                                <i class="fas fa-address-book"></i>
                            </div>
                            <p class="card-category"></p>
                            <h3 class="card-title">Contacts</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                View your bookmarked user contacts
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header card-header-info">
                        <h4 class="card-title">Your unseen message</h4>
                        <p class="card-category">Click to view the chatroom</p>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table w-100 d-md-table">
                            <thead>
                                <th style="width:30%">From</th>
                                <th>Message</th>
                                <th>Open</th>
                            </thead>
                            <tbody id="ajaxTable">
                                <tr>
                                    <td>Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<a href="http://"></a>
@endsection

@push('js')
{{-- push to end script --}}
<script>
    function getChatroomLinkButton(uniqid){
        link = '{{ route('login.chatroom.chat',['unique_id'=> '']) }}/' + uniqid;

        output = '<td class="td-actions text-right td-button">';
        output += '<a href="' + link + '">';
        output += '<button type="button" title="Open Chatroom" class="btn btn-primary btn-link btn-sm">';
        output += '<i class="material-icons td-icon">chat</i>';
        output += '</button></a></td>';
        
        return output;
    }
    function outputList(unseenMessage){
        tempHtml = '';
        
        $.each(unseenMessage, function(i, item){
            if(item.chatroomType == "DM"){
                tempName = item.senderName;
            }else{
                tempName = item.senderName + " (" + item.chatroomName + ") ";
            }

            tempHtml += '<tr><td>' + tempName + ' </td>'
                        + ' <td>' + item.message + '</td>'
                        + getChatroomLinkButton(item.chatroomUniqid)
                        + '</tr>';
        });

        $('#ajaxTable').html(tempHtml);
    }
    $(function(){
        outputList(unseenMessage);
    });
</script>
@endpush