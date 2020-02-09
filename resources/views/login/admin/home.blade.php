@extends('login.layout.app', ['activePage' => 'adminDashboard', 'title' => 'Business admin - Dashboard'])

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
                                    <span class="nav-tabs-title">Discover:</span>
                                    <ul class="nav nav-tabs" data-tabs="tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active show" id="discover-indi" data-toggle="tab">
                                                <i class="material-icons">emoji_people</i> Individual 
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="discover-business" data-toggle="tab">
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
 

