<x-clinic-layout :title="'Dashboard'">

    <div class="welcome-banner">
        <div>
            <h2 class="font-display welcome-heading">
                @php $hour = now()->hour; @endphp
                {{ $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening') }}, {{ explode(' ', auth()->user()->name)[0] }}
            </h2>
            <p class="welcome-subtext">Here's what's happening today, {{ now()->format('l, F j') }}.</p>
        </div>
    </div>

    <!-- Key Stats -->
    <div class="stats-grid">
        <div class="stat-card stat-accent-teal">
            <div class="stat-label">Total Patients</div>
            <div class="stat-value">{{ $totalPatients }}</div>
        </div>

        <div class="stat-card stat-accent-coral">
            <div class="stat-label">Outstanding Balance</div>
            <div class="stat-value">@money($totalBalance)</div>
        </div>

        <div class="stat-card stat-accent-teal">
            <div class="stat-label">Today's Appointments</div>
            <div class="stat-value">{{ $todaysAppointments }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div>
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-muted);margin-bottom:10px;">
            Quick Actions
        </div>
        <div class="quick-actions">
            <a href="{{ route('patients.create') }}" class="quick-action-card">
                <span class="quick-action-icon">+</span>
                <span class="quick-action-label">Add Patient</span>
            </a>
            <a href="{{ route('appointments.index') }}" class="quick-action-card">
                <span class="quick-action-icon">▤</span>
                <span class="quick-action-label">Book Appointment</span>
            </a>
            <a href="{{ route('patients.index') }}" class="quick-action-card">
                <span class="quick-action-icon">◍</span>
                <span class="quick-action-label">View Patients</span>
            </a>
            <a href="{{ route('logs.index') }}" class="quick-action-card">
                <span class="quick-action-icon">⏱</span>
                <span class="quick-action-label">Recent Logs</span>
            </a>
        </div>
    </div>

    <!-- Today's Appointments + Reminders side by side -->
    <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:1.25rem;margin-top:1.75rem;align-items:start;">

        <div class="panel" style="margin:0;">
            <div class="panel-header">
                <h2 class="panel-title">Today's Appointments</h2>
                <a href="{{ route('appointments.index') }}" class="panel-link">View all →</a>
            </div>

            @forelse ($todaysAppointmentsList as $appt)
                <div class="appt-row">
                    <div class="appt-time">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</div>
                    <div class="appt-info">
                        <a href="{{ route('patients.show', $appt->patient) }}" class="table-link">{{ $appt->patient->last_name }}, {{ $appt->patient->first_name }}</a>
                        <div class="appt-purpose">
                            {{ $appt->purpose ?? 'General visit' }}
                            @if($appt->dentist)
                                <span style="color:var(--color-muted);"> &middot; {{ $appt->dentist->name }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="status-tag status-{{ $appt->status }}">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span>
                </div>
            @empty
                <p class="empty-row">No appointments scheduled for today. <a href="{{ route('appointments.index') }}">Book one →</a></p>
            @endforelse
        </div>

        <div class="panel" id="reminders" style="margin:0;scroll-margin-top:20px;">
            <div class="panel-header">
                <h2 class="panel-title">Reminders</h2>
            </div>

            <div style="display:flex;flex-direction:column;gap:18px;">

            <!-- This Week's Appointments -->
            <div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-muted);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--color-border, #E7ECEB);">
                    This Week &middot; {{ ($weekAppointments ?? collect())->count() }}
                </div>
                @forelse(($weekAppointments ?? collect())->take(3) as $appt)
                    <a href="{{ route('patients.show', $appt->patient) }}" style="display:block;padding:9px 10px;border-radius:8px;background:var(--color-bg);margin-bottom:6px;text-decoration:none;color:inherit;border-left:3px solid {{ $appt->dentist->color ?? 'var(--color-teal)' }};">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-size:12px;font-weight:600;color:var(--color-ink);">{{ $appt->patient->last_name }}, {{ $appt->patient->first_name }}</span>
                            <span style="font-size:11px;color:var(--color-muted);flex-shrink:0;margin-left:8px;">{{ $appt->appointment_date->format('M d') }}</span>
                        </div>
                        <div style="font-size:11px;color:var(--color-muted);margin-top:2px;">
                            {{ $appt->appointment_time ? \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') : '' }}
                            {{ $appt->purpose ? ' · '.$appt->purpose : '' }}
                        </div>
                    </a>
                @empty
                    <p style="font-size:12px;color:var(--color-muted);padding:10px 0;">Nothing scheduled this week.</p>
                @endforelse
                @if(($weekAppointments ?? collect())->count() > 5)
                    <a href="{{ route('appointments.index') }}" class="panel-link" style="font-size:12px;">View all →</a>
                @endif
            </div>

            <!-- Outstanding Balances -->
            <div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-muted);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--color-border, #E7ECEB);">
                    Outstanding Balances &middot; {{ ($overdueBalances ?? collect())->count() }}
                </div>
                @forelse(($overdueBalances ?? collect())->take(3) as $patient)
                    <a href="{{ route('patients.show', $patient) }}" style="display:flex;justify-content:space-between;align-items:center;padding:9px 10px;border-radius:8px;background:var(--color-bg);margin-bottom:6px;text-decoration:none;color:inherit;">
                        <span style="font-size:12px;font-weight:600;color:var(--color-ink);">{{ $patient->last_name }}, {{ $patient->first_name }}</span>
                        <span style="font-size:12px;font-weight:700;color:#D9534F;">@money($patient->balance)</span>
                    </a>
                @empty
                    <p style="font-size:12px;color:var(--color-muted);padding:10px 0;">No outstanding balances.</p>
                @endforelse
            </div>

            <!-- Prescriptions Needing Follow-up -->
            <div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-muted);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--color-border, #E7ECEB);">
                    Prescriptions to Review &middot; {{ ($stalePrescriptions ?? collect())->count() }}
                </div>
                @forelse(($stalePrescriptions ?? collect())->take(3) as $rx)
                    <a href="{{ route('patients.show', $rx->patient) }}" style="display:block;padding:9px 10px;border-radius:8px;background:var(--color-bg);margin-bottom:6px;text-decoration:none;color:inherit;">
                        <div style="font-size:12px;font-weight:600;color:var(--color-ink);">{{ $rx->patient->last_name }}, {{ $rx->patient->first_name }}</div>
                        <div style="font-size:11px;color:var(--color-muted);margin-top:2px;">
                            Issued {{ $rx->date_issued->format('M d, Y') }} &middot; {{ $rx->date_issued->diffForHumans() }}
                        </div>
                    </a>
                @empty
                    <p style="font-size:12px;color:var(--color-muted);padding:10px 0;">Nothing needs review.</p>
                @endforelse
            </div>

            <!-- Recently Rescheduled -->
            <div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--color-muted);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--color-border, #E7ECEB);">
                    Recently Rescheduled &middot; {{ ($recentReschedules ?? collect())->count() }}
                </div>
                @forelse(($recentReschedules ?? collect()) as $r)
                    <a href="{{ route('appointments.edit', $r->appointment) }}" style="display:block;padding:9px 10px;border-radius:8px;background:var(--color-bg);margin-bottom:6px;text-decoration:none;color:inherit;border-left:3px solid #F97316;">
                        <div style="font-size:12px;font-weight:600;color:var(--color-ink);">
                            {{ $r->appointment->patient->last_name ?? 'Unknown' }}, {{ $r->appointment->patient->first_name ?? '' }}
                        </div>
                        <div style="font-size:11px;color:var(--color-muted);margin-top:2px;">
                            {{ $r->old_date->format('M d') }} &rarr; {{ \Carbon\Carbon::parse($r->new_date)->format('M d, g:i A') }}
                            @if($r->reason)
                                <br><span style="color:#C2410C;">{{ $r->reason }}</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <p style="font-size:12px;color:var(--color-muted);padding:10px 0;">No recent reschedules.</p>
                @endforelse
            </div>

            </div>
        </div>

    </div>

</x-clinic-layout>
