<x-clinic-layout :title="'Settings'">

    @if (session('success'))
        <div class="status-message" style="margin-bottom: 1.25rem;">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="field-error" style="margin-bottom: 1.25rem; background: #FDECEA; padding: 0.75rem 1rem; border-radius: 0.6rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="panel" x-data="{ tab: localStorage.getItem('settingsTab') || 'appearance' }" x-init="$watch('tab', value => localStorage.setItem('settingsTab', value))">

        <div class="settings-tabs">
            <button type="button" class="settings-tab" :class="tab === 'appearance' && 'active'" @click="tab = 'appearance'">Appearance</button>
            <button type="button" class="settings-tab" :class="tab === 'exports' && 'active'" @click="tab = 'exports'">Export Data</button>
            @if (auth()->user()->isAdmin())
                <button type="button" class="settings-tab" :class="tab === 'backups' && 'active'" @click="tab = 'backups'">Backups</button>
                <button type="button" class="settings-tab" :class="tab === 'staff' && 'active'" @click="tab = 'staff'">Staff Accounts</button>
                <button type="button" class="settings-tab" :class="tab === 'clinic' && 'active'" @click="tab = 'clinic'">Clinic Settings</button>
            @endif
        </div>

<!-- ===== Appearance ===== -->
        <div x-show="tab === 'appearance'" class="settings-panel">
            <h3 class="form-section-title">Dark Mode</h3>
            <form method="POST" action="{{ route('settings.theme') }}" class="theme-row">
                @csrf
                <div>
                    <div class="settings-label">Dark Mode</div>
                    <div class="settings-sublabel">Switch between light and dark interface</div>
                </div>
                <button type="submit" class="theme-switch {{ auth()->user()->theme === 'dark' ? 'active' : '' }}">
                    <span class="theme-switch-knob"></span>
                </button>
            </form>
        </div>

        <!-- ===== Exports ===== -->
        <div x-show="tab === 'exports'" class="settings-panel">
            <div class="export-section">
                <h3 class="form-section-title">Patients</h3>
                <form method="GET" action="{{ route('settings.export.patients') }}" class="filter-bar" style="margin-bottom: 0.75rem;">
                    <div class="field-group">
                        <label for="balance_filter">Filter</label>
                        <select id="balance_filter" name="balance_filter">
                            <option value="">All Patients</option>
                            <option value="with_balance">With Outstanding Balance</option>
                            <option value="paid_up">Paid Up</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-primary">⬇ Download CSV</button>
                    </div>
                </form>
            </div>

            <div class="export-section" style="margin-top: 1.5rem;">
                <h3 class="form-section-title">Logs</h3>
                <form method="GET" action="{{ route('settings.export.logs') }}" class="filter-bar">
                    <div class="field-group">
                        <label for="export_patient_search">Patient (optional)</label>
                        <input
                            id="export_patient_search"
                            type="text"
                            list="export-patient-options"
                            placeholder="All patients"
                            oninput="matchExportPatient(this.value)"
                            autocomplete="off"
                        >
                        <datalist id="export-patient-options">
                            @foreach ($patients as $patient)
                                <option data-id="{{ $patient->id }}" value="{{ $patient->last_name }}, {{ $patient->first_name }}"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" id="export_patient_id" name="patient_id" value="">
                    </div>

                    <div class="field-group">
                        <label for="export_date_from">From</label>
                        <input id="export_date_from" type="date" name="date_from">
                    </div>

                    <div class="field-group">
                        <label for="export_date_to">To</label>
                        <input id="export_date_to" type="date" name="date_to">
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-primary">⬇ Download CSV</button>
                    </div>
                </form>
            </div>

            <p class="settings-sublabel" style="margin-top: 1rem;">
                Tip: you can also export a single patient's history (with date filters) from their profile page.
            </p>

            <script>
                const exportPatientMap = {
                    @foreach ($patients as $patient)
                        "{{ $patient->last_name }}, {{ $patient->first_name }}": {{ $patient->id }},
                    @endforeach
                };

                function matchExportPatient(value) {
                    const hiddenInput = document.getElementById('export_patient_id');
                    hiddenInput.value = exportPatientMap.hasOwnProperty(value) ? exportPatientMap[value] : '';
                }
            </script>
        </div>

        @if (auth()->user()->isAdmin())

            <!-- ===== Backups ===== -->
            <div x-show="tab === 'backups'" class="settings-panel">
                <div class="panel-header" style="margin-bottom: 1.1rem;">
                    <h3 class="form-section-title" style="margin-bottom: 0;">Database Backups</h3>
                    <form method="POST" action="{{ route('settings.backup') }}">
                        @csrf
                        <button type="submit" class="btn-primary">+ Backup Now</button>
                    </form>
                </div>

                <div class="backup-schedule-box">
                    <h3 class="form-section-title">Automatic Backup Schedule</h3>

                    <form method="POST" action="{{ route('settings.backup.preferences') }}" x-data="{ freq: '{{ $appSettings->backup_frequency_type }}' }">
                        @csrf

                        <div class="schedule-options">
                            <label class="schedule-option" :class="freq === 'daily' && 'selected'">
                                <input type="radio" name="backup_frequency_type" value="daily" x-model="freq" style="display:none;">
                                <div class="schedule-option-title">Once a Day</div>
                                <div class="schedule-option-desc">Backs up automatically at a set time every day</div>
                            </label>

                            <label class="schedule-option" :class="freq === 'hourly' && 'selected'">
                                <input type="radio" name="backup_frequency_type" value="hourly" x-model="freq" style="display:none;">
                                <div class="schedule-option-title">Every Hour</div>
                                <div class="schedule-option-desc">Backs up repeatedly, starting from a set time</div>
                            </label>
                        </div>

                        <div class="schedule-time-row">
                            <div class="field-group" style="max-width: 220px;">
                                <label for="backup_time" x-text="freq === 'daily' ? 'Backup Time' : 'Start Backing Up At'"></label>
                                <input id="backup_time" type="time" name="backup_time" value="{{ \Carbon\Carbon::parse($appSettings->backup_time)->format('H:i') }}" required>
                            </div>

                            <div class="field-group" style="flex: 1;">
                                <label for="backup_external_path">External Backup Folder <span class="optional-tag">optional</span></label>
                                <input id="backup_external_path" type="text" name="backup_external_path" value="{{ $appSettings->backup_external_path }}" placeholder="e.g. D:\ClinicBackups">
                            </div>
                        </div>

                        <div class="schedule-summary">
                            <span class="summary-icon">ⓘ</span>
                            <span x-show="freq === 'daily'">
                                A backup will be created once every day, shortly after <strong>{{ \Carbon\Carbon::parse($appSettings->backup_time)->format('g:i A') }}</strong>.
                            </span>
                            <span x-show="freq === 'hourly'">
                                A backup will be created every hour, starting from <strong>{{ \Carbon\Carbon::parse($appSettings->backup_time)->format('g:i A') }}</strong> each day.
                            </span>
                        </div>

                        <div class="form-footer" style="justify-content: flex-start; border-top: none; padding-top: 0.5rem;">
                            <button type="submit" class="btn-primary">Save Schedule</button>
                        </div>
                    </form>
                </div>

                <h3 class="form-section-title" style="margin-top: 1.75rem;">Backup History</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Backup File</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($backups as $backup)
                            <tr>
                                <td>{{ $backup['name'] }}</td>
                                <td>{{ $backup['size'] }} KB</td>
                                <td>{{ $backup['date'] }}</td>
                                <td><a href="{{ route('settings.backup.download', $backup['name']) }}" class="table-link">Download</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-row">No backups yet. Create one above.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ===== Staff Accounts ===== -->
            <div x-show="tab === 'staff'" class="settings-panel">
                <table class="data-table" style="margin-bottom: 1.5rem;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span class="status-tag {{ $u->role === 'admin' ? 'status-completed' : ($u->role === 'dentist' ? 'status-confirmed' : 'status-scheduled') }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                    @if($u->role === 'dentist' && $u->specialty)
                                        <span style="font-size:11px;color:var(--color-muted);display:block;margin-top:2px;">{{ $u->specialty }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('settings.users.destroy', $u) }}" onsubmit="return confirm('Remove this account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="table-link" style="background:none;border:none;color:#D96A48;cursor:pointer;">Remove</button>
                                        </form>
                                    @else
                                        <span class="settings-sublabel">You</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h3 class="form-section-title">Add New Account</h3>
                <form method="POST" action="{{ route('settings.users.store') }}" class="clinic-form" x-data="{ selectedRole: 'staff' }">
                    @csrf
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="name">Name</label>
                            <input id="name" type="text" name="name" required>
                        </div>
                        <div class="field-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" required>
                        </div>
                        <div class="field-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required minlength="8"
                                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                                   title="At least 8 characters, including an uppercase letter, a lowercase letter, and a number">
                            <div style="font-size:11px;color:var(--color-muted);margin-top:4px;">Min 8 characters, with uppercase, lowercase, and a number.</div>
                        </div>
                        <div class="field-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8">
                        </div>
                        <div class="field-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" x-model="selectedRole" required>
                                <option value="staff">Staff</option>
                                <option value="dentist">Dentist</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="field-group" x-show="selectedRole === 'dentist'" x-cloak>
                            <label for="specialty">Specialty <span class="optional-tag">optional</span></label>
                            <input id="specialty" type="text" name="specialty" placeholder="e.g. Orthodontics, General Dentistry">
                        </div>
                        <div class="field-group" x-show="selectedRole === 'dentist'" x-cloak>
                            <label for="dentist_color">Calendar Color</label>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <input type="color" id="dentist_color" name="color" value="#2A9D8F" style="width:44px;height:36px;border:none;padding:0;cursor:pointer;border-radius:5px;">
                                <span style="font-size:11px;color:var(--color-muted);">Used to color-code their appointments</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-footer" style="justify-content: flex-start;">
                        <button type="submit" class="btn-primary">Create Account</button>
                    </div>
                </form>
            </div>


            <!-- ===== Clinic Settings Tab ===== -->
            <div x-show="tab === 'clinic'" class="settings-panel">
            
                {{-- SECTION 1: CLINIC IDENTITY --}}
                <form method="POST" action="{{ route('settings.clinic') }}" enctype="multipart/form-data" class="clinic-form">
                    @csrf
                    <input type="hidden" name="section" value="identity">
                    <h3 class="form-section-title">Clinic Identity</h3>
                    <div class="field-group" style="margin-bottom:1.25rem;" x-data="{ logoPreview: null }">
                        <label>Clinic Logo</label>
                        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                            <template x-if="logoPreview">
                                <img :src="logoPreview" style="height:60px;width:60px;object-fit:cover;border-radius:8px;border:1px solid #dce6f7;padding:4px;background:#fff;">
                            </template>
                            <template x-if="!logoPreview">
                                @if($appSettings->logo)
                                    <img src="{{ asset('storage/' . $appSettings->logo) }}?v={{ $appSettings->updated_at->timestamp }}" style="height:60px;border-radius:8px;border:1px solid #dce6f7;padding:4px;background:#fff;">
                                @else
                                    <div style="height:60px;width:60px;border-radius:10px;background:#dce6f7;display:flex;align-items:center;justify-content:center;font-size:26px;color:#1e4a8a;font-weight:bold;">
                                        {{ strtoupper(substr($appSettings->clinic_name ?? 'D', 0, 1)) }}
                                    </div>
                                @endif
                            </template>

                            @if($appSettings->logo)
                                <a href="{{ route('settings.clinic.remove-logo') }}" onclick="return confirm('Remove logo?')" style="color:#D96A48;font-size:13px;text-decoration:none;">Remove</a>
                            @endif

                            <div>
                                <input type="file" name="logo" accept="image/*" style="font-size:13px;"
                                       @change="const f = $event.target.files[0]; if (f) { const r = new FileReader(); r.onload = e => logoPreview = e.target.result; r.readAsDataURL(f); }">
                                <div style="font-size:11px;color:#aaa;margin-top:4px;">PNG, JPG, SVG. Max 2MB.</div>
                                <div x-show="logoPreview" style="font-size:11px;color:var(--color-teal);margin-top:2px;">New logo selected — click "Save Identity" below to apply.</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="field-group">
                            <label>Clinic Name</label>
                            <input type="text" name="clinic_name" value="{{ old('clinic_name', $appSettings->clinic_name) }}" placeholder="e.g. Clear Smile Dental Clinic">
                        </div>
                        <div class="field-group">
                            <label>Tagline <span class="optional-tag">optional</span></label>
                            <input type="text" name="tagline" value="{{ old('tagline', $appSettings->tagline) }}" placeholder="e.g. Your Smile, Our Priority">
                        </div>
                    </div>
                    <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                        <button type="submit" class="btn-primary">Save Identity</button>
                    </div>
                </form>
            
                <hr style="border:none;border-top:1px solid #eef2ff;margin:1.5rem 0;">
            
                {{-- SECTION 2: CONTACT INFO --}}
                <form method="POST" action="{{ route('settings.clinic') }}" class="clinic-form">
                    @csrf
                    <input type="hidden" name="section" value="contact">
                    <h3 class="form-section-title">Contact Information</h3>
                    <div class="form-grid">
                        <div class="field-group" style="grid-column:span 2;">
                            <label>Address</label>
                            <input type="text" name="address" value="{{ old('address', $appSettings->address) }}" placeholder="e.g. 123 Main St, Baguio City">
                        </div>
                        <div class="field-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $appSettings->phone) }}" placeholder="+63 912 345 6789">
                        </div>
                        <div class="field-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email', $appSettings->email) }}" placeholder="clinic@email.com">
                        </div>
                        <div class="field-group">
                            <label>Website <span class="optional-tag">optional</span></label>
                            <input type="text" name="website" value="{{ old('website', $appSettings->website) }}" placeholder="https://www.yourclinic.com">
                        </div>
                        <div class="field-group">
                            <label>TIN <span class="optional-tag">optional</span></label>
                            <input type="text" name="tin" value="{{ old('tin', $appSettings->tin) }}" placeholder="000-000-000-000">
                        </div>
                    </div>
                    <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                        <button type="submit" class="btn-primary">Save Contact Info</button>
                    </div>
                </form>
            
                <hr style="border:none;border-top:1px solid #eef2ff;margin:1.5rem 0;">
            
                {{-- SECTION 3: CURRENCY & BILLING --}}
                <form method="POST" action="{{ route('settings.clinic') }}" class="clinic-form">
                    @csrf
                    <input type="hidden" name="section" value="billing">
                    <h3 class="form-section-title">Currency & Billing</h3>
                    @php
                    $currencies = ['PHP'=>['Philippine Peso','₱'],'USD'=>['US Dollar','$'],'EUR'=>['Euro','€'],'GBP'=>['British Pound','£'],'JPY'=>['Japanese Yen','¥'],'AUD'=>['Australian Dollar','A$'],'CAD'=>['Canadian Dollar','C$'],'SGD'=>['Singapore Dollar','S$'],'HKD'=>['Hong Kong Dollar','HK$'],'KRW'=>['South Korean Won','₩'],'CNY'=>['Chinese Yuan','¥'],'INR'=>['Indian Rupee','₹'],'MYR'=>['Malaysian Ringgit','RM'],'THB'=>['Thai Baht','฿'],'IDR'=>['Indonesian Rupiah','Rp'],'VND'=>['Vietnamese Dong','₫'],'SAR'=>['Saudi Riyal','SR'],'AED'=>['UAE Dirham','AED'],'ZAR'=>['South African Rand','R'],'BRL'=>['Brazilian Real','R$'],'MXN'=>['Mexican Peso','$'],'NZD'=>['New Zealand Dollar','NZ$'],'CHF'=>['Swiss Franc','CHF'],'NOK'=>['Norwegian Krone','kr'],'SEK'=>['Swedish Krona','kr'],'DKK'=>['Danish Krone','kr'],'PKR'=>['Pakistani Rupee','Rs'],'BDT'=>['Bangladeshi Taka','Tk'],'EGP'=>['Egyptian Pound','E£'],'NGN'=>['Nigerian Naira','₦'],'KES'=>['Kenyan Shilling','KSh'],'CLP'=>['Chilean Peso','$'],'COP'=>['Colombian Peso','$'],'PEN'=>['Peruvian Sol','S/'],'ARS'=>['Argentine Peso','$']];
                    @endphp
                    <div class="form-grid">
                        <div class="field-group">
                            <label>Currency</label>
                            <select name="currency_code" onchange="updateSymbol(this.value)">
                                @foreach($currencies as $code => [$name, $symbol])
                                    <option value="{{ $code }}" data-symbol="{{ $symbol }}" {{ ($appSettings->currency_code ?? 'PHP') === $code ? 'selected' : '' }}>
                                        {{ $code }} - {{ $name }} ({{ $symbol }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label>Currency Symbol</label>
                            <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $appSettings->currency_symbol ?? '₱') }}" placeholder="₱" style="max-width:100px;">
                            <div style="font-size:11px;color:#aaa;margin-top:4px;">Auto-filled when you pick a currency.</div>
                        </div>
                        <div class="field-group">
                            <label>Default Tax Rate (%)</label>
                            <input type="number" name="default_tax_rate" step="0.01" min="0" max="100" value="{{ old('default_tax_rate', $appSettings->default_tax_rate ?? 0) }}" style="max-width:120px;">
                        </div>
                        <div class="field-group" style="display:flex;align-items:center;gap:10px;padding-top:1.5rem;">
                            <input type="checkbox" id="show_tax" name="show_tax_on_receipt" value="1" {{ $appSettings->show_tax_on_receipt ? 'checked' : '' }}>
                            <label for="show_tax" style="margin:0;font-weight:normal;">Show tax on receipts</label>
                        </div>
                        <div class="field-group" style="grid-column:span 2;">
                            <label>Receipt Footer Note <span class="optional-tag">optional</span></label>
                            <input type="text" name="receipt_footer_note" value="{{ old('receipt_footer_note', $appSettings->receipt_footer_note) }}" placeholder="e.g. Thank you for trusting us with your dental health.">
                        </div>
                    </div>
                    <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                        <button type="submit" class="btn-primary">Save Billing Settings</button>
                    </div>
                    <script>
                    const currencySymbols = {
                        @foreach($currencies as $code => [$name, $symbol])
                        "{{ $code }}": "{{ $symbol }}",
                        @endforeach
                    };
                    function updateSymbol(code) {
                        document.getElementById('currency_symbol').value = currencySymbols[code] || '';
                    }
                    </script>
                </form>
            
                <hr style="border:none;border-top:1px solid #eef2ff;margin:1.5rem 0;">
            
                {{-- SECTION 4: DATE & APPEARANCE --}}
                <form method="POST" action="{{ route('settings.clinic') }}" class="clinic-form">
                    @csrf
                    <input type="hidden" name="section" value="appearance">
                    <h3 class="form-section-title">Date, Time & Appearance</h3>
                    <div class="form-grid">
                        <div class="field-group">
                            <label>Date Format</label>
                            <select name="date_format">
                                @foreach(['M d, Y' => 'Jul 13, 2026', 'F d, Y' => 'July 13, 2026', 'd/m/Y' => '13/07/2026', 'm/d/Y' => '07/13/2026', 'Y-m-d' => '2026-07-13'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($appSettings->date_format ?? 'M d, Y') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label>Timezone</label>
                            <select name="timezone">
                                @foreach(['Asia/Manila' => 'Philippines (GMT+8)', 'America/New_York' => 'US Eastern (GMT-5)', 'America/Los_Angeles' => 'US Pacific (GMT-8)', 'Europe/London' => 'UK (GMT)', 'Australia/Sydney' => 'Australia Eastern (GMT+10)'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($appSettings->timezone ?? 'Asia/Manila') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
            
                    <div style="margin-top:1.5rem;">
                        <label style="display:block;margin-bottom:10px;">Theme Presets</label>
                        <div style="display:flex;gap:10px;flex-wrap:wrap;" id="theme-presets">
                            @php
                            // [label, primary, secondary, bg, surface]
                            $presets = [
                                'teal-coral'  => ['Teal & Coral', '#2A9D8F', '#FF8966', '#F7F9F9', '#FFFFFF'],
                                'ocean-blue'  => ['Ocean Blue',   '#1e4a8a', '#f97316', '#F5F8FC', '#FFFFFF'],
                                'royal-purple'=> ['Royal Purple', '#6d28d9', '#ec4899', '#FAF8FD', '#FFFFFF'],
                                'fresh-green' => ['Fresh Green',  '#15803d', '#84cc16', '#F6FAF7', '#FFFFFF'],
                                'navy-gold'   => ['Navy & Gold',  '#1e293b', '#d4af37', '#F4F5F7', '#FFFFFF'],
                                'rose-slate'  => ['Rose Slate',   '#be123c', '#64748b', '#FAF7F8', '#FFFFFF'],
                                'midnight'    => ['Midnight',     '#38bdf8', '#f472b6', '#0f172a', '#1e293b'],
                                'mono'        => ['Monochrome',   '#333333', '#888888', '#F7F7F7', '#FFFFFF'],
                            ];
                            @endphp
                            @foreach($presets as $key => [$label, $p, $s, $bg, $surface])
                            <div onclick="applyPreset('{{ $key }}', '{{ $p }}', '{{ $s }}', '{{ $bg }}', '{{ $surface }}')"
                                 style="cursor:pointer;border:2px solid {{ ($appSettings->theme_preset ?? 'teal-coral') === $key ? '#333' : '#e5e5e5' }};border-radius:10px;padding:8px 10px;text-align:center;width:90px;background:{{ $bg }};"
                                 data-preset="{{ $key }}">
                                <div style="display:flex;height:24px;border-radius:5px;overflow:hidden;margin-bottom:6px;border:1px solid rgba(0,0,0,0.06);">
                                    <div style="flex:1;background:{{ $p }};"></div>
                                    <div style="flex:1;background:{{ $s }};"></div>
                                </div>
                                <div style="font-size:10px;color:#666;">{{ $label }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div style="font-size:11px;color:#aaa;margin-top:8px;">Presets set matching background, surface, and accent colors together for a balanced look. Pick one, then fine-tune below if you like.</div>
                    </div>
            
                    <div class="form-grid" style="margin-top:1.5rem;">
                        <div class="field-group">
                            <label>Primary Color <span class="optional-tag">buttons, links, active states</span></label>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <input type="color" id="primary_color" name="primary_color"
                                       value="{{ $appSettings->primary_color ?? '#2A9D8F' }}"
                                       style="width:50px;height:36px;border:none;padding:0;cursor:pointer;border-radius:5px;"
                                       oninput="document.getElementById('primary_text').value=this.value; clearPresetSelection(); updatePreview();">
                                <input type="text" id="primary_text" value="{{ $appSettings->primary_color ?? '#2A9D8F' }}"
                                       style="max-width:110px;font-family:monospace;"
                                       oninput="document.getElementById('primary_color').value=this.value; clearPresetSelection(); updatePreview();">
                            </div>
                        </div>
                        <div class="field-group">
                            <label>Secondary Color <span class="optional-tag">accents, submit buttons</span></label>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <input type="color" id="secondary_color" name="secondary_color"
                                       value="{{ $appSettings->secondary_color ?? '#FF8966' }}"
                                       style="width:50px;height:36px;border:none;padding:0;cursor:pointer;border-radius:5px;"
                                       oninput="document.getElementById('secondary_text').value=this.value; clearPresetSelection(); updatePreview();">
                                <input type="text" id="secondary_text" value="{{ $appSettings->secondary_color ?? '#FF8966' }}"
                                       style="max-width:110px;font-family:monospace;"
                                       oninput="document.getElementById('secondary_color').value=this.value; clearPresetSelection(); updatePreview();">
                            </div>
                        </div>
                        <div class="field-group">
                            <label>Background Color <span class="optional-tag">page background</span></label>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <input type="color" id="bg_color" name="bg_color"
                                       value="{{ $appSettings->bg_color ?? '#F7F9F9' }}"
                                       style="width:50px;height:36px;border:none;padding:0;cursor:pointer;border-radius:5px;"
                                       oninput="document.getElementById('bg_text').value=this.value; clearPresetSelection(); updatePreview();">
                                <input type="text" id="bg_text" value="{{ $appSettings->bg_color ?? '#F7F9F9' }}"
                                       style="max-width:110px;font-family:monospace;"
                                       oninput="document.getElementById('bg_color').value=this.value; clearPresetSelection(); updatePreview();">
                            </div>
                        </div>
                        <div class="field-group">
                            <label>Surface Color <span class="optional-tag">cards & panels</span></label>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <input type="color" id="surface_color" name="surface_color"
                                       value="{{ $appSettings->surface_color ?? '#FFFFFF' }}"
                                       style="width:50px;height:36px;border:none;padding:0;cursor:pointer;border-radius:5px;"
                                       oninput="document.getElementById('surface_text').value=this.value; clearPresetSelection(); updatePreview();">
                                <input type="text" id="surface_text" value="{{ $appSettings->surface_color ?? '#FFFFFF' }}"
                                       style="max-width:110px;font-family:monospace;"
                                       oninput="document.getElementById('surface_color').value=this.value; clearPresetSelection(); updatePreview();">
                            </div>
                        </div>
                    </div>
            
                    <div style="margin-top:1.25rem;">
                        <label style="display:block;margin-bottom:8px;">Live Preview</label>
                        <div id="theme-preview" style="border-radius:10px;padding:20px;transition:background 0.2s;">
                            <div id="preview-card" style="border-radius:8px;padding:16px;transition:background 0.2s;">
                                <div id="preview-heading" style="font-weight:700;font-size:15px;margin-bottom:4px;transition:color 0.2s;">Sample Card Heading</div>
                                <div id="preview-muted" style="font-size:12px;margin-bottom:12px;transition:color 0.2s;">Muted supporting text sits here.</div>
                                <button type="button" id="preview-btn-primary" style="border:none;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;margin-right:8px;transition:background 0.2s,color 0.2s;">Primary Button</button>
                                <button type="button" id="preview-btn-secondary" style="border:none;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;transition:background 0.2s,color 0.2s;">Secondary Button</button>
                            </div>
                        </div>
                        <div style="font-size:11px;color:#aaa;margin-top:6px;">Text colors adjust automatically for readability against whatever background you pick.</div>
                    </div>
            
                    <input type="hidden" name="theme_preset" id="theme_preset_input" value="{{ $appSettings->theme_preset ?? 'teal-coral' }}">
            
                    <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:1rem;">
                        <button type="submit" class="btn-primary">Save Appearance</button>
                    </div>
                </form>
            
                <script>
                    function hexToRgb(hex) {
                        hex = hex.replace('#', '');
                        if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
                        const num = parseInt(hex, 16);
                        return [(num >> 16) & 255, (num >> 8) & 255, num & 255];
                    }
                    function luminance([r, g, b]) {
                        const a = [r, g, b].map(v => {
                            v /= 255;
                            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
                        });
                        return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2];
                    }
                    function readableOn(hex) {
                        return luminance(hexToRgb(hex)) > 0.5 ? '#12302E' : '#FFFFFF';
                    }
            
                    function applyPreset(key, primary, secondary, bg, surface) {
                        document.getElementById('primary_color').value = primary;
                        document.getElementById('primary_text').value = primary;
                        document.getElementById('secondary_color').value = secondary;
                        document.getElementById('secondary_text').value = secondary;
                        document.getElementById('bg_color').value = bg;
                        document.getElementById('bg_text').value = bg;
                        document.getElementById('surface_color').value = surface;
                        document.getElementById('surface_text').value = surface;
                        document.getElementById('theme_preset_input').value = key;
            
                        document.querySelectorAll('#theme-presets > div').forEach(el => {
                            el.style.borderColor = el.dataset.preset === key ? '#333' : '#e5e5e5';
                        });
                        updatePreview();
                    }
                    function clearPresetSelection() {
                        document.getElementById('theme_preset_input').value = 'custom';
                        document.querySelectorAll('#theme-presets > div').forEach(el => {
                            el.style.borderColor = '#e5e5e5';
                        });
                    }
            
                    function updatePreview() {
                        const primary   = document.getElementById('primary_color').value;
                        const secondary = document.getElementById('secondary_color').value;
                        const bg        = document.getElementById('bg_color').value;
                        const surface   = document.getElementById('surface_color').value;
            
                        const ink   = readableOn(bg);
                        const muted = luminance(hexToRgb(bg)) > 0.5 ? '#6B7280' : '#A0AAB4';
            
                        document.getElementById('theme-preview').style.background = bg;
                        document.getElementById('preview-card').style.background = surface;
                        document.getElementById('preview-heading').style.color = ink;
                        document.getElementById('preview-muted').style.color = muted;
            
                        const btnP = document.getElementById('preview-btn-primary');
                        btnP.style.background = primary;
                        btnP.style.color = readableOn(primary);
            
                        const btnS = document.getElementById('preview-btn-secondary');
                        btnS.style.background = secondary;
                        btnS.style.color = readableOn(secondary);
                    }
            
                    document.addEventListener('DOMContentLoaded', updatePreview);
                </script>
            
            </div>
        @endif

    </div>

</x-clinic-layout>