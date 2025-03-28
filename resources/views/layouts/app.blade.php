<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php($title = (isset($title) ? "$title - " : '') . config('app.name'))

    <title>{{ $title }}</title>

    <meta name="description" content="{{ $description }}">
    <link rel="manifest" href="{{ url('app.webmanifest') }}" />

    <meta property="og:title" content="{{ $title }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="{{ url()->current() }}"/>
    <meta property="og:site_name" content="{{ config('app.name') }}"/>
    <meta property="og:locale" content="en_US"/>
    <meta property="og:description" content="{{ $description }}">

    <meta name="robots" content="index, follow">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md bg-light navbar-laravel p-3 mb-3 border-bottom">
            <div class="container">
                @auth
                    <a class="navbar-brand text-logo" href="{{ route('days.create') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                @else
                    <a class="navbar-brand text-logo" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                @endauth


                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto mb-3 mb-md-0">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->route()->named('days.create') ? 'active' : '' }}" href="{{ route('days.create') }}">{{ __('Home') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->route()->named('days.create') ? 'active' : '' }}" href="{{ route('days.today') }}">{{ __('Today') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->route()->named('calendar') ? 'active' : '' }}" href="{{ route('calendar') }}">{{ __('Calendar') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->route()->named('year.index') ? 'active' : '' }}" href="{{ route('year.index') }}">{{ __('Year') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->route()->named('year-month.index') ? 'active' : '' }}" href="{{ route('year-month.index') }}">{{ __('Year in Months') }}</a>
                            </li>

                            @if(auth()->user()->created_at->year < date('Y') && (\Carbon\Carbon::now()->lessThan(date('15-01-Y'))|| \Carbon\Carbon::now()->greaterThan(date('24-12-Y'))))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->route()->named('year-in-review.show') ? 'active' : '' }}" href="{{ route('year-in-review.show') }}">{{ __('Year in Review') }}</a>
                            </li>
                            @endif

                        @endauth

                        @guest
                            <li class="nav-item">
                                <a class="nav-link {{ request()->route()->named('page.faq') ? 'active' : '' }}" href="{{ route('page.faq') }}">{{ __('FAQ') }}</a>
                            </li>
                            @if(config('calendar.actions.demo'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->route()->named('page.demo') ? 'active' : '' }}" href="{{ route('page.demo') }}">{{ __('Demo') }}</a>
                            </li>
                            @endif
                        @endguest
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="btn btn-outline-dark me-2" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-primary" href="{{ route('register') }}">{{ __('Sign-up') }}</a>
                                </li>
                            @endif
                        @endguest

                        @auth

                            <li class="nav-item">
                                <form action="{{ route('days.index') }}" method="get" class="col-12 col-md-auto mb-3 mb-md-0 me-md-3" role="search">
                                    <label for="search" class="visually-hidden">Search</label>
                                    <input type="search" id="search" name="search" class="form-control" placeholder="Search..." value="{{ request()->get('search') }}">
                                </form>
                            </li>

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                   <a class="dropdown-item" href="{{ route('days.create') }}">New entry...</a>
                                   <a class="dropdown-item" href="{{ route('profile.edit') }}">Settings</a>
                                    <hr class="dropdown-divider">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        @include('layouts.partials.greeting')

        <main class="py-4">
            @if(session('message'))
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col">
                            <div class="alert alert-primary" role="alert">{{ session('message') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <footer>
            <div class="text-center">
                <ul class="list-inline small text-secondary mt-1">
                    <li class="list-inline-item"><a class="link-secondary text-decoration-none"
                                                    href="{{ route('page.privacy-policy') }}">Privacy</a></li>
                    <li class="list-inline-item">Tell Molly &copy; {{ date('Y') }}</li>
                </ul>
            </div>
        </footer>

    </div>
</body>
</html>
