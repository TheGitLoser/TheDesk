@extends('login.layout.app', ['activePage' => 'businessAdminViewUser', 'title' => 'Business account under your business'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <form class="form" id="form">
                    <div class="card">
                        <div class="card-header card-header-tabs card-header-primary">
                            <div class="nav-tabs-navigation">
                                <div class="nav-tabs-wrapper">
                                    <span class="nav-tabs-title">User under your business plan:</span>
                                    <ul class="nav nav-tabs" data-tabs="tabs">
                                        <li class="nav-item">
                                            
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
                        <table class="table w-100 d-md-table" id="ajaxTable">
                            <tbody>
                                <tr>
                                    <td>Loading...</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            Remove user account
                            <i class="far fa-trash-alt"></i>
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
    
    function getTableButton(uniqueId){
        removeButton = '{{ route('login.businessAdmin.removeBusinessPlanUser',['uniqueId'=> '']) }}/' + uniqueId;

        output = '<td class="td-actions text-right td-button">';
        output += '<a href="' + removeButton + '">';
        output += '<i class="far fa-trash-alt"></i>';
        output += '</a></td>';

        return output;
    }
    function outputList(discoverList){
        tempHtml = '';
        tempHtml = '<tbody>';
        $.each(discoverList, function(i, item) {
            tempHtml += '<tr><td>' + item.name + ' <small>@' + item.display_id + '</small><td>'
                        + getTableButton(item.unique_id)
                        + '</tr>';
        });
        tempHtml += '</tbody>';
        $('#ajaxTable').html(tempHtml);
    }
    $(function() {
        outputList(discoverList);
        
        });

</script>
@endpush

@push('js')
<script>
    $('#form').submit(function(e){
        var searchType = 'colleague';
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