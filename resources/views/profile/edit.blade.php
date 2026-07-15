<x-clinic-layout :title="'My Profile'">

    @if (session('status') === 'profile-updated')
        <div class="status-message" style="margin-bottom: 1.25rem;">Profile updated successfully.</div>
    @endif
    @if (session('status') === 'provider-info-updated')
        <div class="status-message" style="margin-bottom: 1.25rem;">Provider details updated successfully.</div>
    @endif

    <div class="panel">

        <!-- Basic Info -->
        <h3 class="form-section-title">Profile Information</h3>
        <form method="POST" action="{{ route('profile.update') }}" class="clinic-form">
            @csrf
            @method('PATCH')

            <div class="form-grid" style="margin-bottom:12px;">
                <div class="field-group">
                    <label for="name">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                <button type="submit" class="btn-primary">Save Profile</button>
            </div>
        </form>

        <hr style="border:none;border-top:1px solid var(--color-border, #E7ECEB);margin:1.75rem 0;">

        <!-- Treating Provider Info - specialty & calendar color -->
        <h3 class="form-section-title">Treating Provider Details</h3>
        <p style="font-size:12px;color:var(--color-muted);margin-bottom:14px;">
            @if($user->isAdmin())
                If you also treat patients directly, set a specialty and calendar color below so you appear correctly in appointment scheduling and the calendar view.
            @else
                Your specialty and calendar color, shown when patients and appointments are assigned to you.
            @endif
        </p>
        <form method="POST" action="{{ route('profile.provider.update') }}" class="clinic-form">
            @csrf

            <div class="form-grid" style="margin-bottom:12px;">
                <div class="field-group">
                    <label for="specialty">Specialty <span class="optional-tag">optional</span></label>
                    <input id="specialty" type="text" name="specialty" value="{{ old('specialty', $user->specialty) }}" placeholder="e.g. General Dentistry, Orthodontics">
                </div>
                <div class="field-group">
                    <label for="provider_color">Calendar Color</label>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input type="color" id="provider_color" name="color" value="{{ $user->color ?? '#2A9D8F' }}" style="width:44px;height:36px;border:none;padding:0;cursor:pointer;border-radius:5px;">
                        <span style="font-size:11px;color:var(--color-muted);">Used to color-code your appointments</span>
                    </div>
                </div>
            </div>

            <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                <button type="submit" class="btn-primary">Save Provider Details</button>
            </div>
        </form>

        <hr style="border:none;border-top:1px solid var(--color-border, #E7ECEB);margin:1.75rem 0;">

        <!-- Password -->
        <h3 class="form-section-title">Update Password</h3>
        <form method="POST" action="{{ route('password.update') }}" class="clinic-form">
            @csrf
            @method('PUT')

            <div class="form-grid" style="margin-bottom:12px;">
                <div class="field-group field-group-full">
                    <label for="current_password">Current Password</label>
                    <input id="current_password" type="password" name="current_password">
                    @error('current_password', 'updatePassword') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="password">New Password</label>
                    <input id="password" type="password" name="password"
                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                           title="At least 8 characters, including an uppercase letter, a lowercase letter, and a number">
                    <div style="font-size:11px;color:var(--color-muted);margin-top:4px;">Min 8 characters, with uppercase, lowercase, and a number.</div>
                    @error('password', 'updatePassword') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation">
                    @error('password_confirmation', 'updatePassword') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                <button type="submit" class="btn-primary">Update Password</button>
            </div>
        </form>

        <hr style="border:none;border-top:1px solid var(--color-border, #E7ECEB);margin:1.75rem 0;">

        <!-- Delete Account -->
        <h3 class="form-section-title" style="color:#D9534F;">Danger Zone</h3>
        <p style="font-size:12px;color:var(--color-muted);margin-bottom:14px;">
            Deleting your account is permanent and cannot be undone.
        </p>
        <button type="button" class="pill-btn pill-btn-danger" onclick="document.getElementById('delete-account-modal').style.display='flex'">
            Delete Account
        </button>

        <div id="delete-account-modal" class="modal-overlay" style="display:none;" onclick="if(event.target === this) this.style.display='none'">
            <div class="modal-box">
                <div class="modal-title">Delete Account</div>
                <div class="modal-subtitle">This action is permanent. Enter your password to confirm.</div>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <input type="password" name="password" class="modal-input" placeholder="Your password" required>
                    @error('password', 'userDeletion') <span class="field-error">{{ $message }}</span> @enderror
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="document.getElementById('delete-account-modal').style.display='none'">Cancel</button>
                        <button type="submit" class="pill-btn pill-btn-danger" style="padding:8px 16px;">Delete My Account</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-clinic-layout>