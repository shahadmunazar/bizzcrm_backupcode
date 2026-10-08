@extends('layouts.main')
@section('content')
@section('title', 'Leave Transaction')

@push('styles')
<style>
  /* Transaction Table Styling */
  #leaveTable-2025, #leaveTable-2024, #leaveTable-2023 {
    font-size: 0.9rem;
  }

  #leaveTable-2025 thead th, #leaveTable-2024 thead th, #leaveTable-2023 thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 12px 15px;
  }

  #leaveTable-2025 tbody tr, #leaveTable-2024 tbody tr, #leaveTable-2023 tbody tr {
    transition: background-color 0.2s ease;
  }

  #leaveTable-2025 tbody tr:hover, #leaveTable-2024 tbody tr:hover, #leaveTable-2023 tbody tr:hover {
    background-color: #f8f9fa;
  }

  #leaveTable-2025 tbody td, #leaveTable-2024 tbody td, #leaveTable-2023 tbody td {
    padding: 12px 15px;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
  }

  #leaveTable-2025 tfoot, #leaveTable-2024 tfoot, #leaveTable-2023 tfoot {
    background-color: #f8f9fa;
    border-top: 2px solid #dee2e6;
  }

  #leaveTable-2025 tfoot td, #leaveTable-2024 tfoot td, #leaveTable-2023 tfoot td {
    padding: 15px;
    font-size: 1rem;
  }

  .table-success-subtle {
    background-color: rgba(25, 135, 84, 0.05);
  }

  .table-danger-subtle {
    background-color: rgba(220, 53, 69, 0.05);
  }

  /* Responsive Transaction Remarks */
  .transaction-remarks-cell {
    max-width: 300px;
    min-width: 200px;
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
  }

  .transaction-remarks-text {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
    max-height: 2.8em;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .transaction-remarks-text.expanded {
    -webkit-line-clamp: unset;
    max-height: none;
    display: block;
  }

  .transaction-remarks-text:hover {
    color: #0d6efd;
  }

  .transaction-remarks-full {
    display: none;
    position: absolute;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 1000;
    max-width: 400px;
    word-wrap: break-word;
  }

  /* Mobile Responsive */
  @media (max-width: 768px) {
    .transaction-remarks-cell {
      max-width: 150px;
      min-width: 120px;
    }

    .transaction-remarks-text {
      -webkit-line-clamp: 1;
      max-height: 1.4em;
    }
  }

  @media (max-width: 576px) {
    .transaction-remarks-cell {
      max-width: 100px;
      min-width: 80px;
      font-size: 0.8rem;
    }
  }
</style>
@endpush
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Leaves Transaction</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="index.html"><i class="ti ti-smart-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            Employee
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Leaves Transaction</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="me-2 mb-2">
                    <div class="dropdown">
                        <a href="javascript:void(0);"
                            class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <i class="ti ti-file-export me-1"></i>Export
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end p-3">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="exportLeaveTransaction('pdf')">
                                    <i class="ti ti-file-type-pdf me-1"></i>Export as PDF
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="exportLeaveTransaction('excel')">
                                    <i class="ti ti-file-type-xls me-1"></i>Export as Excel
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="head-icons ms-2">
                    <a href="javascript:void(0);" class="" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-original-title="Collapse" id="collapse-header">
                        <i class="ti ti-chevrons-up"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->
        <div class="row" id="leaveSummaryCardsContainer">
            <!-- Cards will be dynamically generated here -->
            <div class="col-12 text-center py-4" id="cardsLoading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading leave summary...</p>
            </div>
            {{-- <div class="col-xl-3 col-md-6">
                <div class="card bg-black-le">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-start">
                                <p class="mb-1">Annual Leaves </p>
                                <h4>05 </h4>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <span class="avatar avatar-md d-flex">
                                        <i class="ti ti-calendar-event fs-32"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="badge bg-secondary-transparent">Remaining Leaves : 07
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-blue-le">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-start">
                                <p class="mb-1">Medical Leaves </p>
                                <h4>11 </h4>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <span class="avatar avatar-md d-flex">
                                        <i class="ti ti-vaccine fs-32"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="badge bg-info-transparent">Remaining Leaves : 01
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-purple-le">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-start">
                                <p class="mb-1">Casual Leaves </p>
                                <h4>02 </h4>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <span class="avatar avatar-md d-flex">
                                        <i class="ti ti-hexagon-letter-c fs-32"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="badge bg-transparent-purple">Remaining Leaves : 10
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-pink-le">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="text-start">
                                <p class="mb-1">Other Leaves </p>
                                <h4>07 </h4>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-2">
                                    <span class="avatar avatar-md d-flex">
                                        <i class="ti ti-hexagonal-prism-plus fs-32"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <span class="badge bg-pink-transparent">Remaining Leaves : 05
                        </span>
                    </div>
                </div>
            </div> --}}
        </div>
        <div class="tab-content custom-accordion-items" id="leaveAccordionWrapper">
            <div class="tab-pane active show" id="bottom-justified-tab1" role="tabpanel">
                <div class="accordion accordions-items-seperate" id="accordionExample">
                    <!-- Accordions will be dynamically generated based on years found in database -->
                    <div class="text-center py-4" id="accordionLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                                    </div>
                        <p class="mt-2 text-muted">Loading leave transactions...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manage Columns Modal -->
<div class="modal fade" id="manageColumnsModal" tabindex="-1" aria-labelledby="manageColumnsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageColumnsModalLabel">Manage Columns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="showAllColumns">Show All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="hideAllColumns">Hide All</button>
                </div>
                <div class="list-group" id="columnsList">
                    <!-- Columns will be populated dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyColumnChanges">Apply Changes</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
  const wrapper = $('#leaveAccordionWrapper #accordionExample');
  const cardsWrapper = $('#leaveSummaryCardsContainer');
  const currentYear = new Date().getFullYear();
  let allAvailableYears = []; // Store all years from database
  let allTransactionsData = {}; // Store all transactions data by year
  let allLeaveTypes = []; // Store leave types

  // Column Definitions for Manage Columns feature
  const columnDefinitions = [
    { index: 0, name: '#', visible: true },
    { index: 1, name: 'Date', visible: true },
    { index: 2, name: 'Applied Date', visible: true },
    { index: 3, name: 'Approved Date', visible: true },
    { index: 4, name: 'Leave Type', visible: true },
    { index: 5, name: 'Transaction Remarks', visible: true },
    { index: 6, name: 'Credit', visible: true },
    { index: 7, name: 'Debit', visible: true },
    { index: 8, name: 'Lapsed', visible: true },
    { index: 9, name: 'C.F.', visible: true },
    { index: 10, name: 'Balance', visible: true }
  ];

  // Page identifier for column preferences
  const pageIdentifier = 'leave-transaction';

  // Load column visibility preferences from database
  function loadColumnPreferences(callback) {
    $.ajax({
      url: '{{ route("settings.column.preferences.get", ["box_slug" => request()->route("box_slug")]) }}',
      type: 'GET',
      data: {
        page_identifier: pageIdentifier
      },
      async: false, // Make synchronous to ensure preferences are loaded before table initialization
      success: function(response) {
        if (response.success && response.data && Array.isArray(response.data) && response.data.length > 0) {
          try {
            const preferences = response.data;
            preferences.forEach(pref => {
              // Convert string index to number
              const prefIndex = parseInt(pref.index);
              const col = columnDefinitions.find(c => c.index === prefIndex);
              if (col) {
                // Handle both boolean and string values ("true"/"false")
                if (typeof pref.visible === 'string') {
                  col.visible = pref.visible === 'true' || pref.visible === '1';
                } else {
                  col.visible = pref.visible !== undefined ? Boolean(pref.visible) : true;
                }
              }
            });
            if (callback) callback();
          } catch (e) {
            console.error('Error loading column preferences:', e);
            // Fallback: show all columns
            columnDefinitions.forEach(col => col.visible = true);
            if (callback) callback();
          }
        } else {
          // No preferences found or empty response - show all columns by default
          columnDefinitions.forEach(col => col.visible = true);
          if (callback) callback();
        }
      },
      error: function(xhr, status, error) {
        console.error('Error loading column preferences:', error);
        // Fallback: show all columns
        columnDefinitions.forEach(col => col.visible = true);
        if (callback) callback();
      }
    });
  }

  // Save column preferences to database
  function saveColumnPreferences() {
    $.ajax({
      url: '{{ route("settings.column.preferences.save", ["box_slug" => request()->route("box_slug")]) }}',
      type: 'POST',
      data: {
        page_identifier: pageIdentifier,
        column_preferences: columnDefinitions,
        _token: '{{ csrf_token() }}'
      },
      success: function(response) {
        if (response.success) {
          console.log('Column preferences saved successfully');
        }
      },
      error: function(xhr, status, error) {
        console.error('Error saving column preferences:', error);
      }
    });
  }

  // Populate columns modal
  function populateColumnsModal() {
    const columnsList = $('#columnsList');
    columnsList.empty();

    columnDefinitions.forEach(col => {
      const listItem = $(`
        <div class="list-group-item">
          <div class="form-check">
            <input class="form-check-input column-toggle" type="checkbox"
                   data-column-index="${col.index}"
                   id="col_${col.index}"
                   ${col.visible ? 'checked' : ''}>
            <label class="form-check-label" for="col_${col.index}">
              ${col.name}
            </label>
          </div>
        </div>
      `);
      columnsList.append(listItem);
    });
  }

  // Apply column visibility to all DataTables
  function applyColumnVisibilityToAllTables() {
    allAvailableYears.forEach(year => {
      const dataTable = dataTableInstances[year];
      if (dataTable && $.fn.DataTable.isDataTable(`#leaveTable-${year}`)) {
        columnDefinitions.forEach(col => {
          try {
            dataTable.column(col.index).visible(col.visible, false);
          } catch (e) {
            console.error(`Error applying column visibility to table ${year}:`, e);
          }
        });
        dataTable.columns.adjust();
        dataTable.draw(false);
      }
    });
  }

  // Load column preferences before fetching data
  loadColumnPreferences(function() {
  // Fetch all years data in one API call
  fetchAllYearsData();
  });

  // Fetch all years data in one API call
  function fetchAllYearsData() {
    $.ajax({
      url: '{{ route("leave_transaction.data", ["box_slug" => request()->route("box_slug")]) }}',
      type: 'GET',
      data: {
        fetch_all_years: 'true', // Flag to fetch all years in one call
        year: currentYear, // Still pass year for backward compatibility
      },
      success: function (res) {
        // Hide loading indicators
        $('#cardsLoading').hide();
        $('#accordionLoading').hide();

        if (res.status) {
          // Store all data
          allAvailableYears = res.availableYears || [];
          allLeaveTypes = res.leaveTypes || [];

          // Store transactions by year
          if (res.leaveTransactions) {
            allTransactionsData = res.leaveTransactions;
          }

          // Generate accordions for all years
          generateAccordionsForAllYears();
          generateLeaveSummaryCardsForAllYears();
        } else {
          $('#cardsLoading').html('<p class="text-danger">Failed to fetch leave data.</p>');
        }
      },
      error: function (err) {
        console.error('Error fetching all years data:', err);
        $('#cardsLoading').html('<p class="text-danger">Error loading data. Please try again.</p>');
      }
    });
  }

  // Fetch leave data for a specific year (for filter updates)
  function fetchLeaveDataForYear(year, filters = {}, callback) {
    $.ajax({
      url: '{{ route("leave_transaction.data", ["box_slug" => request()->route("box_slug")]) }}',
      type: 'GET',
      data: {
        year: year,
        dateRange: filters.dateRange || '',
        leaveTypes: filters.leaveTypes || [],
        creditDebitFilters: filters.creditDebitFilters || [],
      },
      success: function (res) {
        if (res.status) {
          const transactions = res.leaveTransactions || {};
          const leaveTypes = res.leaveTypes || [];
          if (callback) callback(transactions, leaveTypes);
        }
      },
      error: function (err) {
        console.error(`Error fetching data for year ${year}:`, err);
        if (callback) callback({}, []);
      }
    });
  }

  // Generate accordions for all years that have data
  function generateAccordionsForAllYears() {
    // Hide loading indicators
    $('#cardsLoading').hide();
    $('#accordionLoading').hide();

    if (allAvailableYears.length === 0) {
      wrapper.html('<p class="text-center text-muted py-4">No leave transactions found.</p>');
      return;
    }

    // Create transactions object with ALL years that have data (even if empty)
    // This ensures all accordions are generated and data is preserved
    const transactionsByYear = {};
    allAvailableYears.forEach(year => {
      // Include all years, even if they have no transactions (empty array)
      // This keeps the accordion structure intact
      transactionsByYear[year] = allTransactionsData[year] || [];
    });

    // Generate accordions for ALL years - data stays in memory, only UI toggles
    generateAccordions(transactionsByYear, allLeaveTypes, {});
  }

  // Generate summary cards for all years
  function generateLeaveSummaryCardsForAllYears() {
    const transactionsByYear = {};
    allAvailableYears.forEach(year => {
      transactionsByYear[year] = allTransactionsData[year] || [];
    });

    const leaveSummary = calculateLeaveSummary(transactionsByYear, allLeaveTypes);
    generateLeaveSummaryCards(transactionsByYear, allLeaveTypes, leaveSummary, {});
  }

  // Unified function to fetch leave data with optional filters (for single year updates)
  // This function should ONLY update the DataTable, NOT regenerate accordions
  function fetchLeaveData(year, filters = {}) {
    // Store the data in allTransactionsData for this year
    fetchLeaveDataForYear(year, filters, function(transactions, leaveTypes) {
        // Hide loading indicator
        $('#cardsLoading').hide();

      // Store data in global variable - don't lose it!
      if (transactions[year]) {
        allTransactionsData[year] = transactions[year];
      }

      // Only update the DataTable for this specific year - don't regenerate accordions
      const dataTable = dataTableInstances[year];
      if (dataTable && $.fn.DataTable.isDataTable(`#leaveTable-${year}`)) {
        // Update the ajax data function to include current filters
        dataTable.settings()[0].ajax.data = function(d) {
          d.year = year;
          d.dateRange = filters.dateRange || '';
          d.leaveTypes = filters.leaveTypes || [];
          d.creditDebitFilters = filters.creditDebitFilters || [];
        };
        // Reload table with new filters
        dataTable.ajax.reload(null, false); // false = don't reset paging
          } else {
        // If DataTable doesn't exist yet, initialize it
        if ($(`#leaveTable-${year}`).length > 0) {
          initDataTable(year, filters);
        }
      }

      // Update filter info badge
          const activeFiltersCount = (filters.dateRange ? 1 : 0) + (filters.leaveTypes?.length || 0) + (filters.creditDebitFilters?.length || 0);
          const filterInfoBadge = $(`#filter-info-${year}`);
          if (activeFiltersCount > 0 && filterInfoBadge.length > 0) {
            filterInfoBadge.removeClass('d-none').text(`${activeFiltersCount} Filter(s) Active`);
          } else if (filterInfoBadge.length > 0) {
            filterInfoBadge.addClass('d-none');
          }

          // Update remove button visibility after data is loaded and DOM is updated
          setTimeout(function() {
            updateRemoveButton(year);
          }, 500);
    });
  }

  // Store DataTables instances
  const dataTableInstances = {};

  // Global click handler for expandable transaction remarks (works after DataTables redraws)
  $(document).on('click', '.transaction-remarks-text, .remarks-hint', function(e) {
    e.stopPropagation();
    const textElement = $(this).hasClass('transaction-remarks-text') ? $(this) : $(this).siblings('.transaction-remarks-text');
    const hint = textElement.siblings('.remarks-hint');

    textElement.toggleClass('expanded');

    // Update tooltip/hint text
    if (textElement.hasClass('expanded')) {
      hint.html('<i class="ti ti-chevron-up me-1"></i>Click to collapse');
    } else {
      hint.html('<i class="ti ti-info-circle me-1"></i>Click to view full text');
    }
  });

  function generateAccordions(transactionsByYear, leaveTypes, filters = {}) {
    let html = '';
    const yearsToGenerate = allAvailableYears.length > 0 ? allAvailableYears : Object.keys(transactionsByYear).map(Number);
    const sortedYears = yearsToGenerate.map(Number).sort((a, b) => b - a);
    sortedYears.forEach((year, index) => {
      const isCurrentYear = year === currentYear;
      const showClass = isCurrentYear ? 'show' : '';
      const collapsedClass = isCurrentYear ? '' : 'collapsed';
      const ariaExpanded = isCurrentYear ? 'true' : 'false';
      const yearTransactions = allTransactionsData[year] || transactionsByYear[year] || [];
      const yearSummary = calculateLeaveSummary({ [year]: yearTransactions }, leaveTypes);
      
      const totalCredits = yearTransactions.reduce((sum, tx) => sum + parseFloat(tx.credit_leave || 0), 0);
      const totalCF = yearTransactions.reduce((sum, tx) => sum + parseFloat(tx.carried_forward_leave || 0), 0);
      const totalLeaves = (totalCredits + totalCF).toFixed(2);
      
      const totalRemaining = Object.values(yearSummary).reduce((sum, item) => sum + parseFloat(item.remaining || 0), 0);
      const totalTaken = yearTransactions.reduce((sum, tx) => sum + parseFloat(tx.debit_leave || 0), 0);
      const totalLapse = yearTransactions.reduce((sum, tx) => sum + parseFloat(tx.lapsed_leave || 0), 0).toFixed(2);
      const leaveTypeOptions = leaveTypes.map(type => `
        <li><a href="javascript:void(0);" class="dropdown-item rounded-1 leave-type"
          data-type="${type.leave_type}" data-year="${year}">${type.leave_type}</a></li>
      `).join('');

      html += `
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading-${year}">
          <button class="accordion-button ${collapsedClass}" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-${year}" aria-expanded="${ariaExpanded}" aria-controls="collapse-${year}">
            <div class="d-flex align-items-center justify-content-between flex-fill w-100">
              <h5 class="mb-0">Leaves ${year}</h5>
              <div class="d-flex align-items-center">
                <a href="#" class="btn btn-icon btn-sm me-2" data-bs-toggle="modal" data-bs-target="#edit_holidays">
                  <i class="ti ti-edit"></i>
                </a>
                <i class="ti ti-chevron-down fs-18"></i>
              </div>
            </div>
          </button>
        </h2>
        <div id="collapse-${year}" class="accordion-collapse collapse ${showClass} border-top"
          aria-labelledby="heading-${year}">
          <div class="accordion-body">
            <div class="card">
              <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <div class="d-flex align-items-center flex-wrap">
                  <h5 class="me-2">Leave List</h5>
                  <span class="badge bg-primary-transparent me-2">Total Leaves : ${totalLeaves}</span>
                  <span class="badge bg-secondary-transparent me-2">Total Remaining Leaves : ${totalRemaining.toFixed(2)}</span>
                  <span class="badge bg-secondary-transparent me-2">Total Taken Leaves : ${totalTaken.toFixed(2)}</span>
                  <span class="badge bg-secondary-transparent me-2">Total Lapse Leaves : ${totalLapse}</span>
                  <span class="badge bg-info-transparent d-none" id="filter-info-${year}" title="Active Filters: All filters work together (AND logic)">Filters Active</span>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                  <!-- Manage Columns -->
                  <div class="me-3 d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-white d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#manageColumnsModal">
                      <i class="ti ti-columns me-1"></i>Manage Columns
                    </button>
                  </div>
                  <!-- Date Range Filter -->
                  <!-- Date Range Filter -->
<div class="me-3 d-flex align-items-center filter-controls" data-year="${year}">
  <div class="input-icon-end position-relative">
    <input type="text" id="dateRange-${year}" class="form-control date-range bookingrange"
      placeholder="dd/mm/yyyy - dd/mm/yyyy" autocomplete="off" />
    <span class="input-icon-addon">
      <i class="ti ti-chevron-down"></i>
    </span>
  </div>
  <button class="btn btn-icon btn-sm btn-outline-secondary ms-2 d-none remove-date-filter" data-year="${year}" title="Clear Date Filter">
    <i class="ti ti-x"></i>
  </button>
</div>

<!-- Leave Type Filter -->
<div class="dropdown me-3 leave-type-filter filter-controls d-flex align-items-center" data-year="${year}">
  <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center"
    data-bs-toggle="dropdown">
    Leave Type
  </a>
  <ul class="dropdown-menu dropdown-menu-end p-3" id="leaveTypeDropdown-${year}">
    ${leaveTypeOptions}
  </ul>
  <button class="btn btn-icon btn-sm btn-outline-secondary ms-2 d-none remove-leave-filter" data-year="${year}" title="Clear Leave Type Filter">
    <i class="ti ti-x"></i>
  </button>
</div>

<!-- Credit/Debit Filter -->
<div class="dropdown me-3 credit-debit-filter filter-controls d-flex align-items-center" data-year="${year}">
  <a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-inline-flex align-items-center"
    data-bs-toggle="dropdown">
    Filter Credit/Debit
  </a>
  <ul class="dropdown-menu dropdown-menu-end p-3" id="creditDebitFilter-${year}">
    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 credit-debit" data-type="credit" data-year="${year}">Credit Type</a></li>
    <li><a href="javascript:void(0);" class="dropdown-item rounded-1 credit-debit" data-type="debit" data-year="${year}">Debit Type</a></li>
  </ul>
  <button class="btn btn-icon btn-sm btn-outline-secondary ms-2 d-none remove-credit-filter" data-year="${year}" title="Clear Credit/Debit Filter">
    <i class="ti ti-x"></i>
  </button>
</div>

                </div>
              </div>

              <!-- Table Section with DataTables -->
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover table-bordered mb-0" id="leaveTable-${year}">
                    <thead class="table-light">
                      <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th style="min-width: 120px;">Date</th>
                        <th style="min-width: 120px;">Applied Date</th>
                        <th style="min-width: 120px;">Approved Date</th>
                        <th style="min-width: 150px;">Leave Type</th>
                        <th style="min-width: 200px; max-width: 400px;">Transaction Remarks</th>
                        <th class="text-end" style="min-width: 120px;">Credit</th>
                        <th class="text-end" style="min-width: 120px;">Debit</th>
                        <th class="text-end" style="min-width: 120px;">Lapsed</th>
                        <th class="text-end" style="min-width: 120px;">C.F.</th>
                        <th class="text-end" style="min-width: 120px;">Balance</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- DataTables will populate this -->
                    </tbody>
                    <tfoot class="table-light">
                      <tr>
                        <td colspan="6" class="text-end fw-bold">Total:</td>
                        <td class="text-end fw-bold" id="total-credit-${year}">0.00</td>
                        <td class="text-end fw-bold" id="total-debit-${year}">0.00</td>
                        <td class="text-end fw-bold" id="total-lapse-${year}">0.00</td>
                        <td class="text-end fw-bold" id="total-cf-${year}">0.00</td>
                        <td class="text-end fw-bold" id="total-balance-${year}">0.00</td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>`;
    });

    wrapper.html(html);
    initDateRangePickers();

    // Initialize DataTables for each year
    sortedYears.forEach(year => {
      setTimeout(function() {
        initDataTable(year, filters);
        updateRemoveButton(year);
      }, 300);
    });
  }

  // Initialize DataTables for leave transactions
  function initDataTable(year, filters = {}) {
    const tableId = `#leaveTable-${year}`;

    // Check if table exists
    if ($(tableId).length === 0) {
      console.warn(`Table ${tableId} not found`);
      return;
    }

    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable(tableId)) {
      $(tableId).DataTable().destroy();
      delete dataTableInstances[year];
    }

    // Get current filter values
    const dateRange = $(`#dateRange-${year}`).val() || '';
    const leaveTypes = [];
    $(`#leaveTypeDropdown-${year} .leave-type.selected`).each(function () {
      leaveTypes.push($(this).data('type'));
    });
    const creditDebitFilters = [];
    $(`#creditDebitFilter-${year} .credit-debit.selected`).each(function () {
      creditDebitFilters.push($(this).data('type'));
    });

    // Merge with passed filters
    const activeFilters = {
      dateRange: filters.dateRange || dateRange,
      leaveTypes: filters.leaveTypes || leaveTypes,
      creditDebitFilters: filters.creditDebitFilters || creditDebitFilters
    };

    // Initialize DataTable with server-side processing
    const dataTable = $(tableId).DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '{{ route("leave_transaction.data", ["box_slug" => request()->route("box_slug")]) }}',
        type: 'GET',
        data: function(d) {
          // Add custom filters to DataTables request
          d.year = year;
          d.dateRange = activeFilters.dateRange;
          d.leaveTypes = activeFilters.leaveTypes;
          d.creditDebitFilters = activeFilters.creditDebitFilters;
        },
        error: function(xhr, error, thrown) {
          console.error('DataTable Error:', error);
          console.error('Response:', xhr.responseText);
        }
      },
      columns: [
        {
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false,
          className: 'text-center fw-medium',
          visible: columnDefinitions[0].visible,
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          data: 'formatted_date',
          name: 'date',
          className: 'fw-medium',
          visible: columnDefinitions[1].visible
        },
        {
          data: 'applied_date',
          name: 'applied_date',
          className: 'fw-medium',
          visible: columnDefinitions[2].visible
        },
        {
          data: 'approved_date',
          name: 'approved_date',
          className: 'fw-medium',
          visible: columnDefinitions[3].visible
        },
        {
          data: 'leave_type_name',
          name: 'leave_type_name',
          visible: columnDefinitions[4].visible,
          render: function(data, type, row) {
            return `<span class="badge bg-primary-transparent">${data}</span>`;
          }
        },
        {
          data: 'transaction_remarks',
          name: 'transaction_remarks',
          className: 'transaction-remarks-cell',
          visible: columnDefinitions[5].visible,
          render: function(data, type, row) {
            if (!data || data === '-') {
              return '<span class="text-muted">-</span>';
            }

            const isBackfilled = data.includes('Backfilled');
            const className = isBackfilled ? 'text-info' : '';
            const textClass = 'transaction-remarks-text ' + className;
            const uniqueId = 'remarks-' + row.id + '-' + Date.now();

            // Escape HTML to prevent XSS
            const escapedData = $('<div>').text(data).html();

            // Check if text is long (more than 50 characters)
            const isLong = data.length > 50;

            if (isLong) {
              return `
                <div class="position-relative">
                  <div class="${textClass}" data-full-text="${escapedData.replace(/"/g, '&quot;')}" title="Click to expand/collapse">
                    ${escapedData}
                  </div>
                  <small class="text-muted d-block mt-1 remarks-hint" style="font-size: 0.75rem; cursor: pointer;">
                    <i class="ti ti-info-circle me-1"></i>Click to view full text
                  </small>
                </div>
              `;
            } else {
              return `<span class="${className}">${escapedData}</span>`;
            }
          }
        },
        {
          data: 'formatted_credit',
          name: 'credit_leave',
          className: 'text-end',
          visible: columnDefinitions[6].visible,
          render: function(data, type, row) {
            const credit = parseFloat(data || 0);
            return credit > 0
              ? `<span class="text-success fw-bold">+${credit.toFixed(2)}</span>`
              : '<span class="text-muted">0.00</span>';
          }
        },
        {
          data: 'formatted_debit',
          name: 'debit_leave',
          className: 'text-end',
          visible: columnDefinitions[7].visible,
          render: function(data, type, row) {
            const debit = parseFloat(data || 0);
            return debit > 0
              ? `<span class="text-danger fw-bold">-${debit.toFixed(2)}</span>`
              : '<span class="text-muted">0.00</span>';
          }
        },
        {
          data: 'lapsed_leave',
          name: 'lapsed_leave',
          className: 'text-end',
          visible: columnDefinitions[8].visible,
          render: function(data, type, row) {
            const lapsed = parseFloat(data || 0);
            return lapsed > 0
              ? `<span class="text-warning fw-bold">${lapsed.toFixed(2)}</span>`
              : '<span class="text-muted">0.00</span>';
          }
        },
        {
          data: 'carried_forward_leave',
          name: 'carried_forward_leave',
          className: 'text-end',
          visible: columnDefinitions[9].visible,
          render: function(data, type, row) {
            const cf = parseFloat(data || 0);
            return cf > 0
              ? `<span class="text-info fw-bold">${cf.toFixed(2)}</span>`
              : '<span class="text-muted">0.00</span>';
          }
        },
        {
          data: 'formatted_balance',
          name: 'balance_leave',
          className: 'text-end fw-semibold',
          visible: columnDefinitions[10].visible,
          render: function(data, type, row) {
            const balance = parseFloat(data || 0);
            return `<span class="badge bg-secondary-transparent">${balance.toFixed(2)}</span>`;
          }
        }
      ],
      order: [[1, 'desc']], // Order by date descending
      pageLength: 25,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      language: {
        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
        search: "",
        searchPlaceholder: "Search transactions...",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ transactions",
        infoEmpty: "No transactions found",
        infoFiltered: "(filtered from _MAX_ total transactions)",
        paginate: {
          next: 'Next <i class="ti ti-chevron-right"></i>',
          previous: '<i class="ti ti-chevron-left"></i> Prev'
        }
      },
      drawCallback: function(settings) {
        // Update totals after table is drawn
        updateDataTableTotals(year);
      },
      rowCallback: function(row, data) {
        // Add row styling based on transaction type
        if (parseFloat(data.formatted_credit || 0) > 0) {
          $(row).addClass('table-success-subtle');
        } else if (parseFloat(data.formatted_debit || 0) > 0) {
          $(row).addClass('table-danger-subtle');
        }
      }
    });

    // Store DataTable instance
    dataTableInstances[year] = dataTable;
  }

  // Export Functionality
  window.exportLeaveTransaction = function(type) {
    const yearToExport = currentYear; 
    const exportUrl = '{{ route("leave_transaction.export", ["box_slug" => request()->route("box_slug")]) }}';
    
    // Gather active filters for the year (if any)
    const dateRange = $(`#dateRange-${yearToExport}`).val() || '';
    const leaveTypes = [];
    $(`#leaveTypeDropdown-${yearToExport} .leave-type.selected`).each(function () {
      leaveTypes.push($(this).data('type'));
    });
    const creditDebitFilters = [];
    $(`#creditDebitFilter-${yearToExport} .credit-debit.selected`).each(function () {
      creditDebitFilters.push($(this).data('type'));
    });

    // toastr.info('Starting export...', 'Please wait');

    $.ajax({
      url: exportUrl,
      type: 'GET',
      data: {
        type: type,
        year: yearToExport,
        dateRange: dateRange,
        leaveTypes: leaveTypes,
        creditDebitFilters: creditDebitFilters
      },
      success: function(response) {
        if (response.status === 'success') {
          // toastr.success(response.message, 'Export Started');
          pollForFile(response.file_name);
        } else {
          console.error(response.message || 'Export failed');
        }
      },
      error: function(xhr) {
        let msg = 'Failed to start export';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        console.error(msg);
      }
    });
  };

  function pollForFile(fileName) {
    const pollInterval = 2000;
    const maxAttempts = 30;
    let attempts = 0;

    const checkFile = function() {
      attempts++;
      // We need to construct the URL dynamically because 'fileName' is a JS variable
      // Using a placeholder ':file' and replacing it
      let downloadBaseUrl = '{{ route("leave_transaction.export.download", ["box_slug" => request()->route("box_slug"), "file" => ":file"]) }}';
      const downloadUrl = downloadBaseUrl.replace(':file', fileName);
        
      $.ajax({
        url: downloadUrl,
        type: 'GET',
        xhrFields: {
           responseType: 'blob'
        },
        success: function(data, status, xhr) {
             const blob = new Blob([data], { type: xhr.getResponseHeader('content-type') });
             const link = document.createElement('a');
             link.href = window.URL.createObjectURL(blob);
             link.download = fileName;
             document.body.appendChild(link);
             link.click();
             document.body.removeChild(link);
             // toastr.success('Export downloaded successfully', 'Success');
        },
        error: function(xhr) {
          // If 404, it means file not ready yet
          if (xhr.status === 404 && attempts < maxAttempts) {
             setTimeout(checkFile, pollInterval);
          } else {
             console.error('Export timed out or failed to download.');
          }
        }
      });
    };
    
    setTimeout(checkFile, pollInterval);
  }

  // Update totals from DataTable data
  function updateDataTableTotals(year) {
    const tableId = `#leaveTable-${year}`;
    const dataTable = dataTableInstances[year];

    if (!dataTable) return;

    // Get all data from current page
    const tableData = dataTable.rows({ search: 'applied' }).data();
    let totalCredit = 0;
    let totalDebit = 0;
    let totalLapse = 0;
    let totalCF = 0;
    let lastBalance = 0;

    tableData.each(function(row) {
      totalCredit += parseFloat(row.formatted_credit || 0);
      totalDebit += parseFloat(row.formatted_debit || 0);
      totalLapse += parseFloat(row.lapsed_leave || 0);
      totalCF += parseFloat(row.carried_forward_leave || 0);
      lastBalance = parseFloat(row.formatted_balance || 0);
    });

    // Update footer
    $(`#total-credit-${year}`).text(totalCredit.toFixed(2));
    $(`#total-debit-${year}`).text(totalDebit.toFixed(2));
    $(`#total-lapse-${year}`).text(totalLapse.toFixed(2));
    $(`#total-cf-${year}`).text(totalCF.toFixed(2));
    $(`#total-balance-${year}`).text(lastBalance.toFixed(2));
  }

  // Initialize date range pickers
  function initDateRangePickers() {
    wrapper.find('.date-range').each(function () {
      if (!$(this).data('daterangepicker')) {
        $(this).daterangepicker({
          locale: {
            format: 'DD/MM/YYYY',
            cancelLabel: 'Clear',
          },
          autoUpdateInput: false,
          opens: 'right',
        });

        $(this).on('apply.daterangepicker', function (ev, picker) {
          $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
          const year = $(this).attr('id').split('-')[1];

          // Show active state on date filter button
          const dateFilterBtn = $(this).closest('.filter-controls').find('.date-range');
          if ($(this).val()) {
            dateFilterBtn.addClass('border-primary');
          }

          // Show remove button immediately for date range
          const dateRemoveBtn = $(`.remove-date-filter[data-year="${year}"]`);
          if (dateRemoveBtn.length > 0 && $(this).val().trim() !== '') {
            dateRemoveBtn.removeClass('d-none').show();
          }

          // Apply filters (updateRemoveButton will be called after data loads)
          applyFilters(year);
        });

        $(this).on('cancel.daterangepicker', function () {
          $(this).val('');
          const year = $(this).attr('id').split('-')[1];

          // Remove active state
          const dateFilterBtn = $(this).closest('.filter-controls').find('.date-range');
          dateFilterBtn.removeClass('border-primary');

          // Update remove button visibility
          updateRemoveButton(year);
          applyFilters(year);
        });

      }
    });
  }

  // Generate table rows for transactions
  // ✅ Improved styling to look like a professional transaction table
  function generateTableRows(transactions) {
    if (!transactions.length) {
      return '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="ti ti-inbox me-2"></i>No transactions found</td></tr>';
    }

    // Sort transactions: first by leave_type, then by date (descending)
    const sortedTransactions = [...transactions].sort((a, b) => {
      // First sort by leave type
      if (a.leave_type !== b.leave_type) {
        return a.leave_type.localeCompare(b.leave_type);
      }
      // Then by date (newest first) - date format is "dd MMM YYYY" (e.g., "01 Dec 2025")
      const parseDate = (dateStr) => {
        const parts = dateStr.split(' ');
        const day = parseInt(parts[0]);
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const month = monthNames.indexOf(parts[1]);
        const year = parseInt(parts[2]);
        return new Date(year, month, day);
      };
      const dateA = parseDate(a.date);
      const dateB = parseDate(b.date);
      return dateB - dateA;
    });

    let rows = '';
    let totalCredit = 0;
    let totalDebit = 0;
    let lastBalance = 0;

    sortedTransactions.forEach((item, index) => {
      const creditLeave = parseFloat(item.credit_leave || 0);
      const debitLeave = parseFloat(item.debit_leave || 0);
      const lapsedLeave = parseFloat(item.lapsed_leave || 0);
      const cfLeave = parseFloat(item.carried_forward_leave || 0);
      const balanceLeave = parseFloat(item.balance_leave || 0);
      totalCredit += creditLeave;
      totalDebit += debitLeave;
      lastBalance = balanceLeave;
      const rowClass = creditLeave > 0 ? 'table-success-subtle' : debitLeave > 0 ? 'table-danger-subtle' : '';
      const remarks = item.transaction_remarks || '-';
      const isBackfilled = remarks.includes('Backfilled');
      const remarksClass = isBackfilled ? 'text-info' : '';
      rows += `
      <tr class="${rowClass}">
        <td class="text-center fw-medium">${index + 1}</td>
        <td class="fw-medium">${item.date}</td>
        <td>
          <span class="badge bg-primary-transparent">${item.leave_type}</span>
        </td>
        <td class="${remarksClass}" style="max-width: 400px;">
          <small>${remarks}</small>
        </td>
        <td class="text-end">
          ${creditLeave > 0 ? `<span class="text-success fw-bold">+${creditLeave.toFixed(2)}</span>` : '<span class="text-muted">0.00</span>'}
        </td>
        <td class="text-end">
          ${debitLeave > 0 ? `<span class="text-danger fw-bold">-${debitLeave.toFixed(2)}</span>` : '<span class="text-muted">0.00</span>'}
        </td>
        <td class="text-end">
          ${lapsedLeave > 0 ? `<span class="text-warning fw-bold">${lapsedLeave.toFixed(2)}</span>` : '<span class="text-muted">0.00</span>'}
        </td>
        <td class="text-end">
          ${cfLeave > 0 ? `<span class="text-info fw-bold">${cfLeave.toFixed(2)}</span>` : '<span class="text-muted">0.00</span>'}
        </td>
        <td class="text-end fw-semibold">
          <span class="badge bg-secondary-transparent">${balanceLeave.toFixed(2)}</span>
        </td>
      </tr>`;
    });
    window[`totals_${new Date().getFullYear()}`] = {
      credit: totalCredit,
      debit: totalDebit,
      balance: lastBalance
    };
    return rows;
  }
  function updateTableTotals(year, transactions) {
    let totalCredit = 0;
    let totalDebit = 0;
    let lastBalance = 0;

    if (transactions && transactions.length > 0) {
      transactions.forEach(tx => {
        totalCredit += parseFloat(tx.credit_leave || 0);
        totalDebit += parseFloat(tx.debit_leave || 0);
      });
      const sortedByDate = [...transactions].sort((a, b) => {
        const parseDate = (dateStr) => {
          const parts = dateStr.split(' ');
          const day = parseInt(parts[0]);
          const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
          const month = monthNames.indexOf(parts[1]);
          const year = parseInt(parts[2]);
          return new Date(year, month, day);
        };
        return parseDate(b.date) - parseDate(a.date);
      });

      if (sortedByDate.length > 0) {
        lastBalance = parseFloat(sortedByDate[0].balance_leave || 0);
      }
    }

    // Update footer totals
    $(`#total-credit-${year}`).text(totalCredit.toFixed(2));
    $(`#total-debit-${year}`).text(totalDebit.toFixed(2));
    $(`#total-balance-${year}`).text(lastBalance.toFixed(2));
  }

  // Calculate leave summary
  // ✅ Improved to get latest balance for each leave type
  function calculateLeaveSummary(transactionsByYear, leaveTypes) {
    const summary = {};
    leaveTypes.forEach(type => {
      summary[type.leave_type] = { total: 0, remaining: 0 };
    });

    Object.values(transactionsByYear).forEach(yearTransactions => {
      // Group transactions by leave type
      const byLeaveType = {};
      yearTransactions.forEach(tx => {
        if (!byLeaveType[tx.leave_type]) {
          byLeaveType[tx.leave_type] = [];
        }
        byLeaveType[tx.leave_type].push(tx);
      });

      // Calculate totals and get latest balance for each leave type
      Object.keys(byLeaveType).forEach(leaveType => {
        const txs = byLeaveType[leaveType];
        // Sort by date to get latest - date format is "dd MMM YYYY"
        const parseDate = (dateStr) => {
          const parts = dateStr.split(' ');
          const day = parseInt(parts[0]);
          const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
          const month = monthNames.indexOf(parts[1]);
          const year = parseInt(parts[2]);
          return new Date(year, month, day);
        };
        txs.sort((a, b) => {
          const dateA = parseDate(a.date);
          const dateB = parseDate(b.date);
          return dateB - dateA;
        });

        // Sum all credits and carried forward
        const total = txs.reduce((sum, tx) => sum + Number(tx.credit_leave || 0) + Number(tx.carried_forward_leave || 0), 0);
        // Get latest balance (first transaction after sorting)
        const remaining = Number(txs[0]?.balance_leave || 0);

        summary[leaveType] = {
          total: total.toFixed(2),
          remaining: remaining.toFixed(2)
        };
      });
    });

    return summary;
  }

  // Generate leave summary cards
  // ✅ Improved to show accurate balance from backend data and respect filters
  function generateLeaveSummaryCards(transactionsByYear, leaveTypes, leaveSummary, filters = {}) {
    let html = '';
    const currentYearStr = new Date().getFullYear().toString();
    let yearTransactions = transactionsByYear[currentYearStr] || [];

    // Apply leave type filter to transactions if filter is active
    if (filters.leaveTypes && filters.leaveTypes.length > 0) {
      yearTransactions = yearTransactions.filter(tx =>
        filters.leaveTypes.includes(tx.leave_type)
      );
    }

    // Apply credit/debit filter if active
    if (filters.creditDebitFilters && filters.creditDebitFilters.length > 0) {
      yearTransactions = yearTransactions.filter(tx => {
        if (filters.creditDebitFilters.includes('credit') && parseFloat(tx.credit_leave || 0) > 0) {
          return true;
        }
        if (filters.creditDebitFilters.includes('debit') && parseFloat(tx.debit_leave || 0) > 0) {
          return true;
        }
        return false;
      });
    }

    // Apply date range filter if active
    if (filters.dateRange) {
      const dateParts = filters.dateRange.split(' - ');
      if (dateParts.length === 2) {
        try {
          const startDate = moment(dateParts[0].trim(), 'DD/MM/YYYY');
          const endDate = moment(dateParts[1].trim(), 'DD/MM/YYYY');
          yearTransactions = yearTransactions.filter(tx => {
            const txDate = moment(tx.date, 'DD MMM YYYY');
            return txDate.isSameOrAfter(startDate) && txDate.isSameOrBefore(endDate);
          });
        } catch (e) {
          console.error('Error parsing date range:', e);
        }
      }
    }

    // Sort leave types by ID to match processing order
    // If filter is active, only show filtered leave types
    let sortedLeaveTypes = [...leaveTypes].sort((a, b) => a.id - b.id);

    if (filters.leaveTypes && filters.leaveTypes.length > 0) {
      sortedLeaveTypes = sortedLeaveTypes.filter(type =>
        filters.leaveTypes.includes(type.leave_type)
      );
    }

    sortedLeaveTypes.forEach(type => {
      const summary = leaveSummary[type.leave_type] || { total: 0, remaining: 0 };

      // Parse date helper - date format is "dd MMM YYYY"
      const parseDate = (dateStr) => {
        const parts = dateStr.split(' ');
        const day = parseInt(parts[0]);
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const month = monthNames.indexOf(parts[1]);
        const year = parseInt(parts[2]);
        return new Date(year, month, day);
      };

      const transactions = yearTransactions
        .filter(tx => tx.leave_type === type.leave_type)
        .sort((a, b) => {
          const dateA = parseDate(a.date);
          const dateB = parseDate(b.date);
          return dateB - dateA;
        });

      // Get latest balance - prefer from backend leaveTypes data, then from transaction, then from summary
      const latestTransaction = transactions[0];
      const balanceFromBackend = parseFloat(type.balance_leave || 0);
      const balanceFromTransaction = latestTransaction?.balance_leave ? parseFloat(latestTransaction.balance_leave) : 0;
      const balanceFromSummary = parseFloat(summary.remaining) || 0;

      // Use backend balance first (most accurate), fallback to transaction, then summary
      const latestBalance = balanceFromBackend > 0 ? balanceFromBackend : (balanceFromTransaction > 0 ? balanceFromTransaction : balanceFromSummary);

      // Total credits from summary or calculate from transactions
      const totalCredits = parseFloat(summary.total) || transactions.reduce((sum, tx) => sum + parseFloat(tx.credit_leave || 0), 0);

      // Get card styling - use leave color from backend or default
      let badgeBgClass = 'bg-secondary-transparent';
      const cardColor = type.leave_color || 'bg-primary-le';

      // Map card colors to badge colors
      if (type.leave_color) {
        if (type.leave_color.includes('black')) {
          badgeBgClass = 'bg-secondary-transparent';
        } else if (type.leave_color.includes('blue')) {
          badgeBgClass = 'bg-info-transparent';
        } else if (type.leave_color.includes('purple')) {
          badgeBgClass = 'bg-transparent-purple';
        } else if (type.leave_color.includes('pink')) {
          badgeBgClass = 'bg-pink-transparent';
        } else {
          badgeBgClass = type.leave_color.replace('-le', '-transparent');
        }
      }

      const leaveIcon = type.leave_icon || 'ti ti-calendar-event';
      const displayName = type.leave_type || 'Leave';

      html += `
        <div class="col-xl-3 col-md-6">
          <div class="card ${cardColor}">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between">
                <div class="text-start">
                  <p class="mb-1">${displayName}</p>
                  <h4>${totalCredits.toFixed(2)}</h4>
                </div>
                <div class="d-flex">
                  <div class="flex-shrink-0 me-2">
                    <span class="avatar avatar-md d-flex">
                      <i class="${leaveIcon} fs-32"></i>
                    </span>
                  </div>
                </div>
              </div>
              <span class="badge ${badgeBgClass}">
                Remaining Leaves: ${latestBalance.toFixed(2)}
              </span>
            </div>
          </div>
        </div>`;
    });

    // Only update if we have leave types
    if (sortedLeaveTypes.length > 0) {
      cardsWrapper.html(html);
    } else {
      cardsWrapper.html('<div class="col-12"><p class="text-center text-muted">No leave types found</p></div>');
    }
  }

  // Leave type filter toggle
  // ✅ Improved to show visual feedback and update dynamically
  wrapper.on('click', '.leave-type', function (e) {
    e.preventDefault();
    $(this).toggleClass('selected');

    // Update button text to show selected count
    const year = $(this).data('year');
    const selectedCount = $(`#leaveTypeDropdown-${year} .leave-type.selected`).length;
    const leaveTypeBtn = $(`.leave-type-filter[data-year="${year}"] .dropdown-toggle`);

    if (selectedCount > 0) {
      leaveTypeBtn.html(`Leave Type (${selectedCount})`);
      leaveTypeBtn.addClass('active');

      // Show remove button immediately for leave type
      const leaveRemoveBtn = $(`.remove-leave-filter[data-year="${year}"]`);
      if (leaveRemoveBtn.length > 0) {
        leaveRemoveBtn.removeClass('d-none').show();
      }
    } else {
      leaveTypeBtn.html('Leave Type');
      leaveTypeBtn.removeClass('active');

      // Hide remove button if no selection
      const leaveRemoveBtn = $(`.remove-leave-filter[data-year="${year}"]`);
      if (leaveRemoveBtn.length > 0) {
        leaveRemoveBtn.addClass('d-none').hide();
      }
    }

    // Apply filters (updateRemoveButton will be called after data loads)
    applyFilters(year);
  });


  // Credit/debit filter toggle
  // ✅ Improved to show visual feedback and update dynamically
  wrapper.on('click', '.credit-debit', function (e) {
    e.preventDefault();
    $(this).toggleClass('selected');

    // Update button text to show selected count
    const year = $(this).data('year');
    const selectedCount = $(`#creditDebitFilter-${year} .credit-debit.selected`).length;
    const creditDebitBtn = $(`.credit-debit-filter[data-year="${year}"] .dropdown-toggle`);

    if (selectedCount > 0) {
      creditDebitBtn.html(`Filter Credit/Debit (${selectedCount})`);
      creditDebitBtn.addClass('active');

      // Show remove button immediately for credit/debit
      const creditRemoveBtn = $(`.remove-credit-filter[data-year="${year}"]`);
      if (creditRemoveBtn.length > 0) {
        creditRemoveBtn.removeClass('d-none').show();
      }
    } else {
      creditDebitBtn.html('Filter Credit/Debit');
      creditDebitBtn.removeClass('active');

      // Hide remove button if no selection
      const creditRemoveBtn = $(`.remove-credit-filter[data-year="${year}"]`);
      if (creditRemoveBtn.length > 0) {
        creditRemoveBtn.addClass('d-none').hide();
      }
    }

    // Apply filters (updateRemoveButton will be called after data loads)
    applyFilters(year);
  });


  // Add/remove "Remove" button in dropdown
  // ✅ Improved to show/hide remove buttons based on filter state
  function updateRemoveButton(year) {
    if (!year) {
      // If no year provided, try to get from current context
      year = new Date().getFullYear();
    }

    // Check filter states with better error handling
    const dateRangeInput = $(`#dateRange-${year}`);
    const dateRangeVal = dateRangeInput.length > 0 ? (dateRangeInput.val() || '').trim() : '';

    const leaveTypeDropdown = $(`#leaveTypeDropdown-${year}`);
    const leaveTypeSelected = leaveTypeDropdown.length > 0 ? leaveTypeDropdown.find('.leave-type.selected').length > 0 : false;

    const creditDebitFilter = $(`#creditDebitFilter-${year}`);
    const creditDebitSelected = creditDebitFilter.length > 0 ? creditDebitFilter.find('.credit-debit.selected').length > 0 : false;

    // Show/hide individual remove buttons - only update if element exists
    // Date Range Filter - show if date range has value
    const dateRemoveBtn = $(`.remove-date-filter[data-year="${year}"]`);
    if (dateRemoveBtn.length > 0) {
      if (dateRangeVal !== '') {
        dateRemoveBtn.removeClass('d-none').css('display', 'inline-block');
      } else {
        dateRemoveBtn.addClass('d-none').css('display', 'none');
      }
    }

    // Leave Type Filter - show if any leave type is selected
    const leaveRemoveBtn = $(`.remove-leave-filter[data-year="${year}"]`);
    if (leaveRemoveBtn.length > 0) {
      if (leaveTypeSelected) {
        leaveRemoveBtn.removeClass('d-none').css('display', 'inline-block');
      } else {
        leaveRemoveBtn.addClass('d-none').css('display', 'none');
      }
    }

    // Credit/Debit Filter - show if any credit/debit is selected
    const creditRemoveBtn = $(`.remove-credit-filter[data-year="${year}"]`);
    if (creditRemoveBtn.length > 0) {
      if (creditDebitSelected) {
        creditRemoveBtn.removeClass('d-none').css('display', 'inline-block');
      } else {
        creditRemoveBtn.addClass('d-none').css('display', 'none');
      }
    }

    // Optional: Show a "Clear All Filters" button if any filter is active
    const anyFilterActive = dateRangeVal !== '' || leaveTypeSelected || creditDebitSelected;
    let clearAllBtn = $(`.clear-all-filters[data-year="${year}"]`);

    if (anyFilterActive) {
      if (clearAllBtn.length === 0) {
        // Add clear all button if it doesn't exist
        const filterControlsContainer = $(`.filter-controls[data-year="${year}"]`).last().parent();
        if (filterControlsContainer.length > 0) {
          clearAllBtn = $(`
            <button class="btn btn-sm btn-outline-danger ms-2 clear-all-filters" data-year="${year}" title="Clear All Filters">
              <i class="ti ti-x me-1"></i>Clear All
            </button>
          `);
          filterControlsContainer.append(clearAllBtn);
          clearAllBtn.on('click', function() {
            clearAllFilters(year);
          });
        }
      }
    } else {
      if (clearAllBtn.length > 0) {
        clearAllBtn.remove();
      }
    }
  }

  // Clear all filters function - Removes ALL filters at once
  function clearAllFilters(year) {
    // Clear date range
    const dateInput = $(`#dateRange-${year}`);
    dateInput.val('');
    if (dateInput.data('daterangepicker')) {
      const drp = dateInput.data('daterangepicker');
      drp.setStartDate(moment());
      drp.setEndDate(moment());
    }
    dateInput.removeClass('border-primary');

    // Clear leave type selections
    $(`#leaveTypeDropdown-${year} .leave-type.selected`).removeClass('selected');
    const leaveTypeBtn = $(`.leave-type-filter[data-year="${year}"] .dropdown-toggle`);
    if (leaveTypeBtn.length > 0) {
      leaveTypeBtn.html('Leave Type').removeClass('active');
    }

    // Clear credit/debit selections
    $(`#creditDebitFilter-${year} .credit-debit.selected`).removeClass('selected');
    const creditDebitBtn = $(`.credit-debit-filter[data-year="${year}"] .dropdown-toggle`);
    if (creditDebitBtn.length > 0) {
      creditDebitBtn.html('Filter Credit/Debit').removeClass('active');
    }

    // Hide all remove buttons
    $(`.remove-date-filter[data-year="${year}"]`).addClass('d-none').hide();
    $(`.remove-leave-filter[data-year="${year}"]`).addClass('d-none').hide();
    $(`.remove-credit-filter[data-year="${year}"]`).addClass('d-none').hide();

    // Update remove buttons
    updateRemoveButton(year);

    // Apply filters (with no filters = show all)
    applyFilters(year);
  }


  // Remove Leave Type Filter - Independent removal
  wrapper.on('click', '.remove-leave-filter', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const year = $(this).data('year');

    // Remove all selected leave types (only this filter)
    $(`#leaveTypeDropdown-${year} .leave-type.selected`).removeClass('selected');

    // Reset button text
    const leaveTypeBtn = $(`.leave-type-filter[data-year="${year}"] .dropdown-toggle`);
    if (leaveTypeBtn.length > 0) {
      leaveTypeBtn.html('Leave Type').removeClass('active');
    }

    // Hide this remove button only
    $(this).addClass('d-none').hide();

    // Update remove button visibility (other filters remain active)
    updateRemoveButton(year);

    // Apply filters (other filters like date range and credit/debit remain active)
    applyFilters(year);
  });

  // Remove Credit/Debit Filter - Independent removal
  wrapper.on('click', '.remove-credit-filter', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const year = $(this).data('year');

    // Remove all selected credit/debit filters (only this filter)
    $(`#creditDebitFilter-${year} .credit-debit.selected`).removeClass('selected');

    // Reset button text
    const creditDebitBtn = $(`.credit-debit-filter[data-year="${year}"] .dropdown-toggle`);
    if (creditDebitBtn.length > 0) {
      creditDebitBtn.html('Filter Credit/Debit').removeClass('active');
    }

    // Hide this remove button only
    $(this).addClass('d-none').hide();

    // Update remove button visibility (other filters remain active)
    updateRemoveButton(year);

    // Apply filters (other filters like date range and leave type remain active)
    applyFilters(year);
  });




  // Remove Date Range Filter - Independent removal
  wrapper.on('click', '.remove-date-filter', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const year = $(this).data('year');
    const dateInput = $(`#dateRange-${year}`);

    // Clear date range value (only this filter)
    dateInput.val('');

    // Reset date range picker if it exists
    if (dateInput.data('daterangepicker')) {
      const drp = dateInput.data('daterangepicker');
      drp.setStartDate(moment());
      drp.setEndDate(moment());
      drp.hide();
    }

    // Remove active state
    dateInput.removeClass('border-primary');

    // Hide this remove button only
    $(this).addClass('d-none').hide();

    // Update remove button visibility (other filters remain active)
    updateRemoveButton(year);

    // Apply filters (other filters like leave type and credit/debit remain active)
    applyFilters(year);
  });

  // Apply filters and fetch filtered data
  // ✅ All filters work independently - you can add/remove any filter anytime
  // ✅ Filters work together with AND logic (all active filters must match)
  // ✅ Uses DataTables server-side processing
  function applyFilters(year) {
    if (!year) return;

    // Collect all active filters independently
    const dateRange = $(`#dateRange-${year}`).val() || '';
    const leaveTypes = [];
    $(`#leaveTypeDropdown-${year} .leave-type.selected`).each(function () {
      leaveTypes.push($(this).data('type'));
    });
    const creditDebitFilters = [];
    $(`#creditDebitFilter-${year} .credit-debit.selected`).each(function () {
      creditDebitFilters.push($(this).data('type'));
    });

    const filters = { dateRange, leaveTypes, creditDebitFilters };

    // Reload DataTable with new filters
    const dataTable = dataTableInstances[year];
    if (dataTable && $.fn.DataTable.isDataTable(`#leaveTable-${year}`)) {
      // Update the ajax data function to include current filters
      dataTable.settings()[0].ajax.data = function(d) {
        d.year = year;
        d.dateRange = filters.dateRange;
        d.leaveTypes = filters.leaveTypes;
        d.creditDebitFilters = filters.creditDebitFilters;
      };

      // Reload table with new filters
      dataTable.ajax.reload(null, false); // false = don't reset paging
    } else {
      // If DataTable not initialized yet, initialize it
      // Accordion should already exist, so just initialize the DataTable
      if ($(`#leaveTable-${year}`).length > 0) {
        initDataTable(year, filters);
      }
      // Don't call fetchLeaveData here as it might regenerate accordions
      // Data should already be loaded in allTransactionsData
    }
  }

  // Prevent accordions from closing when another is opened
  // All accordions should remain open independently
  // Data is already loaded upfront, so we don't need to fetch on expand
  // Accordions just toggle visibility - data stays in memory

  // Open Manage Columns Modal
  $('#manageColumnsModal').on('show.bs.modal', function() {
    populateColumnsModal();
  });

  // Show All Columns
  $('#showAllColumns').on('click', function() {
    $('.column-toggle').prop('checked', true);
  });

  // Hide All Columns
  $('#hideAllColumns').on('click', function() {
    $('.column-toggle').prop('checked', false);
  });

  // Apply Column Changes
  $('#applyColumnChanges').on('click', function() {
    $('.column-toggle').each(function() {
      const columnIndex = parseInt($(this).data('column-index'));
      const isVisible = $(this).is(':checked');
      const col = columnDefinitions.find(c => c.index === columnIndex);
      if (col) {
        col.visible = isVisible;
      }
    });

    saveColumnPreferences();

    // Apply column visibility to all DataTables
    applyColumnVisibilityToAllTables();

    // Close modal
    $('#manageColumnsModal').modal('hide');
  });
});

</script>
@endpush

@endsection
