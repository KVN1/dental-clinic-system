<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — Crosby Dental Clinic</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body {{ auth()->user()->theme === 'dark' ? 'theme-dark' : '' }}">
<div class="app-shell" x-data="{ sidebarOpen: false }">

        <div class="sidebar-overlay" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside class="sidebar" :class="sidebarOpen && 'sidebar-open'">
<div class="sidebar-brand">
                <img src="{{ asset('images/crosby-logo.png') }}" alt="Crosby Dental Clinic" style="width: 48px; height: 48px; object-fit: contain;">
                <span class="sidebar-brand-name">Crosby Dental</span>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">▦</span> Dashboard
                </a>
                <a href="{{ route('patients.index') }}" class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                    <span class="nav-icon">◍</span> Patients
                </a>
<a href="{{ route('appointments.index') }}" class="nav-item {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
    <span class="nav-icon">▤</span> Appointments
</a>
@if (auth()->user()->isAdmin())
    <a href="{{ route('billing.index') }}" class="nav-item {{ request()->routeIs('billing.*') ? 'active' : '' }}">
        <span class="nav-icon">₱</span> Billing
    </a>
@endif
<a href="{{ route('logs.index') }}" class="nav-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
    <span class="nav-icon">▤</span> Logs
</a>
<a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
    <span class="nav-icon">⚙</span> Settings
</a>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-email">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Sign Out</button>
                </form>
            </div>
        </aside>

        <!-- Main -->
        <div class="app-main">
<header class="app-topbar" x-data="{ showPasswordModal: false }">
    <div style="display: flex; align-items: center; gap: 0.85rem;">
        <button type="button" class="mobile-menu-btn" @click="sidebarOpen = !sidebarOpen">☰</button>
        <h1 class="font-display topbar-title">{{ $title ?? 'Dashboard' }}</h1>
    </div>

<div class="topbar-right">
        @if (request()->routeIs('logs.*') || request()->routeIs('patients.index'))
            @if (session('amounts_visible', false))
                <form method="POST" action="{{ route('privacy.hide') }}">
                    @csrf
                    <button type="submit" class="privacy-toggle privacy-visible">
                        <span>👁</span> Hide Amounts
                    </button>
                </form>
            @else
                <button type="button" class="privacy-toggle privacy-hidden" @click="showPasswordModal = true">
                    <span>👁️</span> Show Amounts
                </button>
            @endif
        @endif

        <div class="topbar-date">{{ now()->format('l, F j, Y') }}</div>
    </div>

    <!-- Password confirmation modal -->
    <div class="modal-overlay" x-show="showPasswordModal" x-cloak @click.self="showPasswordModal = false" style="display: none;">
        <div class="modal-box">
            <h3 class="font-display modal-title">Confirm Password</h3>
            <p class="modal-subtitle">Enter your password to reveal amounts.</p>

            <form method="POST" action="{{ route('privacy.reveal') }}">
                @csrf
                <input type="password" name="password" class="modal-input" placeholder="Password" autofocus>
                @error('password')
                    <span class="field-error">{{ $message }}</span>
                @enderror

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" @click="showPasswordModal = false">Cancel</button>
                    <button type="submit" class="btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</header>

            <main class="app-content">
                {{ $slot }}
            </main>
        </div>

    </div>

</body>
</html>