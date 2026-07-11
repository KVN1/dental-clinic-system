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

        @endif

    </div>

</x-clinic-layout>