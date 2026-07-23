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

    <a href="{{ route('manual.index') }}" class="nav-item {{ request()->routeIs('manual.*') ? 'active' : '' }}">
        <span class="nav-icon">?</span> User Manual
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
                @if (session('demo_blocked'))
                    <div style="background:#FFF7ED;border:1px solid #FDBA74;border-radius:8px;padding:10px 16px;margin-bottom:1rem;color:#9A3412;font-size:13px;">
                        {{ session('demo_blocked') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

    </div>



@php
    $tourSteps = [
        'dashboard'    => ['title' => 'Your Dashboard', 'desc' => "This is your home base. See today's appointments, outstanding balances, and reminders here every time you log in.", 'next_step' => 'patients', 'next_url' => route('patients.index'), 'next_label' => 'Next: Patients'],
        'patients'     => ['title' => 'Patients', 'desc' => 'Add new patients, search existing ones, and manage their full medical history, treatments, prescriptions, and X-rays from one place.', 'next_step' => 'appointments', 'next_url' => route('appointments.index'), 'next_label' => 'Next: Appointments'],
        'appointments' => ['title' => 'Appointments', 'desc' => 'Book appointments manually, or click directly on the calendar to schedule. Assign a dentist and the calendar color-codes automatically.', 'next_step' => 'manual', 'next_url' => route('manual.index'), 'next_label' => 'Next: User Manual'],
        'manual'       => ['title' => 'Need Help Later?', 'desc' => 'This User Manual is always here with step-by-step guides for everything in the system. Come back anytime.', 'next_step' => 'settings', 'next_url' => route('settings.index'), 'next_label' => 'Next: Settings'],
        'settings'     => ['title' => 'Settings', 'desc' => 'This is where you brand the system as your own: clinic name, logo, colors, staff accounts, backups, and more.', 'next_step' => null, 'next_url' => null, 'next_label' => 'Finish Tour'],
    ];

    $currentTourStep = session('onboarding_step');
@endphp

@if ($currentTourStep && isset($tourSteps[$currentTourStep]))
    @php $step = $tourSteps[$currentTourStep]; @endphp
    <div style="position:fixed;bottom:24px;right:24px;z-index:9999;background:#fff;border-radius:14px;padding:18px 20px;max-width:300px;box-shadow:0 12px 40px rgba(0,0,0,0.25);border:1px solid #E7ECEB;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-size:10px;font-weight:700;color:#2A9D8F;text-transform:uppercase;letter-spacing:0.06em;">Quick Guide</span>
            <form method="POST" action="{{ route('onboarding.dismiss') }}" style="margin:0;">
                @csrf
                <button type="submit" style="background:none;border:none;color:#6B7280;font-size:12px;cursor:pointer;">Skip</button>
            </form>
        </div>
        <div style="font-size:14px;font-weight:700;color:#12302E;margin-bottom:6px;">{{ $step['title'] }}</div>
        <div style="font-size:12px;color:#6B7280;line-height:1.6;margin-bottom:14px;">{{ $step['desc'] }}</div>

        @if ($step['next_url'])
            <form method="POST" action="{{ route('onboarding.advance') }}">
                @csrf
                <input type="hidden" name="step" value="{{ $step['next_step'] }}">
                <input type="hidden" name="next_url" value="{{ $step['next_url'] }}">
                <button type="submit" style="background:#2A9D8F;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">
                    {{ $step['next_label'] }} &rarr;
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('onboarding.dismiss') }}">
                @csrf
                <button type="submit" style="background:#2A9D8F;color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">
                    {{ $step['next_label'] }}
                </button>
            </form>
        @endif
    </div>
@endif

@php
    $onboardingStep = session('onboarding_step');
    $onboardingSteps = \App\Http\Controllers\OnboardingController::steps();
    $showOnboarding = false;
    $onboardingData = null;

    if ($onboardingStep && isset($onboardingSteps[$onboardingStep])) {
        $expectedRoute = $onboardingSteps[$onboardingStep]['route'];
        if (request()->routeIs($expectedRoute) || request()->routeIs($expectedRoute . '.*')) {
            $showOnboarding = true;
            $onboardingData = $onboardingSteps[$onboardingStep];
        }
    }
@endphp

@if($showOnboarding)
<div class="modal-overlay" style="display:flex;">
    <div class="modal-box" style="max-width:380px;text-align:left;">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <span style="font-size:10px;font-weight:700;color:var(--color-teal);text-transform:uppercase;letter-spacing:0.06em;">
                Step {{ $onboardingStep }} of {{ count($onboardingSteps) }}
            </span>
            <form method="POST" action="{{ route('onboarding.skip') }}" style="margin:0;">
                @csrf
                <button type="submit" style="background:none;border:none;color:var(--color-muted);font-size:12px;cursor:pointer;">Skip tour</button>
            </form>
        </div>

        <div style="font-size:16px;font-weight:700;color:var(--color-ink);margin-bottom:8px;">
            {{ $onboardingData['title'] }}
        </div>
        <div style="font-size:13px;color:var(--color-muted);line-height:1.65;margin-bottom:20px;">
            {{ $onboardingData['desc'] }}
        </div>

        <form method="POST" action="{{ route('onboarding.next') }}">
            @csrf
            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">
                {{ $onboardingStep >= count($onboardingSteps) ? 'Finish' : 'Next: ' . ($onboardingSteps[$onboardingStep + 1]['title'] ?? '') }}
            </button>
        </form>

    </div>
</div>
@endif

</body>
</html>