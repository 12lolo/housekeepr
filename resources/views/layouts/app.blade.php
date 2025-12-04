<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HouseKeepr') }} - @yield('title', 'Dashboard')</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="app-layout">
        <header class="app-header">
            <div class="app-header-inner">
                <div class="d-flex align-center gap-4">
                    <h1 class="text-xl text-bold" style="margin: 0;">
                        <a href="{{ route('dashboard') }}" class="text-primary">HouseKeepr</a>
                    </h1>

                    @if(isset($portalName))
                        <span class="badge badge-light-primary">{{ $portalName }}</span>
                    @endif
                </div>

                <nav class="app-nav">
                    @yield('nav')

                    <div class="d-flex align-center gap-3">
                        <span class="text-sm text-muted">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-ghost">Uitloggen</button>
                        </form>
                    </div>
                </nav>
            </div>
        </header>

        <main class="app-main">
            <div class="app-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <div>{{ session('success') }}</div>
                        <button class="alert-close">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <div>{{ session('error') }}</div>
                        <button class="alert-close">&times;</button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="app-footer">
            <div class="app-footer-inner">
                <div class="app-footer-copy">
                    &copy; {{ date('Y') }} HouseKeepr. Alle rechten voorbehouden.
                </div>
                <div class="app-footer-links">
                    <a href="#">Help</a>
                    <a href="#">Privacy</a>
                    <a href="#">Voorwaarden</a>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
