<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/manifest.js') }}" type="text/javascript"></script>
    <script src="{{ mix('js/vendor.js') }}" type="text/javascript"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css" >
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }
       .centered-and-cropped { 
          object-fit: cover; 
       }
    </style>
    @yield('head')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" id="appBar">
            <init-store></init-store>
            <div class="container">
                <a href="{{ url('/') }}">
		  <img src="/images/milkiland_logo.png">
                </a>
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ __('messages.MainPage') }}
                </a>
                <a class="navbar-brand" href="{{ route('video') }}">
                    {{ __('messages.Video') }}
                </a>
                <a class="navbar-brand" href="{{ route('documentation') }}">
                    {{ __('messages.Documentation') }}
                </a>
                <a class="navbar-brand" href="{{ route('contact') }}">
                    {{ __('messages.Contacts') }}
                </a>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="navbar-brand" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            @if (Auth::user()->avatar)
                            <img class="centered-and-cropped" width="30" height="30" style="border-radius:50%" src="{{ url(Auth::user()->avatar) }}"> 
                            @endif
                            <a id="navbarDropdown" class="navbar-brand dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    {{ __('messages.Profile') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('fileupload') }}">
                                    {{ __('messages.Upload') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('chat') }}">
                                    {{ __('messages.Chat') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="navbar-brand dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('messages.Language') }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('locale', ['locale' => 'en']) }}">
                                {{ __('messages.EN') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('locale', ['locale' => 'ru']) }}">
                                {{ __('messages.RU') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('locale', ['locale' => 'uk']) }}">
                                {{ __('messages.UK') }}
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
    @yield('js')
</body>
</html>
