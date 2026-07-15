<x-clinic-layout :title="'All Logs'">

    <div class="panel" style="margin-bottom: 1.25rem;">
        <form method="GET" action="{{ route('logs.index') }}" class="filter-bar">
<div class="field-group">
                <label for="patient_search">Patient</label>
                <input
                    id="patient_search"
                    type="text"
                    list="patient-options"
                    placeholder="Search patient..."
                    value="{{ request('patient_id') ? $patients->firstWhere('id', request('patient_id'))?->last_name . ', ' . $patients->firstWhere('id', request('patient_id'))?->first_name : '' }}"
                    oninput="matchPatient(this.value)"
                    autocomplete="off"
                >
                <datalist id="patient-options">
                    @foreach ($patients as $patient)
                        <option data-id="{{ $patient->id }}" value="{{ $patient->last_name }}, {{ $patient->first_name }}"></option>
                    @endforeach
                </datalist>
                <input type="hidden" id="patient_id" name="patient_id" value="{{ request('patient_id') }}">
            </div>

            <div class="field-group">
                <label for="entry_type">Type</label>
                <select id="entry_type" name="entry_type">
                    <option value="">All Types</option>
                    <option value="visit" {{ request('entry_type') === 'visit' ? 'selected' : '' }}>Visit</option>
                    <option value="payment" {{ request('entry_type') === 'payment' ? 'selected' : '' }}>Payment</option>
                    <option value="note" {{ request('entry_type') === 'note' ? 'selected' : '' }}>Note</option>
                </select>
            </div>

            <div class="field-group">
                <label for="date_from">From</label>
                <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}">
            </div>

            <div class="field-group">
                <label for="date_to">To</label>
                <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}">
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-primary">Filter</button>
                @if (request()->anyFilled(['patient_id', 'entry_type', 'date_from', 'date_to']))
                    <a href="{{ route('logs.index') }}" class="btn-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="panel">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Charged</th>
                    <th>Paid</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="clickable-row" onclick="window.location='{{ route('patients.show', $log->patient) }}'">
                        <td>{{ \Carbon\Carbon::parse($log->visit_date)->format('M d, Y') }}</td>
                        <td>{{ $log->patient->last_name }}, {{ $log->patient->first_name }}</td>
                        <td><span class="timeline-type-tag timeline-type-{{ $log->entry_type }}">{{ ucfirst($log->entry_type) }}</span></td>
                        <td>{{ \Illuminate\Support\Str::limit($log->description, 50) }}</td>
                        <td>@if ($log->amount_charged > 0) @money($log->amount_charged) @else — @endif</td>
                        <td>@if ($log->amount_paid > 0) @money($log->amount_paid) @else — @endif</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">No logs match your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrap">
            {{ $logs->links() }}
        </div>
    </div>
<script>
        const patientMap = {
            @foreach ($patients as $patient)
                "{{ $patient->last_name }}, {{ $patient->first_name }}": {{ $patient->id }},
            @endforeach
        };

        function matchPatient(value) {
            const hiddenInput = document.getElementById('patient_id');
            if (patientMap.hasOwnProperty(value)) {
                hiddenInput.value = patientMap[value];
            } else {
                hiddenInput.value = '';
            }
        }
    </script>


</x-clinic-layout>