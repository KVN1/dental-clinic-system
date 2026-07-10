<x-clinic-layout :title="'Patients'">

    <div class="page-actions">
        <div class="search-box">
            <input type="text" placeholder="Search patients..." disabled>
        </div>
        <a href="{{ route('patients.create') }}" class="btn-primary">
            <span>+ Add Patient</span>
        </a>
    </div>

    @if (session('success'))
        <div class="status-message" style="margin-bottom: 1.25rem;">{{ session('success') }}</div>
    @endif

    <div class="panel">
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
                                <span class="balance-pill balance-due">₱{{ number_format($patient->balance, 2) }}</span>
                            @else
                                <span class="balance-pill balance-clear">Paid up</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-row">No patients yet. <a href="{{ route('patients.create') }}">Add your first patient →</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-clinic-layout>