@extends('login.layout.app', ['activePage' => 'discover', 'title' => 'Discover new contacts'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <form class="form" id="form">
                    @csrf
                    <div class="card">
                        <div class="card-header card-header-tabs card-header-primary">
                            <div class="nav-tabs-navigation">
                                <div class="nav-tabs-wrapper">
                                    <span class="nav-tabs-title">Discover:</span>
                                    <ul class="nav nav-tabs" data-tabs="tabs">
                                        <li class="nav-item">
                                            <a class="nav-link" id="discover-indi" data-toggle="tab">
                                                <i class="material-icons">emoji_people</i> Individual
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="discover-business" data-toggle="tab">
                                                <i class="material-icons">business</i> Business
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="discover-colleague" data-toggle="tab">
                                                <i class="material-icons">business_center</i> Colleague
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="discover-extact" data-toggle="tab">
                                                <i class="material-icons">perm_identity</i> by extact name/id
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-body ">
                            @csrf
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label align-middle">name</label>
                                <div class="col-sm-4 form-group bmd-form-group">
                                    <input type="text" class="form-control" id="name" placeholder="name">
                                </div>
                                <label class="col-sm-2 col-form-label align-middle">id</label>
                                <div class="col-sm-4 form-group bmd-form-group">
                                    <input type="text" class="form-control" id="id" placeholder="id">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">content_copy</i>
                        </div>
                        <p class="card-category">
                        </p>
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive w-100 d-block d-md-table" id="ajaxTable">
                            <tbody>
                                <tr>
                                    <td>Loading...</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            Add to contact list
                            <i class="material-icons info-icon">playlist_add</i>

                            Start to chat
                            <i class="material-icons info-icon">chat</i>
                            
                            New request to business
                            <i class="material-icons info-icon">mail</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection


@push('js')
<script>
    var discoverList = {!! $output !!};
    var searchType = '{{ $searchType }}';
    
    function getTableButton(uniqueId){
        addContactButton = '{{ route('backend.chatroom.addContact',['uniqueId'=> '']) }}/' + uniqueId;
        startChatButton = '{{ route('backend.chatroom.startChat',['uniqueId'=> '']) }}/' + uniqueId;

        output = '<td class="td-actions text-right td-button">';
        output += '<a href="' + addContactButton + '">';
        output += '<button type="button" title="Add to contact list" class="btn btn-primary btn-link btn-sm">';
        output += '<i class="material-icons td-icon">playlist_add</i>';
        output += '</button></a></td>';

        output += '<td class="td-actions text-right td-button">';
        output += '<a href="' + startChatButton + '">';
        output += '<button type="button" title="Start to chat" class="btn btn-primary btn-link btn-sm">';
        output += '<i class="material-icons td-icon">chat</i>';
        output += '</button></a></td>';
        
        return output;
    }
    function getNewRequestButton(uniqueId){
        newRequestButton = '{{ route('login.request.new',['uniqueId'=> '']) }}/' + uniqueId;

        output = '<td class="td-actions text-right td-button">';
        output += '<a href="' + newRequestButton + '">';
        output += '<button type="button" title="New Request" class="btn btn-primary btn-link btn-sm">';
        output += '<i class="material-icons td-icon">mail</i>';
        output += '</button></a></td>';
        
        return output;
    }
    function outputList(discoverList){
        tempHtml = '';
        tempHtml = '<tbody>';

        if(searchType == 'business'){
            $.each(discoverList, function(i, item) {
                tempHtml += '<tr><td>' + item.name + ' </td>'
                            + getNewRequestButton(item.unique_id)
                            + '</tr>';
            });
        }else{
            $.each(discoverList, function(i, item) {
                tempHtml += '<tr><td>' + item.name + ' <small>@' + item.display_id + '</small></td>'
                            + getTableButton(item.unique_id)
                            + '</tr>';
            });
        }
        tempHtml += '</tbody>';
        $('#ajaxTable').html(tempHtml);
    }
    $(function() {
        outputList(discoverList);
        switch(searchType) {
            case 'indi':
                $('#discover-indi').addClass("active show");
                $('#discover-colleague').hide();
                break;
            case 'business':
                $('#discover-business').addClass("active show");
                $('#discover-colleague').hide();
                break;
            case 'colleague':
                $('#discover-colleague').addClass("active show");
                break;
            default:
                $('#discover-indi').addClass("active show");
        }
    });

</script>
@endpush

@push('js')
<script>
    $('#form').submit(function(e){
        if($("#discover-indi").hasClass("show")){
            searchType = 'indi';
        }else if($("#discover-business").hasClass("show")){
            searchType = 'business';
        }else if($("#discover-colleague").hasClass("show")){
            searchType = 'colleague';
        }else{
            searchType = 'extact';
        }
        e.preventDefault();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('ajax.discover') }}",
            method: 'post',
            data: {
                name: $('#name').val(),
                id: $('#id').val(),
                searchType: searchType
            },
            success: function(response){
                outputList(response['output']);
            }
        });
    });
</script>
@endpush