<x-clinic-layout :title="'Billing'">

    <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-label">Today's Revenue</div>
            <div class="stat-value">₱{{ number_format($todayRevenue, 2) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">This Month's Revenue</div>
            <div class="stat-value">₱{{ number_format($monthRevenue, 2) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Outstanding</div>
            <div class="stat-value">₱{{ number_format($totalOutstanding, 2) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">All-Time Revenue</div>
            <div class="stat-value">₱{{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-note">of ₱{{ number_format($totalCharged, 2) }} charged</div>
        </div>
    </div>

    <div class="panel" style="margin-bottom: 1.5rem;">
        <div class="panel-header">
            <h2 class="panel-title">Outstanding Balances</h2>
            <span class="panel-count">{{ $outstandingPatients->count() }} patient{{ $outstandingPatients->count() !== 1 ? 's' : '' }}</span>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Contact</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($outstandingPatients as $patient)
                    <tr class="clickable-row" onclick="window.location='{{ route('patients.show', $patient) }}'">
                        <td>
                            <div class="patient-name-cell">
                                <div class="patient-avatar">{{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}</div>
                                <span>{{ $patient->last_name }}, {{ $patient->first_name }}</span>
                            </div>
                        </td>
                        <td>{{ $patient->contact_number ?? '—' }}</td>
                        <td><span class="balance-pill balance-due">₱{{ number_format($patient->balance, 2) }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-row">No outstanding balances. Everyone's paid up 🎉</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

<div class="panel" style="margin-bottom: 1.5rem;">
        <h2 class="panel-title" style="margin-bottom: 1rem;">Revenue — Last 6 Months</h2>

        @if ($monthlyTrend->count() > 0)
            <div class="trend-chart">
                @php $maxTotal = $monthlyTrend->max('total') ?: 1; @endphp
                @foreach ($monthlyTrend as $point)
                    <div class="trend-bar-wrap">
                        <div class="trend-bar" style="height: {{ max(6, ($point->total / $maxTotal) * 140) }}px;" title="₱{{ number_format($point->total, 2) }}"></div>
                        <div class="trend-label">{{ \Carbon\Carbon::createFromFormat('Y-m', $point->month)->format('M') }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="empty-row">Not enough data yet to show a trend.</p>
        @endif
    </div>

    <div class="panel">
        <h2 class="panel-title" style="margin-bottom: 1rem;">Most Common Procedures</h2>

        @if ($commonProcedures->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Procedure</th>
                        <th>Times Performed</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($commonProcedures as $proc)
                        <tr>
                            <td>{{ $proc->description }}</td>
                            <td>{{ $proc->visit_count }}</td>
                            <td>₱{{ number_format($proc->total_revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="empty-row">Not enough visit data yet.</p>
        @endif
    </div>

</x-clinic-layout>