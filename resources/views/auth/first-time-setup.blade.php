<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $setupClinic = \App\Models\AppSetting::current(); @endphp
    <title>First-Time Setup — {{ $setupClinic->clinic_name ?? 'Dental Clinic System' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">

    <div class="login-stage">
        <div class="stage-grain" aria-hidden="true"></div>
        <div class="stage-glow" aria-hidden="true"></div>
        <div class="stage-glow-secondary" aria-hidden="true"></div>

        <div class="orbs" aria-hidden="true">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
        </div>

        <div class="floaters" aria-hidden="true">
            <span></span><span></span><span></span><span></span><span></span>
        </div>

        <div class="login-card" style="max-width:460px;">

            <div class="login-card-header">
                <div class="login-mark">
                    @if($setupClinic->logo)
                        <img src="{{ asset('storage/' . $setupClinic->logo) }}" alt="{{ $setupClinic->clinic_name }}" class="login-mark-logo">
                    @else
                        <img src="{{ asset('images/crosby-logo.png') }}" alt="{{ $setupClinic->clinic_name ?? 'Dental Clinic System' }}" class="login-mark-logo">
                    @endif
                </div>
                <div>
                    <div class="login-clinic-name">{{ $setupClinic->clinic_name ?? 'Dental Clinic System' }}</div>
                    <div class="login-portal-label">FIRST-TIME SETUP</div>
                </div>
            </div>

            <h1 class="font-display" style="font-size:1.5rem;font-weight:700;color:var(--color-ink);margin-bottom:6px;">
                Welcome! Let's get set up.
            </h1>
            <p style="font-size:13px;color:var(--color-muted);margin-bottom:1.75rem;line-height:1.6;">
                This appears to be your first time launching the system. Create your admin account below to get started, this only happens once.
            </p>

            @if ($errors->any())
                <div style="background:rgba(217,83,79,0.1);border:1px solid rgba(217,83,79,0.3);border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#D9534F;font-size:13px;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('setup.store') }}">
                @csrf

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;color:var(--color-muted);margin-bottom:6px;">Your Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           style="width:100%;padding:11px 14px;border-radius:10px;border:1px solid var(--color-border, #E7ECEB);background:var(--color-bg);color:var(--color-ink);font-size:14px;">
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;color:var(--color-muted);margin-bottom:6px;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           style="width:100%;padding:11px 14px;border-radius:10px;border:1px solid var(--color-border, #E7ECEB);background:var(--color-bg);color:var(--color-ink);font-size:14px;">
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;color:var(--color-muted);margin-bottom:6px;">Password</label>
                    <input type="password" name="password" required
                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                           title="At least 8 characters, including an uppercase letter, a lowercase letter, and a number"
                           style="width:100%;padding:11px 14px;border-radius:10px;border:1px solid var(--color-border, #E7ECEB);background:var(--color-bg);color:var(--color-ink);font-size:14px;">
                    <div style="font-size:11px;color:var(--color-muted);margin-top:4px;">Min 8 characters, with uppercase, lowercase, and a number.</div>
                </div>

                <div style="margin-bottom:22px;">
                    <label style="display:block;font-size:12px;color:var(--color-muted);margin-bottom:6px;">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           style="width:100%;padding:11px 14px;border-radius:10px;border:1px solid var(--color-border, #E7ECEB);background:var(--color-bg);color:var(--color-ink);font-size:14px;">
                </div>

                <button type="submit" class="submit-btn">
                    <span>Create Admin Account &amp; Continue</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

            <p class="login-footnote" style="margin-top:1.5rem;">You can add staff and dentist accounts later from Settings.</p>

        </div>

    </div>

</body>
</html>
