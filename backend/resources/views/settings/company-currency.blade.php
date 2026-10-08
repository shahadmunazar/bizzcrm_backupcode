@extends('layouts.main')
@section('content')
@section('title', 'Company Currency')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Dynamic Toast -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
            <div id="toastMessage" class="toast align-items-center text-white bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastBody">
                        Success!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <!-- Page Header -->
                <div class="page-header mb-4">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h4 class="page-title">{{ $box->name }}</h4>
                            <p class="text-muted">Select your company's default currency</p>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->

                <!-- Settings Menu -->
                <div class="card">
                    <div class="card-body pb-0 pt-2">
                        <ul class="nav nav-tabs nav-tabs-bottom">
                            <li class="nav-item me-3"><a href="profile.html" class="nav-link px-0"><i
                                        class="ti ti-settings-cog me-2"></i>General Settings</a></li>
                            <li class="nav-item me-3"><a href="{{ box_route('website.settings.branch') }}"
                                    class="nav-link px-0 active"><i class="ti ti-world-cog me-2"></i>Website
                                    Settings</a></li>
                            <li class="nav-item me-3"><a href="invoice-settings.html" class="nav-link px-0"><i
                                        class="ti ti-apps me-2"></i>App Settings</a></li>
                            <li class="nav-item me-3"><a href="email-settings.html" class="nav-link px-0"><i
                                        class="ti ti-device-laptop me-2"></i>System Settings</a></li>
                            <li class="nav-item me-3"><a href="payment-gateways.html" class="nav-link px-0"><i
                                        class="ti ti-moneybag me-2"></i>Financial Settings</a></li>
                            <li class="nav-item"><a href="storage.html" class="nav-link px-0"><i
                                        class="ti ti-flag-cog me-2"></i>Other Settings</a></li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-3 col-lg-12 theiaStickySidebar">
                        <div class="card">
                            <div class="card-body">
                                <div class="settings-sidebar">
                                    <h4 class="fw-semibold mb-3">Website Settings</h4>
                                    <div class="list-group list-group-flush settings-sidebar">

                                        <a href="{{ box_route('website.settings.company') }}"
                                            class="fw-medium {{ request()->routeIs('website.settings.company') ? 'active' : '' }}">
                                            Company Settings
                                        </a>

                                        <a href="{{ box_route('website.settings.branch') }}"
                                            class="fw-medium {{ request()->routeIs('website.settings.branch') ? 'active' : '' }}">
                                            Company Branches
                                        </a>

                                        <a href="{{ box_route('website.settings.companys_currency') }}"
                                            class="fw-medium {{ request()->routeIs('website.settings.companys_currency') ? 'active' : '' }}">
                                            Company Currency
                                        </a>

                                        <a href="{{ box_route('website.settings.attendance.module') }}"
                                            class="fw-medium {{ request()->routeIs('website.settings.attendance.module') ? 'active' : '' }}">
                                            Attendance Module
                                        </a>

                                        <a href="{{ box_route('manage_users.work_mode') }}"
                                            class="fw-medium {{ request()->routeIs('manage_users.work_mode') ? 'active' : '' }}">
                                            Work Mode Add
                                        </a>

                                        <a href="{{ box_route('website.settings.localization') }}"
                                            class="fw-medium {{ request()->routeIs('website.settings.localization') ? 'active' : '' }}">
                                            Localization
                                        </a>

                                        <a href="{{ box_route('website.settings.prefixes') }}"
                                            class="fw-medium {{ request()->routeIs('website.settings.prefixes') ? 'active' : '' }}">
                                            Prefixes
                                        </a>

                                        <a href="{{ box_route('website.settings.preference') }}"
                                            class="fw-medium {{ request()->routeIs('website.settings.preference') ? 'active' : '' }}">
                                            Preference
                                        </a>

                                        <a href="{{ box_route('website.settings.appearance') }}"
                                            class="fw-medium {{ request()->routeIs('website.settings.appearance') ? 'active' : '' }}">
                                            Appearance
                                        </a>

                                        <a href="{{ box_route('website.settings.language') }}"
                                            class="fw-medium {{ request()->routeIs('website.settings.language') ? 'active' : '' }}">
                                            Language
                                        </a>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-9 col-lg-12">
                        <!-- Active Currency Summary -->
                        <div class="card bg-primary mb-4 border-0">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="text-white mb-1">Active Currency:
                                            <span id="activeCurrencyDisplay" class="fw-bold">
                                                {{ $box->currency_code ?? 'Not Set' }} ({{ $box->currency_symbol ?? '?' }})
                                            </span>
                                        </h5>
                                        <p class="text-white-50 mb-0">This currency will be applied to all your salary calculations and reports for {{ $box->name }}.</p>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle" style="width: 60px; height: 60px;">
                                            <i class="ti ti-coins text-primary fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                    <div class="card-body">
                        <!-- Search -->
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" id="currencySearch" class="form-control" placeholder="Search by country name, currency name or code (e.g. India, USD, Euro)...">
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div id="loadingState" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading currencies...</p>
                        </div>

                        <!-- Grid -->
                        <div class="row" id="currencyGrid" style="display: none;">
                            <!-- Content will be loaded via JS -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<script>
    let currentCurrencyCode = "{{ $box->currency_code }}";
    let allCountriesData = [];

    document.addEventListener('DOMContentLoaded', function() {
        fetchCurrencies();

        // Search Listener
        document.getElementById('currencySearch').addEventListener('input', function(e) {
            filterAndRender(e.target.value);
        });
    });

    function fetchCurrencies() {
        fetch("{{ asset('assets/json/countries.json') }}")
            .then(res => res.json())
            .then(data => {
                // Process data to group by currency code
                const currencyMap = new Map();

                data.forEach(country => {
                    if (country.currencies && Object.keys(country.currencies).length > 0) {
                        const code = Object.keys(country.currencies)[0];
                        const currency = country.currencies[code];

                        if (!currencyMap.has(code)) {
                            currencyMap.set(code, {
                                code: code,
                                name: currency.name,
                                symbol: currency.symbol || code,
                                countries: []
                            });
                        }
                        currencyMap.get(code).countries.push({
                            name: country.name.common,
                            flag: `https://flagcdn.com/w40/${country.cca2.toLowerCase()}.png`,
                            cca2: country.cca2
                        });
                    }
                });

                // Convert map to array and sort by currency name
                allCountriesData = Array.from(currencyMap.values());
                allCountriesData.sort((a, b) => a.name.localeCompare(b.name));

                renderGrid(allCountriesData);

                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('currencyGrid').style.display = 'flex';
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loadingState').innerHTML = '<p class="text-danger">Failed to load currency data. Please try again later.</p>';
            });
    }

    function filterAndRender(searchTerm) {
        const term = searchTerm.toLowerCase();
        const filtered = allCountriesData.filter(currency => {
            const currencyName = currency.name.toLowerCase();
            const code = currency.code.toLowerCase();
            const symbol = currency.symbol.toLowerCase();

            // Search in associated countries
            const countryMatch = currency.countries.some(c =>
                c.name.toLowerCase().includes(term) ||
                c.cca2.toLowerCase().includes(term)
            );

            return currencyName.includes(term) || code.includes(term) || symbol.includes(term) || countryMatch;
        });

        renderGrid(filtered);
    }

    function renderGrid(data) {
        const container = document.getElementById('currencyGrid');

        if (data.length === 0) {
            container.innerHTML = '<div class="col-12 text-center text-muted">No currencies found matching your search.</div>';
            return;
        }

        container.innerHTML = data.map(currency => {
            const isSelected = currentCurrencyCode === currency.code;
            
            // Get up to 3 flags to show
            const flags = currency.countries.slice(0, 3).map(c =>
                `<img src="${c.flag}" alt="${c.name}" class="rounded-circle border border-white" style="width: 20px; height: 20px; object-fit: cover; margin-left: -8px;" title="${c.name}">`
            ).join('');

            const extraCount = currency.countries.length > 3 ?
                `<span class="small text-muted ms-1">+${currency.countries.length - 3}</span>` : '';

            return `
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <div class="currency-card p-3 border rounded cursor-pointer ${isSelected ? 'border-primary bg-light' : ''}"
                         onclick="selectCurrency('${currency.code}', '${currency.symbol}', this)">

                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="currency-symbol fs-4 fw-bold text-primary bg-light-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                ${currency.symbol}
                            </div>
                            <div class="selected-badge" style="display: ${isSelected ? 'block' : 'none'};">
                                <span class="badge bg-primary rounded-pill"><i class="ti ti-check"></i></span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <h6 class="mb-1 fw-bold text-dark">${currency.name}</h6>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10">${currency.code}</span>
                        </div>

                        <div class="d-flex align-items-center mt-3 ps-2">
                            ${flags}
                            ${extraCount}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function selectCurrency(code, symbol, element) {
        // Find currency name from the card
        const currencyName = element.querySelector('h6').textContent.trim();

        // Remove active class from all
        document.querySelectorAll('.currency-card').forEach(el => {
            el.classList.remove('border-primary', 'bg-light');
            const badge = el.querySelector('.selected-badge');
            if (badge) badge.style.display = 'none';
        });

        // Add active class to clicked
        element.classList.add('border-primary', 'bg-light');
        const badge = element.querySelector('.selected-badge');
        if (badge) badge.style.display = 'block';

        // Update global current selection tracker
        currentCurrencyCode = code;

        // Update top display
        document.getElementById('activeCurrencyDisplay').textContent = `${code} (${symbol})`;

        // AJAX update
        $.ajax({
            url: "{{ route('settings.general.companys_currency.update', ['box_slug' => request()->route('box_slug')]) }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                currency_code: code,
                currency_symbol: symbol,
                currency_name: currencyName
            },
            success: function(response) {
                if(response.success) {
                    const data = response.data || {};
                    const displayMessage = response.message;
                    const title = `Currency Updated: ${data.currency_code || code}`;
                    showSuccessToast(displayMessage, title);
                }
            },
            error: function(xhr) {
                showErrorToast('Failed to update currency. Please try again.');
            }
        });
    }

    function showSuccessToast(message, title = 'Success!') {
        const $toast = $('#toastMessage');
        const $toastBody = $('#toastBody');

        $toast.removeClass('bg-danger bg-warning').addClass('bg-success');
        $toastBody.html(`
            <div class="d-flex align-items-center">
                <i class="ti ti-check-circle me-3 fs-4"></i>
                <div class="flex-grow-1">
                    <h6 class="mb-0 text-white fw-bold">${title}</h6>
                    <small class="text-white-50">${message}</small>
                </div>
            </div>
        `);

        const toast = new bootstrap.Toast($toast[0], {
            autohide: true,
            delay: 5000
        });
        toast.show();
    }

    function showErrorToast(message, title = 'Error!') {
        const $toast = $('#toastMessage');
        const $toastBody = $('#toastBody');

        $toast.removeClass('bg-success bg-warning').addClass('bg-danger');
        $toastBody.html(`
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-circle me-3 fs-4"></i>
                <div class="flex-grow-1">
                    <h6 class="mb-0 text-white fw-bold">${title}</h6>
                    <small class="text-white-50">${message}</small>
                </div>
            </div>
        `);

        const toast = new bootstrap.Toast($toast[0], {
            autohide: true,
            delay: 5000
        });
        toast.show();
    }
</script>

<style>
    .cursor-pointer {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .currency-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-color: var(--bs-primary) !important;
    }
    .bg-light-primary {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }
    .fs-4 {
        font-size: 24px !important;
    }
    .toast-container {
        z-index: 10000 !important;
    }
</style>
@endsection
