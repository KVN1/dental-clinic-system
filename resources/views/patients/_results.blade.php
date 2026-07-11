<table class="data-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($patients as $patient)
            <tr class="clickable-row" onclick="window.location='{{ route('patients.show', $patient) }}'">
                <td>
                    <div class="patient-name-cell">
                        <div class="patient-avatar">{{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}</div>
                        <span>{{ $patient->last_name }}, {{ $patient->first_name }}</span>
                    </div>
                </td>
                <td>{{ $patient->contact_number ?? '—' }}</td>
                <td>{{ $patient->email ?? '—' }}</td>
                <td>
                    @if ($patient->balance > 0)
                        <span class="balance-pill balance-due">@money($patient->balance)</span>
                    @else
                        <span class="balance-pill balance-clear">Paid up</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="empty-row">No patients found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="pagination-wrap">
    {{ $patients->links() }}
</div>