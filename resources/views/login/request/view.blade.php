@extends('login.layout.app', ['activePage' => 'request', 'title' => 'Request'])

@section('content')
<div class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">question_answer</i>
                        </div>
                        <p class="card-category">
                        </p>
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
                            Goto response
                            <i class="material-icons info-icon">reply</i>
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
    var newRequest = {!! $newRequest !!};
    
    function getGotoChatroomButton(uniqueId){
        newRequestButton = '{{ route('backend.request.response',['uniqueId'=> '']) }}/' + uniqueId;

        output = '<td class="td-actions text-right td-button">';
        output += '<a href="' + newRequestButton + '">';
        output += '<button type="button" title="New Request" class="btn btn-primary btn-link btn-sm">';
        output += '<i class="material-icons td-icon">reply</i>';
        output += '</button></a></td>';
        
        return output;
    }
    function outputList(newRequest){
        tempHtml = '<thead><tr><th>Requester</th><th>Title</th>';
        tempHtml += '<th>Details</th><th>Status</th><th>Goto</th></tr></thead>';
        tempHtml += '<tbody>';

        $.each(newRequest, function(i, item) {
            if(item.companyName){
                item.requesterName += " <small>@" + item.companyName + "</small>";
            }
            if(item.status == 2){
                status = "Waiting to response";
            }else if(item.status == 1){
                status = "Ongoing";
            }
            tempHtml += '<tr><td>' + item.requesterName + ' </td><td>' + item.title 
                        + ' </td><td>' + item.details + '</td><td>' + status + '</td>'
                        + getGotoChatroomButton(item.unique_id)
                        + '</tr>';
        });
        tempHtml += '</tbody>';
        $('#ajaxTable').html(tempHtml);
    }
    $(function() {
        outputList(newRequest);
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