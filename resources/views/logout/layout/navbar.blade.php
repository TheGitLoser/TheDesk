<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
    <div class="container">
        <div class="navbar-wrapper">
            <a class="navbar-brand" href="{{ route('logout.home') }}">{{ $title }}</a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                
                <li class="nav-item{{ $activePage == 'logout.register' ? ' active' : '' }}">
                    <a href="{{ route('logout.register') }}" class="nav-link">
                        <i class="material-icons">person_add</i> Register
                    </a>
                </li>
                <li class="nav-item{{ $activePage == 'logout.login' ? ' active' : '' }}">
                    <a href="{{ route('logout.login') }}" class="nav-link">
                        <i class="material-icons">fingerprint</i> Login
                    </a>
                </li>
                <li class="nav-item{{ $activePage == 'logout.forgotPassword' ? ' active' : '' }}">
                    <a href="{{ route('logout.forgotPassword') }}" class="nav-link">
                        <i class="fas fa-unlock"></i> Forgot Password
                    </a>
                </li>
                <li class="nav-item{{ $activePage == 'logout.resetPassword' ? ' active' : '' }}">
                    <a href="{{ route('logout.resetPassword') }}" class="nav-link">
                        <i class="fas fa-lock-open"></i> Reset Password
                    </a>
                </li>
                
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->