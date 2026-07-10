<x-clinic-layout :title="'Dashboard'">

    <div class="welcome-banner">
        <div>
            <h2 class="font-display welcome-heading">
                @php $hour = now()->hour; @endphp
                {{ $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening') }}, {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h2>
            <p class="welcome-subtext">Here's what's happening at Crosby Dental Clinic today, {{ now()->format('l, F j') }}.</p>
        </div>
    </div>

    <div class="quick-actions">
        <a href="{{ route('patients.create') }}" class="quick-action-card">
            <span class="quick-action-icon">＋</span>
            <span class="quick-action-label">Add Patient</span>
        </a>
        <a href="{{ route('appointments.create') }}" class="quick-action-card">
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

    <div class="dashboard-columns">

        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">Today's Appointments</h2>
                <a href="{{ route('appointments.index') }}" class="panel-link">View all →</a>
            </div>

            @forelse ($todaysAppointmentsList as $appt)
                <div class="appt-row">
                    <div class="appt-time">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</div>
                    <div class="appt-info">
                        <a href="{{ route('patients.show', $appt->patient) }}" class="table-link">{{ $appt->patient->last_name }}, {{ $appt->patient->first_name }}</a>
                        <div class="appt-purpose">{{ $appt->purpose ?? 'General visit' }}</div>
                    </div>
                    <span class="status-tag status-{{ $appt->status }}">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span>
                </div>
            @empty
                <p class="empty-row">No appointments scheduled for today. <a href="{{ route('appointments.create') }}">Book one →</a></p>
            @endforelse
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">Recently Added Patients</h2>
                <a href="{{ route('patients.index') }}" class="panel-link">View all →</a>
            </div>

            @forelse ($recentPatients as $patient)
                <div class="appt-row">
                    <div class="patient-avatar">{{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}</div>
                    <div class="appt-info">
                        <a href="{{ route('patients.show', $patient) }}" class="table-link">{{ $patient->last_name }}, {{ $patient->first_name }}</a>
                        <div class="appt-purpose">{{ $patient->contact_number ?? 'No contact on file' }}</div>
                    </div>
                    @if ($patient->balance > 0)
                        <span class="balance-pill balance-due">@money($patient->balance)</span>
                    @else
                        <span class="balance-pill balance-clear">Paid up</span>
                    @endif
                </div>
            @empty
                <p class="empty-row">No patients yet. <a href="{{ route('patients.create') }}">Add your first patient →</a></p>
            @endforelse
        </div>

    </div>

</x-clinic-layout>