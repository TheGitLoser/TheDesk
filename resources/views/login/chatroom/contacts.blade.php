@extends('login.layout.app', ['activePage' => 'contacts', 'title' => 'My contacts'])

@section('content')
<div class="content">
    <div class="container-fluid">
        {{-- <div class="row">
            <div class="col-md-12">

                <form class="form" id="form" method="GET">
                    <div class="card">

                        <div class="card-header card-header-tabs card-header-primary">
                            <div class="nav-tabs-navigation">
                                <div class="nav-tabs-wrapper">
                                    <span class="nav-tabs-title">Search:</span>
                                    <ul class="nav nav-tabs" data-tabs="tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active show" id="discover-indi" href="#profile" data-toggle="tab">
                                                <i class="material-icons">emoji_people</i> Individual 
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="discover-business" href="#messages" data-toggle="tab">
                                                <i class="material-icons">business</i> Business
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

        </div> --}}

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
                        <table class="table" id="ajaxTable">
                            <tbody>
                                <tr>
                                    <td>Loading...</td>
                                    <td class="td-actions text-right td-button">
                                        <button type="button" rel="tooltip" title="Hide"
                                            class="btn btn-primary btn-link btn-sm">
                                            <i class="far fa-minus-square td-icon"></i>
                                        </button>
                                    </td>
                                    <td class="td-actions text-right td-button">
                                        <button type="button" rel="tooltip" title="Start to chat"
                                            class="btn btn-primary btn-link btn-sm">
                                            <i class="material-icons td-icon">chat</i>
                                        </button>
                                    </td>
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
            </div>
        </div>

    </div>
</div>
@endsection
 

@push('js')
<script>
    var discoverList = {!! $output !!};
    $(function() {
        function getTableButton(uniqueId){
            addContactButton = '{{ route('login.chatroom.hideContact',['uniqueId'=> '']) }}/' + uniqueId;
            startChatButton = '{{ route('login.chatroom.startChat',['uniqueId'=> '']) }}/' + uniqueId;

            output = '<td class="td-actions text-right td-button">';
            output += '<a href="' + addContactButton + '">';
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

        var tempHtml = '<tbody>';
        $.each(discoverList, function(i, item) {
            tempHtml += '<tr><td>' + item.name + ' <small>@' + item.unique_id + '</small><td>'
                        + getTableButton(item.unique_id)
                        + '</tr>';
        });
        tempHtml += '</tbody>';
         $('#ajaxTable').html(tempHtml);
    });

</script>
    
@endpush