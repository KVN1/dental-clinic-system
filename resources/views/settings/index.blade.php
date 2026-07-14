<x-clinic-layout :title="'Settings'">

    @if (session('success'))
        <div class="status-message" style="margin-bottom: 1.25rem;">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="field-error" style="margin-bottom: 1.25rem; background: #FDECEA; padding: 0.75rem 1rem; border-radius: 0.6rem;">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="panel" x-data="{ tab: 'appearance' }">

        <div class="settings-tabs">
            <button type="button" class="settings-tab" :class="tab === 'appearance' && 'active'" @click="tab = 'appearance'">Appearance</button>
            <button type="button" class="settings-tab" :class="tab === 'exports' && 'active'" @click="tab = 'exports'">Export Data</button>
            @if (auth()->user()->isAdmin())
                <button type="button" class="settings-tab" :class="tab === 'backups' && 'active'" @click="tab = 'backups'">Backups</button>
                <button type="button" class="settings-tab" :class="tab === 'staff' && 'active'" @click="tab = 'staff'">Staff Accounts</button>
                <button type="button" class="settings-tab" :class="tab === 'clinic' && 'active'" @click="tab = 'clinic'">Clinic Settings</button>
            @endif
        </div>

        <!-- ===== Clinic Settings Tab ===== -->
<div x-show="tab === 'clinic'" class="settings-panel">

    {{-- SECTION 1: CLINIC IDENTITY --}}
    <form method="POST" action="{{ route('settings.clinic') }}" enctype="multipart/form-data" class="clinic-form">
        @csrf
        <input type="hidden" name="section" value="identity">
        <h3 class="form-section-title">Clinic Identity</h3>
        <div class="field-group" style="margin-bottom:1.25rem;">
            <label>Clinic Logo</label>
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                @if($appSettings->logo)
                    <img src="{{ asset('storage/' . $appSettings->logo) }}" style="height:60px;border-radius:8px;border:1px solid #dce6f7;padding:4px;background:#fff;">
                    <a href="{{ route('settings.clinic.remove-logo') }}" onclick="return confirm('Remove logo?')" style="color:#D96A48;font-size:13px;text-decoration:none;">Remove</a>
                @else
                    <div style="height:60px;width:60px;border-radius:10px;background:#dce6f7;display:flex;align-items:center;justify-content:center;font-size:26px;color:#1e4a8a;font-weight:bold;">
                        {{ strtoupper(substr($appSettings->clinic_name ?? 'D', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <input type="file" name="logo" accept="image/*" style="font-size:13px;">
                    <div style="font-size:11px;color:#aaa;margin-top:4px;">PNG, JPG, SVG. Max 2MB.</div>
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
            <div class="field-group">
                <label>Primary Color</label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" id="primary_color" name="primary_color"
                           value="{{ $appSettings->primary_color ?? '#1e4a8a' }}"
                           style="width:50px;height:36px;border:none;padding:0;cursor:pointer;border-radius:5px;"
                           oninput="document.getElementById('color_text').value=this.value">
                    <input type="text" id="color_text" value="{{ $appSettings->primary_color ?? '#1e4a8a' }}"
                           style="max-width:110px;font-family:monospace;"
                           oninput="document.getElementById('primary_color').value=this.value">
                </div>
            </div>
        </div>
        <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
            <button type="submit" class="btn-primary">Save Appearance</button>
        </div>
    </form>

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
                                <td><span class="status-tag {{ $u->role === 'admin' ? 'status-completed' : 'status-scheduled' }}">{{ ucfirst($u->role) }}</span></td>
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
                <form method="POST" action="{{ route('settings.users.store') }}" class="clinic-form">
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
                            <input id="password" type="password" name="password" required minlength="8">
                        </div>
                        <div class="field-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-footer" style="justify-content: flex-start;">
                        <button type="submit" class="btn-primary">Create Account</button>
                    </div>
                </form>
            </div>


            <!-- ===== Clinic Settings ===== -->
            <div x-show="tab === 'clinic'" class="settings-panel">

                @if (session('success'))
                    <div class="status-message" style="margin-bottom:1rem;">{{ session('success') }}</div>
                @endif

                {{-- SECTION 1: Identity --}}
                <form method="POST" action="{{ route('settings.clinic') }}" enctype="multipart/form-data" class="clinic-form">
                    @csrf
                    <input type="hidden" name="section" value="identity">
                    <h3 class="form-section-title">Clinic Identity</h3>

                    <div class="field-group" style="margin-bottom:1.25rem;">
                        <label>Logo</label>
                        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                            @if($appSettings->logo)
                                <img src="{{ asset('storage/' . $appSettings->logo) }}" style="height:56px;border-radius:8px;border:1px solid #dce6f7;padding:4px;background:#fff;" alt="Logo">
                                <a href="{{ route('settings.clinic.remove-logo') }}" onclick="return confirm('Remove logo?')" style="color:#D96A48;font-size:13px;text-decoration:none;">Remove</a>
                            @else
                                <div style="height:56px;width:90px;border:2px dashed #dce6f7;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:11px;">No Logo</div>
                            @endif
                            <div>
                                <input type="file" name="logo" accept="image/*" style="font-size:13px;">
                                <div style="font-size:11px;color:#aaa;margin-top:3px;">PNG, JPG, SVG. Max 2MB.</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label>Clinic Name</label>
                            <input type="text" name="clinic_name" value="{{ $appSettings->clinic_name ?? 'Clear Smile Dental Clinic' }}" placeholder="Clinic name">
                        </div>
                        <div class="field-group">
                            <label>Tagline <span class="optional-tag">optional</span></label>
                            <input type="text" name="tagline" value="{{ $appSettings->tagline }}" placeholder="e.g. Your Smile, Our Priority">
                        </div>
                    </div>
                    <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                        <button type="submit" class="btn-primary">Save Identity</button>
                    </div>
                </form>

                <hr style="border:none;border-top:1px solid #eef2ff;margin:1.5rem 0;">

                {{-- SECTION 2: Contact --}}
                <form method="POST" action="{{ route('settings.clinic') }}" class="clinic-form">
                    @csrf
                    <input type="hidden" name="section" value="contact">
                    <h3 class="form-section-title">Contact Information</h3>
                    <div class="form-grid">
                        <div class="field-group" style="grid-column:span 2;">
                            <label>Address</label>
                            <input type="text" name="address" value="{{ $appSettings->address }}" placeholder="123 Main St, City">
                        </div>
                        <div class="field-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="{{ $appSettings->phone }}" placeholder="+63 912 345 6789">
                        </div>
                        <div class="field-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ $appSettings->email }}" placeholder="clinic@email.com">
                        </div>
                        <div class="field-group">
                            <label>Website <span class="optional-tag">optional</span></label>
                            <input type="text" name="website" value="{{ $appSettings->website }}" placeholder="https://yourclinic.com">
                        </div>
                        <div class="field-group">
                            <label>TIN <span class="optional-tag">optional</span></label>
                            <input type="text" name="tin" value="{{ $appSettings->tin }}" placeholder="000-000-000-000">
                        </div>
                    </div>
                    <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                        <button type="submit" class="btn-primary">Save Contact</button>
                    </div>
                </form>

                <hr style="border:none;border-top:1px solid #eef2ff;margin:1.5rem 0;">

                {{-- SECTION 3: Currency & Billing --}}
                <form method="POST" action="{{ route('settings.clinic') }}" class="clinic-form">
                    @csrf
                    <input type="hidden" name="section" value="billing">
                    <h3 class="form-section-title">Currency & Billing</h3>
                    <div class="form-grid">
                        <div class="field-group">
                            <label>Currency</label>
                            <select name="currency_code" onchange="updateSymbol(this.value)">
                                @php
                                $currencies = [
                                    'PHP' => ['symbol' => '₱', 'name' => 'Philippine Peso'],
                                    'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
                                    'EUR' => ['symbol' => '€', 'name' => 'Euro'],
                                    'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
                                    'AUD' => ['symbol' => 'A$', 'name' => 'Australian Dollar'],
                                    'CAD' => ['symbol' => 'C$', 'name' => 'Canadian Dollar'],
                                    'SGD' => ['symbol' => 'S$', 'name' => 'Singapore Dollar'],
                                    'JPY' => ['symbol' => '¥', 'name' => 'Japanese Yen'],
                                    'CNY' => ['symbol' => '¥', 'name' => 'Chinese Yuan'],
                                    'KRW' => ['symbol' => '₩', 'name' => 'South Korean Won'],
                                    'INR' => ['symbol' => '₹', 'name' => 'Indian Rupee'],
                                    'MYR' => ['symbol' => 'RM', 'name' => 'Malaysian Ringgit'],
                                    'IDR' => ['symbol' => 'Rp', 'name' => 'Indonesian Rupiah'],
                                    'THB' => ['symbol' => '฿', 'name' => 'Thai Baht'],
                                    'VND' => ['symbol' => '₫', 'name' => 'Vietnamese Dong'],
                                    'HKD' => ['symbol' => 'HK$', 'name' => 'Hong Kong Dollar'],
                                    'TWD' => ['symbol' => 'NT$', 'name' => 'Taiwan Dollar'],
                                    'SAR' => ['symbol' => '﷼', 'name' => 'Saudi Riyal'],
                                    'AED' => ['symbol' => 'د.إ', 'name' => 'UAE Dirham'],
                                    'NZD' => ['symbol' => 'NZ$', 'name' => 'New Zealand Dollar'],
                                    'CHF' => ['symbol' => 'Fr', 'name' => 'Swiss Franc'],
                                    'ZAR' => ['symbol' => 'R', 'name' => 'South African Rand'],
                                    'BRL' => ['symbol' => 'R$', 'name' => 'Brazilian Real'],
                                    'MXN' => ['symbol' => 'MX$', 'name' => 'Mexican Peso'],
                                ];
                                @endphp
                                @foreach($currencies as $code => $info)
                                    <option value="{{ $code }}"
                                        data-symbol="{{ $info['symbol'] }}"
                                        {{ ($appSettings->currency_code ?? 'PHP') === $code ? 'selected' : '' }}>
                                        {{ $code }} &ndash; {{ $info['name'] }} ({{ $info['symbol'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-group">
                            <label>Currency Symbol</label>
                            <input type="text" name="currency_symbol" id="currency_symbol_input"
                                   value="{{ $appSettings->currency_symbol ?? '₱' }}"
                                   placeholder="₱" style="max-width:100px;">
                            <div style="font-size:11px;color:#aaa;margin-top:3px;">Auto-fills from currency selection. Override if needed.</div>
                        </div>
                        <div class="field-group">
                            <label>Tax Rate (%)</label>
                            <input type="number" name="default_tax_rate" step="0.01" min="0" max="100"
                                   value="{{ $appSettings->default_tax_rate ?? 0 }}" style="max-width:120px;">
                        </div>
                        <div class="field-group" style="display:flex;align-items:center;gap:10px;padding-top:1.5rem;">
                            <input type="checkbox" id="show_tax" name="show_tax_on_receipt" value="1"
                                   {{ ($appSettings->show_tax_on_receipt ?? false) ? 'checked' : '' }}>
                            <label for="show_tax" style="margin:0;font-weight:normal;">Show tax on receipts</label>
                        </div>
                    </div>

                    <div class="field-group" style="margin-top:1rem;">
                        <label>Accepted Payment Methods</label>
                        <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:6px;">
                            @foreach(['Cash', 'GCash', 'Maya', 'Bank Transfer', 'Credit Card', 'Cheque', 'PayMongo'] as $method)
                                <label style="display:flex;align-items:center;gap:5px;font-weight:normal;font-size:13px;cursor:pointer;">
                                    <input type="checkbox" name="payment_methods[]" value="{{ $method }}"
                                           {{ in_array($method, $appSettings->payment_methods ?? ['Cash']) ? 'checked' : '' }}>
                                    {{ $method }}
                                </label>
                            @endforeach
                        </div>
                        <div style="font-size:11px;color:#aaa;margin-top:4px;">Online payments (GCash, Maya, etc.) will prompt for a reference number at checkout.</div>
                    </div>

                    <div class="field-group" style="margin-top:1rem;">
                        <label>Receipt Footer Note <span class="optional-tag">optional</span></label>
                        <input type="text" name="receipt_footer_note"
                               value="{{ $appSettings->receipt_footer_note }}"
                               placeholder="e.g. Thank you for trusting us with your dental health.">
                    </div>

                    <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                        <button type="submit" class="btn-primary">Save Billing</button>
                    </div>

                    <script>
                        const currencySymbols = @json(array_combine(array_keys($currencies), array_column($currencies, 'symbol')));
                        function updateSymbol(code) {
                            document.getElementById('currency_symbol_input').value = currencySymbols[code] || '';
                        }
                    </script>
                </form>

                <hr style="border:none;border-top:1px solid #eef2ff;margin:1.5rem 0;">

                {{-- SECTION 4: Appearance --}}
                <form method="POST" action="{{ route('settings.clinic') }}" class="clinic-form">
                    @csrf
                    <input type="hidden" name="section" value="appearance">
                    <h3 class="form-section-title">Appearance</h3>
                    <div class="form-grid">
                        <div class="field-group">
                            <label>Primary Color</label>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <input type="color" name="primary_color" id="pc"
                                       value="{{ $appSettings->primary_color ?? '#1e4a8a' }}"
                                       style="width:48px;height:36px;border:none;padding:0;cursor:pointer;border-radius:5px;"
                                       oninput="document.getElementById('pct').value=this.value">
                                <input type="text" id="pct"
                                       value="{{ $appSettings->primary_color ?? '#1e4a8a' }}"
                                       style="max-width:110px;font-family:monospace;"
                                       oninput="document.getElementById('pc').value=this.value">
                            </div>
                        </div>
                    </div>
                    <div class="form-footer" style="justify-content:flex-start;border-top:none;padding-top:0.5rem;">
                        <button type="submit" class="btn-primary">Save Appearance</button>
                    </div>
                </form>

            </div>
        @endif

    </div>

</x-clinic-layout>