<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.login.header')
</head>


<body>
    <div class="wrapper ">

        @include('layouts.login.sidebar')

        <div class="main-panel">

            @include('layouts.login.navbar')

            @yield('content')

            @include('layouts.login.footer')

        </div>
    </div>

    @include('layouts.login.sidebarFilter')
    @include('layouts.login.endScript')

</body>

</html>