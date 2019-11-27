<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('logout.layout.header')
</head>

<body class="off-canvas-sidebar">

    @include('logout.layout.navbar')

    <div class="wrapper wrapper-full-page">
        <div class="page-header login-page header-filter" filter-color="black"
            style="background-image: url('{{ asset('material') }}/img/login.jpg'); background-size: cover; background-position: top center;align-items: center;"
            data-color="purple">
            <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
            
            @yield('content')

            @include('logout.layout.footer')

        </div>
    </div>

    @include('logout.layout.endScript')

</body>

</html>