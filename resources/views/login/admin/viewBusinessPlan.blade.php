@extends('login.layout.app', ['activePage' => 'adminViewBusinessPlan', 'title' => ''])

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
                                    <span class="nav-tabs-title">Business plan list:</span>
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
        linkButton = "{{ route('login.admin.viewBusinessPlanDetails',['uniqueId'=> '']) }}/" + uniqueId;

        output = '<td class="td-actions text-right td-button">';
        output += '<a href="' + linkButton + '">';
        output += '<i class="material-icons">info</i>';
        output += '</a></td>';

        return output;
    }
    function outputList(discoverList){
        tempHtml = '';
        tempHtml = '<tbody>';
        $.each(discoverList, function(i, item) {
            tempHtml += '<tr><td>' + item.company_name + '<td>'
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
        e.preventDefault();
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('ajax.searchBusinessPlan') }}",
            method: 'post',
            data: {
                name: $('#name').val()
            },
            success: function(response){
                outputList(response['output']);
            }
        });
    });
</script>
@endpush