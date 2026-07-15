<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php $loginClinic = \App\Models\AppSetting::current(); @endphp
    <title>Sign In — {{ $loginClinic->clinic_name ?? 'Crosby Dental Clinic' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">

    <div class="login-stage" x-data="{ role: 'dentist', showPassword: false }">

        <div class="stage-grain" aria-hidden="true"></div>
        <div class="stage-glow" aria-hidden="true"></div>
        <div class="stage-glow-secondary" aria-hidden="true"></div>

        <div class="orbs" aria-hidden="true">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
        </div>

        <svg class="stage-tooth" viewBox="0 0 120 140" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M60 10C40 10 25 22 22 42C20 55 24 68 28 82C31 93 33 105 38 118C40 123 44 128 49 128C55 128 56 112 60 100C64 112 65 128 71 128C76 128 80 123 82 118C87 105 89 93 92 82C96 68 100 55 98 42C95 22 80 10 60 10Z"
                stroke="currentColor" stroke-width="2"/>
        </svg>

        <div class="floaters" aria-hidden="true">
            <span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span>
        </div>

        <!-- Bottom-left brand copy -->
        <div class="stage-copy">
            @if($loginClinic->logo)
                <img src="{{ asset('storage/' . $loginClinic->logo) }}" alt="{{ $loginClinic->clinic_name ?? 'Crosby Dental Clinic' }}" class="stage-logo">
            @else
                <img src="{{ asset('images/crosby-logo.png') }}" alt="{{ $loginClinic->clinic_name ?? 'Crosby Dental Clinic' }}" class="stage-logo">
            @endif

            <span class="stage-tag">
                <span class="tag-dot"></span> DENTAL PRACTICE SYSTEM
            </span>
            @php
                $nameParts = explode(' ', $loginClinic->clinic_name ?? 'Crosby Dental Clinic', 2);
                $firstWord = $nameParts[0] ?? 'Crosby';
                $restWords = $nameParts[1] ?? 'Dental Clinic';
            @endphp
            <h1 class="font-display stage-title">{{ $firstWord }}<br><span>{{ $restWords }}</span></h1>
            <p class="stage-subtitle">Manage patients, appointments, and balances — all in one place.</p>

            <div class="stage-dots">
                <span class="active"></span><span></span><span></span><span></span>
            </div>
        </div>

        <!-- Login glass card -->
        <div class="login-card">
            <div class="login-card-header">
                <div class="login-mark">
                    @if($loginClinic->logo)
                        <img src="{{ asset('storage/' . $loginClinic->logo) }}" alt="{{ $loginClinic->clinic_name ?? 'Crosby Dental Clinic' }}" class="login-mark-logo">
                    @else
                        <img src="{{ asset('images/crosby-logo.png') }}" alt="{{ $loginClinic->clinic_name ?? 'Crosby Dental Clinic' }}" class="login-mark-logo">
                    @endif
                </div>
                <div>
                    <div class="login-clinic-name">{{ $loginClinic->clinic_name ?? 'Crosby Dental Clinic' }}</div>
                    <div class="login-portal-label">CLINIC PORTAL</div>
                </div>
            </div>

            <h2 class="font-display login-welcome">Welcome back</h2>
            <p class="login-subtitle">Sign in with your account to continue</p>

            @if (session('status'))
                <div class="status-message">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf

                <div class="field-group">
                    <label for="email">EMAIL</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@crosbydental.com">
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-group">
                    <label for="password">PASSWORD</label>
                    <div class="password-wrap">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="••••••••">
                        <button type="button" class="show-toggle" @click="showPassword = !showPassword" x-text="showPassword ? 'Hide' : 'Show'"></button>
                    </div>
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="submit-btn">
                    <span>Sign In</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <button type="button" onclick="fillDemoClinic()" class="submit-btn" style="margin-top:10px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);">
                    Try Demo — Auto Fill &amp; Login
                </button>
            </form>

            <!-- Demo credentials -->
            <div style="margin-top:14px;padding:12px 16px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:10px;font-size:13px;color:rgba(255,255,255,0.8);">
                <div style="font-weight:700;color:#7eb3ff;margin-bottom:6px;letter-spacing:0.05em;">DEMO CREDENTIALS</div>
                <div>Email: <strong>kevinsatur11@gmail.com</strong></div>
                <div>Password: <strong>QWEASDzxc123</strong></div>
            </div>

            <div class="access-divider"><span>ACCESS LEVEL</span></div>

            <div class="access-pills">
                <button type="button" class="access-pill" :class="role === 'dentist' && 'active'" @click="role = 'dentist'">
                    <span class="pill-dot"></span>
                    <span class="pill-text">
                        <strong>Dentist / Admin</strong>
                        <em>Full clinical access</em>
                    </span>
                </button>
                <button type="button" class="access-pill" :class="role === 'staff' && 'active'" @click="role = 'staff'">
                    <span class="pill-dot"></span>
                    <span class="pill-text">
                        <strong>Front Desk</strong>
                        <em>Scheduling &amp; billing</em>
                    </span>
                </button>
            </div>

            <p class="login-footnote">© {{ date('Y') }} {{ $loginClinic->clinic_name ?? 'Crosby Dental Clinic' }} — Demo Mode</p>
        </div>

    </div>

<script>
function fillDemoClinic() {
    document.getElementById('email').value = 'kevinsatur11@gmail.com';
    document.getElementById('password').value = 'QWEASDzxc123';
    document.querySelector('form').submit();
}
</script>
</body>
</html>