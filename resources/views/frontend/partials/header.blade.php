@php
    use App\Models\CompanyDetails;
    $company = CompanyDetails::select('company_name')->first();
@endphp

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

        <a href="{{ route('homepage') }}" class="logo d-flex align-items-center">
        <h1 class="sitename">{{ $company->company_name }}</h1>
        </a>

        <nav id="navmenu" class="navmenu">
        <ul>
            <li>
                <a href="{{ route('homepage') }}" class="{{ request()->routeIs('homepage') ? 'active' : '' }}">Blog</a>
            </li>
            <li>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About Us</a>
            </li>
            @if (Auth::check())
            <li class="dropdown">
                <a href="#"><span>Account</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                    @if(auth()->user()->is_type == '1')
                        <li><a href="{{ route('admin.dashboard') }}">{{Auth::user()->name}}</a></li>
                    @else
                        <li><a>{{Auth::user()->name}}</a></li>
                    @endif
                    <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </ul>
            </li>
        @else
            <li class="dropdown">
                <a href="#"><span>Login/Register</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                </ul>
            </li>
        @endif

        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

    </div>
</header>