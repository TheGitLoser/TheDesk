@extends('login.layout.app', ['activePage' => 'adminViewBusinessPlan', 'title' => 'Business plan details'])

@section('content')
<div class="content">
    <div class="container-fluid">
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
                                    <td style="width:30%">Business name: </td>
                                    <td>{{$businessPlan->name}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%">Profile: </td>
                                    <td>{{$businessPlan->profile}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%">Admin: </td>
                                    <td>
                                        <table>
                                            @foreach ($businessPlanUser as $item)
                                                @if ($item->type == "business admin")
                                                <tr>
                                                    <td>{{$item->name}} <small>@ {{$item->display_id}}</small></td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:30%">Colleagues: </td>
                                    <td>
                                        <table>
                                            @foreach ($businessPlanUser as $item)
                                                @if ($item->type == "business")
                                                <tr>
                                                    <td>{{$item->name}} <small>@ {{$item->display_id}}</small></td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </table>
                                    </td>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
 

