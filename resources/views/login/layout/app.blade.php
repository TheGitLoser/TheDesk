<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('login.layout.header')
</head>


<body>
    <div class="wrapper ">

        @include('login.layout.sidebar')

        <div class="main-panel">

            @include('login.layout.navbar')

            @yield('content')

            @include('login.layout.footer')

        </div>
    </div>

    @include('login.layout.sidebarFilter')
    @include('login.layout.endScript')

</body>

</html>