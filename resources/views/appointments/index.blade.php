<x-clinic-layout :title="'Appointments'">

    <div class="page-actions">
        <form method="GET" action="{{ route('appointments.index') }}" class="filter-bar" style="margin:0;">
            <div class="field-group" style="margin:0;">
                <label for="dentist_filter" style="font-size:11px;">Dentist</label>
                <select id="dentist_filter" name="dentist_id" onchange="this.form.submit()">
                    <option value="">All Dentists</option>
                    @foreach($dentists as $dentist)
                        <option value="{{ $dentist->id }}" {{ (string)$filterDentistId === (string)$dentist->id ? 'selected' : '' }}>
                            {{ $dentist->name }}{{ $dentist->specialty ? ' — '.$dentist->specialty : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
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

<div class="panel" style="margin-top:2rem;" x-data="appointmentCalendar()" x-init="init()">

    <div class="panel-header">
        <h2 class="panel-title">Calendar View</h2>
        <div style="display:flex;align-items:center;gap:10px;">
            <button type="button" class="row-icon-btn" @click="prevMonth()" title="Previous month">&larr;</button>
            <span style="font-weight:600;font-size:14px;min-width:140px;text-align:center;" x-text="monthLabel"></span>
            <button type="button" class="row-icon-btn" @click="nextMonth()" title="Next month">&rarr;</button>
            <button type="button" class="btn-secondary" style="font-size:12px;padding:5px 12px;" @click="goToday()">Today</button>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:1px;background:var(--color-border,#E7ECEB);border:1px solid var(--color-border,#E7ECEB);border-radius:10px;overflow:hidden;margin-top:1rem;">

        <template x-for="d in ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']">
            <div style="background:var(--color-bg);padding:8px;text-align:center;font-size:11px;font-weight:700;color:var(--color-muted);text-transform:uppercase;letter-spacing:0.05em;" x-text="d"></div>
        </template>

        <template x-for="cell in calendarCells" :key="cell.key">
            <div
                @click="cell.inMonth && openDay(cell.dateStr)"
                :style="{
                    background: cell.inMonth ? 'var(--color-surface)' : 'var(--color-bg)',
                    opacity: cell.inMonth ? 1 : 0.4,
                    cursor: cell.inMonth ? 'pointer' : 'default',
                    minHeight: '86px',
                    padding: '8px',
                    position: 'relative',
                    transition: 'background 0.15s ease',
                    outline: cell.isToday ? '2px solid var(--color-teal)' : 'none',
                    outlineOffset: '-2px'
                }"
                @mouseenter="cell.inMonth && ($el.style.background = 'color-mix(in srgb, var(--color-ink) 4%, var(--color-surface))')"
                @mouseleave="cell.inMonth && ($el.style.background = 'var(--color-surface)')"
            >
                <div style="font-size:12px;font-weight:600;color:var(--color-ink);margin-bottom:4px;" x-text="cell.day"></div>

                <div style="display:flex;flex-direction:column;gap:3px;">
                    <template x-for="appt in (days[cell.dateStr] || []).slice(0, 3)" :key="appt.id">
                        <div style="font-size:10px;padding:2px 5px;border-radius:4px;background:color-mix(in srgb, var(--color-ink) 6%, var(--color-surface));display:flex;align-items:center;gap:4px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
                            <span :style="{width:'6px',height:'6px',borderRadius:'50%',background:appt.dentist_color,flexShrink:0}"></span>
                            <span x-text="appt.time" style="flex-shrink:0;color:var(--color-muted);"></span>
                            <span x-text="appt.patient" style="overflow:hidden;text-overflow:ellipsis;"></span>
                        </div>
                    </template>
                    <div x-show="(days[cell.dateStr] || []).length > 3" style="font-size:10px;color:var(--color-muted);padding-left:2px;" x-text="'+' + ((days[cell.dateStr] || []).length - 3) + ' more'"></div>
                </div>
            </div>
        </template>

    </div>

    <!-- Day detail modal -->
    <div x-show="modalOpen" x-cloak
         style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:center;justify-content:center;padding:20px;"
         @click.self="modalOpen = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">

        <div style="background:var(--color-surface);border-radius:14px;max-width:480px;width:100%;max-height:80vh;overflow-y:auto;padding:24px;box-shadow:0 20px 60px rgba(0,0,0,0.3);">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="font-size:16px;font-weight:700;color:var(--color-ink);margin:0;" x-text="modalDateLabel"></h3>
                <button type="button" @click="modalOpen = false" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--color-muted);line-height:1;">&times;</button>
            </div>

            <div x-show="(days[modalDate] || []).length === 0" style="text-align:center;padding:24px 0;color:var(--color-muted);font-size:13px;">
                No appointments on this day.
            </div>

            <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:18px;">
                <template x-for="appt in (days[modalDate] || [])" :key="appt.id">
                    <a :href="appt.edit_url" style="display:block;padding:10px 12px;border-radius:8px;background:var(--color-bg);text-decoration:none;color:inherit;border-left:3px solid;" :style="{borderLeftColor: appt.dentist_color}">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-weight:600;font-size:13px;color:var(--color-ink);" x-text="appt.time"></span>
                            <span class="status-tag" :class="'status-' + appt.status" style="font-size:10px;" x-text="appt.status"></span>
                        </div>
                        <div style="font-size:13px;color:var(--color-ink);margin-top:2px;" x-text="appt.patient"></div>
                        <div style="font-size:11px;color:var(--color-muted);margin-top:2px;" x-text="(appt.dentist || 'Unassigned') + (appt.purpose ? ' \u00b7 ' + appt.purpose : '')"></div>
                    </a>
                </template>
            </div>

            <a :href="'{{ route('appointments.create') }}' + '?date=' + modalDate" class="btn-primary" style="width:100%;text-align:center;justify-content:center;display:flex;">
                + Book Appointment for This Day
            </a>

        </div>
    </div>

</div>

<script>
function appointmentCalendar() {
    return {
        currentMonth: {{ now()->month }},
        currentYear: {{ now()->year }},
        monthLabel: '',
        calendarCells: [],
        days: {},
        modalOpen: false,
        modalDate: null,
        modalDateLabel: '',

        init() {
            this.loadMonth();
        },

        monthName(m) {
            return ['January','February','March','April','May','June','July','August','September','October','November','December'][m - 1];
        },

        async loadMonth() {
            this.monthLabel = this.monthName(this.currentMonth) + ' ' + this.currentYear;
            this.buildCells();

            try {
                const res = await fetch(`{{ route('appointments.calendar.data') }}?month=${this.currentMonth}&year=${this.currentYear}`);
                const data = await res.json();
                this.days = data.days || {};
            } catch (e) {
                console.error('Failed to load calendar data', e);
            }
        },

        buildCells() {
            const first = new Date(this.currentYear, this.currentMonth - 1, 1);
            const startWeekday = first.getDay();
            const daysInMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
            const daysInPrevMonth = new Date(this.currentYear, this.currentMonth - 1, 0).getDate();

            const todayStr = new Date().toISOString().split('T')[0];
            const cells = [];

            for (let i = startWeekday - 1; i >= 0; i--) {
                const day = daysInPrevMonth - i;
                cells.push({ key: 'prev-' + day, day, inMonth: false, dateStr: null, isToday: false });
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const m = String(this.currentMonth).padStart(2, '0');
                const d = String(day).padStart(2, '0');
                const dateStr = `${this.currentYear}-${m}-${d}`;
                cells.push({ key: 'cur-' + day, day, inMonth: true, dateStr, isToday: dateStr === todayStr });
            }

            const remaining = 42 - cells.length;
            for (let day = 1; day <= remaining; day++) {
                cells.push({ key: 'next-' + day, day, inMonth: false, dateStr: null, isToday: false });
            }

            this.calendarCells = cells;
        },

        prevMonth() {
            this.currentMonth--;
            if (this.currentMonth < 1) { this.currentMonth = 12; this.currentYear--; }
            this.loadMonth();
        },

        nextMonth() {
            this.currentMonth++;
            if (this.currentMonth > 12) { this.currentMonth = 1; this.currentYear++; }
            this.loadMonth();
        },

        goToday() {
            const now = new Date();
            this.currentMonth = now.getMonth() + 1;
            this.currentYear = now.getFullYear();
            this.loadMonth();
        },

        openDay(dateStr) {
            this.modalDate = dateStr;
            const d = new Date(dateStr + 'T00:00:00');
            this.modalDateLabel = d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            this.modalOpen = true;
        }
    }
}
</script>

</x-clinic-layout>