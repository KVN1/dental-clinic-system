<x-clinic-layout :title="'Appointments'">

    <div class="page-actions">
        <div></div>
        <a href="{{ route('appointments.create') }}" class="btn-primary">
            <span>+ Book Appointment</span>
        </a>
    </div>

    @if (session('success'))
        <div class="status-message" style="margin-bottom: 1.25rem;">{{ session('success') }}</div>
    @endif

    @forelse ($appointments as $date => $dayAppointments)
        <div class="panel" style="margin-bottom: 1.25rem;">
            <div class="panel-header">
                <h2 class="panel-title">
                    {{ \Carbon\Carbon::parse($date)->isToday() ? 'Today — ' : '' }}{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                </h2>
                <span class="panel-count">{{ $dayAppointments->count() }} appointment{{ $dayAppointments->count() !== 1 ? 's' : '' }}</span>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Dentist</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dayAppointments as $appt)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</td>
                            <td>
                                <a href="{{ route('patients.show', $appt->patient) }}" class="table-link">
                                    {{ $appt->patient->last_name }}, {{ $appt->patient->first_name }}
                                </a>
                            </td>
                            <td>
                                @if($appt->dentist)
                                    <span style="display:inline-flex;align-items:center;gap:6px;">
                                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $appt->dentist->color ?? 'var(--color-teal)' }};display:inline-block;"></span>
                                        {{ $appt->dentist->name }}
                                    </span>
                                @else
                                    <span style="color:var(--color-muted);">Unassigned</span>
                                @endif
                            </td>
                            <td>{{ $appt->purpose ?? '—' }}</td>
                            <td><span class="status-tag status-{{ $appt->status }}">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span></td>
<td>
                                <div class="appt-actions">
                                    <form method="POST" action="{{ route('appointments.status', $appt) }}" class="status-form">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="status-select">
                                            <option value="scheduled" {{ $appt->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="confirmed" {{ $appt->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="completed" {{ $appt->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="no_show" {{ $appt->status === 'no_show' ? 'selected' : '' }}>No-show</option>
                                            <option value="cancelled" {{ $appt->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>

                                    <a href="{{ route('appointments.edit', $appt) }}" class="row-icon-btn" title="Edit / Reschedule">✎</a>

                                    <form method="POST" action="{{ route('appointments.destroy', $appt) }}" onsubmit="return confirm('Delete this appointment permanently?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="row-icon-btn row-icon-danger" title="Delete">🗑</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="panel">
            <p class="empty-row">No upcoming appointments. <a href="{{ route('appointments.create') }}">Book one →</a></p>
        </div>
    @endforelse

</x-clinic-layout>