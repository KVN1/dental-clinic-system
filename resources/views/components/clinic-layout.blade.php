<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — {{ \App\Models\AppSetting::current()->clinic_name ?? 'Dental Clinic' }}</title>

    @php $faviconSettings = \App\Models\AppSetting::current(); @endphp
    @if($faviconSettings->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $faviconSettings->logo) }}?v={{ $faviconSettings->updated_at->timestamp }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('storage/' . $faviconSettings->logo) }}?v={{ $faviconSettings->updated_at->timestamp }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $faviconSettings->logo) }}?v={{ $faviconSettings->updated_at->timestamp }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $themeSettings = \App\Models\AppSetting::current();
        $primaryHex   = $themeSettings->primary_color   ?? '#2A9D8F';
        $secondaryHex = $themeSettings->secondary_color ?? '#FF8966';
        $bgHex        = $themeSettings->bg_color        ?? '#F7F9F9';
        $surfaceHex   = $themeSettings->surface_color   ?? '#FFFFFF';

        $hexToRgbArr = function ($hex) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            return array_map('hexdec', str_split($hex, 2));
        };
        $rgbStr = fn($arr) => implode(', ', $arr);

        // Relative luminance (WCAG) to decide readable text color
        $luminance = function ($rgb) {
            [$r, $g, $b] = array_map(function ($c) {
                $c = $c / 255;
                return $c <= 0.03928 ? $c / 12.92 : pow((($c + 0.055) / 1.055), 2.4);
            }, $rgb);
            return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
        };

        // Pick readable ink color against a given background
        $readableTextOn = function ($bgRgb) use ($luminance) {
            return $luminance($bgRgb) > 0.5 ? '#12302E' : '#FFFFFF';
        };

        $primaryRgb   = $hexToRgbArr($primaryHex);
        $secondaryRgb = $hexToRgbArr($secondaryHex);
        $bgRgb        = $hexToRgbArr($bgHex);
        $surfaceRgb   = $hexToRgbArr($surfaceHex);

        // Ink (main text) reads against the page background
        $inkHex = $readableTextOn($bgRgb);
        // Muted text: same hue direction as ink, softened
        $mutedHex = $luminance($bgRgb) > 0.5 ? '#6B7280' : '#A0AAB4';

        // Text that sits on top of primary/secondary colored buttons
        $onPrimaryHex   = $readableTextOn($primaryRgb);
        $onSecondaryHex = $readableTextOn($secondaryRgb);
    @endphp
    <style>
        :root {
            --color-teal: {{ $primaryHex }} !important;
            --color-coral: {{ $secondaryHex }} !important;
            --color-teal-rgb: {{ $rgbStr($primaryRgb) }} !important;
            --color-coral-rgb: {{ $rgbStr($secondaryRgb) }} !important;

            --color-bg: {{ $bgHex }} !important;
            --color-surface: {{ $surfaceHex }} !important;
            --color-ink: {{ $inkHex }} !important;
            --color-muted: {{ $mutedHex }} !important;

            --color-on-primary: {{ $onPrimaryHex }} !important;
            --color-on-secondary: {{ $onSecondaryHex }} !important;
        }
    </style>
</head>
<body class="app-body {{ auth()->user()->theme === 'dark' ? 'theme-dark' : '' }}">
<div class="app-shell" x-data="{ sidebarOpen: false }">

        <div class="sidebar-overlay" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside class="sidebar" :class="sidebarOpen && 'sidebar-open'">
<div class="sidebar-brand">
                @php $clinicSettings = \App\Models\AppSetting::current(); @endphp
                @if($clinicSettings->logo)
                    <img src="{{ asset('storage/' . $clinicSettings->logo) }}?v={{ $clinicSettings->updated_at->timestamp }}" alt="{{ $clinicSettings->clinic_name }}" style="width: 48px; height: 48px; object-fit: contain; border-radius: 8px;">
                @else
                    <div style="width:48px;height:48px;border-radius:10px;background:#dce6f7;display:flex;align-items:center;justify-content:center;font-size:22px;color:#1e4a8a;flex-shrink:0;">
                        {{ strtoupper(substr($clinicSettings->clinic_name ?? 'D', 0, 1)) }}
                    </div>
                @endif
                <span class="sidebar-brand-name">{{ $clinicSettings->clinic_name ?? 'Dental Clinic' }}</span>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">▦</span> Dashboard
                </a>

                <a href="{{ route('dashboard') }}#reminders" class="nav-item nav-item-reminders">
                    <span class="nav-icon" style="position:relative;">
                        !
                        @if(($remindersCount ?? 0) > 0)
                            <span class="nav-badge">{{ $remindersCount > 99 ? '99+' : $remindersCount }}</span>
                        @endif
                    </span>
                    Reminders
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
                    <input type="hidden" name="redirect_to" value="/login">
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