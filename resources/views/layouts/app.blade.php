<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JAYBTAK Pro</title>

    {{-- Global CSS --}}
    <link href="{{ asset('css/globals.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
        }
        footer {
            margin-top: auto;
            width: 100%;
        }
    </style>
</head>
<body>
    {{-- 🔹 Navbar (Figma layout top) --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('JAYBTAK_LOGO.png') }}" 
                    alt="JAYBTAK Pro" 
                    class="img-fluid"
                    style="height: 40px;">
            </a>
            <div class="d-flex align-items-center">
                <a href="{{ url('/how-it-works') }}" class="nav-link">How it works</a>
                <a href="{{ url('/pricing') }}" class="nav-link">Pricing</a>
                <a href="{{ url('/contact') }}" class="nav-link">Contact</a>
                <a href="{{ route('login') }}" class="btn btn-outline-primary mx-2">Login</a>
                <a href="{{ route('signup') }}" class="btn btn-primary">Sign Up</a>
            </div>
        </div>
    </nav>

    {{-- 🔸 Main Page Content (Figma frame content) --}}
    <main class="py-5">
        @yield('content')
    </main>

    {{-- Global JS --}}
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
@include('partials.footer')

</body>
</html>
