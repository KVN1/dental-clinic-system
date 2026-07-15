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
            const resultsContainer = document.getElementById('patients-results');
            let debounceTimer;

            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                const value = input.value;
                clearBtn.style.display = value ? 'inline' : 'none';

                debounceTimer = setTimeout(function () {
                    const url = new URL('{{ route('patients.index') }}');
                    if (value) url.searchParams.set('search', value);

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => res.text())
                        .then(html => {
                            resultsContainer.innerHTML = html;
                            // Update the browser URL without reloading, so refresh/back button still works
                            window.history.replaceState({}, '', url);
                        });
                }, 300);
            });
        })();
    </script>

</x-clinic-layout>