<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <a class="navbar-brand" href="/" id="pageTitle">{{ $title }}</a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end">
            <form class="navbar-form">
            </form>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login.home') }}">
                        <i class="material-icons">dashboard</i>
                        <p class="d-lg-none d-md-block">
                            Home
                        </p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="" id="navbarDropdownMenuLink" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="material-icons">notifications</i>
                        <span class="notification"></span>
                        <p class="d-lg-none d-md-block">
                            Notifications
                        </p>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="#">{{ __('Mike John responded to your email') }}</a>
                        <a class="dropdown-item" href="#">{{ __('You have 5 new tasks') }}</a>
                        <a class="dropdown-item" href="#">{{ __('You\'re now friend with Andrew') }}</a>
                        <a class="dropdown-item" href="#">{{ __('Another Notification') }}</a>
                        <a class="dropdown-item" href="#">{{ __('Another One') }}</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#pablo" id="navbarDropdownProfile" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="material-icons">person</i>
                        <p class="d-lg-none d-md-block">
                            Account
                        </p>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                        <a class="dropdown-item" href="{{ route('login.account.profile') }}"> Profile</a>
                        <a class="dropdown-item" href="{{ route('login.account.editPassword') }}"> Change Password</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('login.account.logout') }}"> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>