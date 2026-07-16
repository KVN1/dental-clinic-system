<x-clinic-layout :title="'Patients'">

    <div class="page-actions">
        <div class="search-box">
            <input type="text" id="patient-search-input" value="{{ request('search') }}" placeholder="Search by name, contact, or email...">
            <a href="{{ route('patients.index') }}" class="search-clear" id="search-clear-btn" style="{{ request('search') ? '' : 'display:none;' }}">✕</a>
        </div>
        <a href="{{ route('patients.create') }}" class="btn-primary">
            <span>+ Add Patient</span>
        </a>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:1.25rem;">
        <div class="field-group" style="margin:0;min-width:170px;">
            <label for="sort-select" style="font-size:11px;">Sort By</label>
            <select id="sort-select">
                <option value="name_asc" {{ request('sort', 'name_asc') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                <option value="date_added_newest" {{ request('sort') === 'date_added_newest' ? 'selected' : '' }}>Date Added (Newest)</option>
                <option value="date_added_oldest" {{ request('sort') === 'date_added_oldest' ? 'selected' : '' }}>Date Added (Oldest)</option>
                <option value="balance_highest" {{ request('sort') === 'balance_highest' ? 'selected' : '' }}>Balance (Highest)</option>
                <option value="balance_lowest" {{ request('sort') === 'balance_lowest' ? 'selected' : '' }}>Balance (Lowest)</option>
                <option value="last_visit_recent" {{ request('sort') === 'last_visit_recent' ? 'selected' : '' }}>Last Visit (Recent)</option>
                <option value="last_visit_oldest" {{ request('sort') === 'last_visit_oldest' ? 'selected' : '' }}>Last Visit (Oldest)</option>
            </select>
        </div>

        <div class="field-group" style="margin:0;min-width:150px;">
            <label for="balance-filter-select" style="font-size:11px;">Balance</label>
            <select id="balance-filter-select">
                <option value="" {{ !request('balance_filter') ? 'selected' : '' }}>All Patients</option>
                <option value="with_balance" {{ request('balance_filter') === 'with_balance' ? 'selected' : '' }}>Has Balance</option>
                <option value="paid_up" {{ request('balance_filter') === 'paid_up' ? 'selected' : '' }}>Paid Up</option>
            </select>
        </div>

        <div class="field-group" style="margin:0;min-width:170px;">
            <label for="appointment-filter-select" style="font-size:11px;">Appointments</label>
            <select id="appointment-filter-select">
                <option value="" {{ !request('appointment_filter') ? 'selected' : '' }}>All Patients</option>
                <option value="upcoming" {{ request('appointment_filter') === 'upcoming' ? 'selected' : '' }}>Has Upcoming</option>
                <option value="none" {{ request('appointment_filter') === 'none' ? 'selected' : '' }}>No Upcoming</option>
            </select>
        </div>
    </div>

    @if (session('success'))
        <div class="status-message" style="margin-bottom: 1.25rem;">{{ session('success') }}</div>
    @endif

    <div class="panel">
        <div id="patients-results">
            @include('patients._results')
        </div>
    </div>

    <script>
        (function () {
            const input = document.getElementById('patient-search-input');
            const clearBtn = document.getElementById('search-clear-btn');
            const sortSelect = document.getElementById('sort-select');
            const balanceFilter = document.getElementById('balance-filter-select');
            const appointmentFilter = document.getElementById('appointment-filter-select');
            const resultsContainer = document.getElementById('patients-results');
            let debounceTimer;

            function fetchResults() {
                const url = new URL('{{ route('patients.index') }}');
                const value = input.value;
                if (value) url.searchParams.set('search', value);
                if (sortSelect.value) url.searchParams.set('sort', sortSelect.value);
                if (balanceFilter.value) url.searchParams.set('balance_filter', balanceFilter.value);
                if (appointmentFilter.value) url.searchParams.set('appointment_filter', appointmentFilter.value);

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        resultsContainer.innerHTML = html;
                        window.history.replaceState({}, '', url);
                    });
            }

            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                clearBtn.style.display = input.value ? 'inline' : 'none';
                debounceTimer = setTimeout(fetchResults, 300);
            });

            sortSelect.addEventListener('change', fetchResults);
            balanceFilter.addEventListener('change', fetchResults);
            appointmentFilter.addEventListener('change', fetchResults);
        })();
    </script>

</x-clinic-layout>