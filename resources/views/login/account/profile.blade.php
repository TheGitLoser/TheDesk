@extends('login.layout.app', ['activePage' => '', 'title' => 'Your Profile'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon text-right">
                        <div class="card-icon">
                            <i class="far fa-id-card"></i>
                        </div>
                        <p class="card-category">Used Space</p>
                        <h3 class="card-title">49/50
                            <small>GB</small>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                1 of 2
                            </div>
                            <div class="col">
                                2 of 2
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">warning</i>
                            <a href="#pablo">Get More Space...</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection