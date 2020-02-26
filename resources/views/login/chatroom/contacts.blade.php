@extends('login.layout.app', ['activePage' => 'contacts', 'title' => 'My contacts'])

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
                                    <span class="nav-tabs-title">Search:</span>
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
                <form action="{{ route('login.chatroom.createChannel') }}" method="POST">
                    @csrf
                    <div class="card card-stats">
                        <div class="card-header card-header-warning card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">content_copy</i>
                            </div>
                            <div class="card-category" style="height: 0;">
                                <button type="submit" class="btn btn-primary">Create channel</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table" id="ajaxTable">
                                <tbody>
                                    <tr>
                                        <td>Loading...</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                Hide
                                <i class="far fa-minus-square info-icon"></i>

                                Start to chat
                                <i class="material-icons info-icon">chat</i>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
 

@push('js')
<script>
    var contactList = {!! $output !!};
    var searchType = '{{ $searchType }}';
    
    function getTableButton(uniqueId){
        hideContactButton = '{{ route('login.chatroom.hideContact',['uniqueId'=> '']) }}/' + uniqueId;
        startChatButton = '{{ route('login.chatroom.startChat',['uniqueId'=> '']) }}/' + uniqueId;

        output = '<td class="td-actions text-right td-button">';
        output += '<a href="' + hideContactButton + '">';
        output += '<button type="button" title="Hide" class="btn btn-primary btn-link btn-sm">';
        output += '<i class="far fa-minus-square td-icon"></i>';
        output += '</button></a></td>';

        output += '<td class="td-actions text-right td-button">';
        output += '<a href="' + startChatButton + '">';
        output += '<button type="button" title="Start to chat" class="btn btn-primary btn-link btn-sm">';
        output += '<i class="material-icons td-icon">chat</i>';
        output += '</button></a></td>';
        
        return output;
    }
    function getCheckBox(uniqueId){
        output = '<div class="form-check">';
        output += '<label class="form-check-label">';
        output += '<input class="form-check-input" type="checkbox" name="' + uniqueId + '">';
        output += '<span class="form-check-sign">';
        output += '<span class="check"></span>';
        output += '</span>';
        output += '</label>';
        output += '</div>';
        return output;
    }
    function outputList(contactList){
        tempHtml = '';
        tempHtml = '<tbody>';
        $.each(contactList, function(i, item) {
        tempHtml += '<tr><td>' + getCheckBox(item.unique_id) + '</td><td>' + item.name + ' <small>@' + item.display_id + '</small><td>'
                    + getTableButton(item.unique_id)
                    + '</tr>';
        });
        tempHtml += '</tbody>';
        $('#ajaxTable').html(tempHtml);
    }

    $(function() {
        outputList(contactList);
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
        }else{
            searchType = 'colleague';
        }
        e.preventDefault();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('ajax.searchContact') }}",
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