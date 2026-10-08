@extends('layouts.main')
@section('title', 'Finance Dashboard')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">Finance & Expense Dashboard</h4>
                </div>
                <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                    <div class="d-inline-block" style="width: 250px;">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="ti ti-calendar"></i></span>
                            <input type="text" class="form-control bookingrange" id="dashboardDateRange" placeholder="Select Date Range">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100 flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5>Pending Allowances</h5>
                                <h3 class="mt-2" id="stat-pending-allowance">₹0.00</h3>
                            </div>
                            <div class="dash-icon bg-warning-light">
                                <i class="ti ti-wallet fs-30 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100 flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5>Approved Allowances</h5>
                                <h3 class="mt-2" id="stat-approved-allowance">₹0.00</h3>
                            </div>
                            <div class="dash-icon bg-success-light">
                                <i class="ti ti-wallet fs-30 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="mb-3">Expense Overview</h5>
        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100 flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5>Pending Expenses</h5>
                                <h3 class="mt-2" id="stat-pending-amount">₹0.00</h3>
                            </div>
                            <div class="dash-icon bg-warning-light">
                                <i class="ti ti-clock fs-30 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100 flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5>Approved Expenses</h5>
                                <h3 class="mt-2" id="stat-approved-amount">₹0.00</h3>
                            </div>
                            <div class="dash-icon bg-info-light">
                                <i class="ti ti-check fs-30 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100 flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5>Paid Expenses</h5>
                                <h3 class="mt-2" id="stat-paid-amount">₹0.00</h3>
                            </div>
                            <div class="dash-icon bg-success-light">
                                <i class="ti ti-cash fs-30 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 d-flex">
                <div class="card w-100 flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5>Rejected Expenses</h5>
                                <h3 class="mt-2" id="stat-rejected-amount">₹0.00</h3>
                            </div>
                            <div class="dash-icon bg-danger-light">
                                <i class="ti ti-x fs-30 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Expenses By Status Chart -->
            <div class="col-md-6 d-flex">
                <div class="card w-100 flex-fill">
                    <div class="card-header">
                        <h5 class="card-title">Expenses by Status</h5>
                    </div>
                    <div class="card-body">
                        <div id="status-chart"></div>
                    </div>
                </div>
            </div>
            <!-- Expenses By Category Chart -->
            <div class="col-md-6 d-flex">
                <div class="card w-100 flex-fill">
                    <div class="card-header">
                        <h5 class="card-title">Expenses by Category</h5>
                    </div>
                    <div class="card-body">
                        <div id="category-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Expense Trend Chart -->
            <div class="col-md-12 d-flex">
                <div class="card w-100 flex-fill">
                    <div class="card-header">
                        <h5 class="card-title">Expense Trend (Volume)</h5>
                    </div>
                    <div class="card-body">
                        <div id="trend-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Recent Expense Activity</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Employee</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-activity-body">
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    
    // Initialize Date Range Picker
    var start = moment().subtract(6, 'days');
    var end = moment();
    
    function cb(start, end) {
        $('#dashboardDateRange').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        loadDashboardData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
    }

    $('#dashboardDateRange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: { format: 'DD/MM/YYYY' }
    }, cb);
    
    // Initial Load
    cb(start, end);

    function loadDashboardData(startDate, endDate) {
        $.ajax({
            url: "{{ route('finance.dashboard.data', ['box_slug' => session('box_slug', request()->route('box_slug'))]) }}",
            type: 'GET',
            data: { start_date: startDate, end_date: endDate },
            dataType: 'json',
            success: function(response) {
                $('#stat-pending-amount').text('₹' + parseFloat(response.pending_amount).toFixed(2));
                $('#stat-approved-amount').text('₹' + parseFloat(response.approved_amount).toFixed(2));
                $('#stat-paid-amount').text('₹' + parseFloat(response.paid_amount).toFixed(2));
                $('#stat-rejected-amount').text('₹' + parseFloat(response.rejected_amount).toFixed(2));
                
                $('#stat-pending-allowance').text('₹' + parseFloat(response.allowances.pending).toFixed(2));
                $('#stat-approved-allowance').text('₹' + parseFloat(response.allowances.approved).toFixed(2));
                
                // Populate recent activity
                var tbody = $('#recent-activity-body');
                tbody.empty();
                
                if(response.recent_expenses && response.recent_expenses.length > 0) {
                    $.each(response.recent_expenses, function(index, expense) {
                        var empName = expense.box_user && expense.box_user.user ? expense.box_user.user.name + ' ' + (expense.box_user.user.last_name || '') : 'Unknown';
                        var catName = expense.category ? expense.category.name : 'Unknown';
                        var statusBadge = '';
                        
                        if(expense.status === 'Pending') statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
                        else if(expense.status === 'Approved') statusBadge = '<span class="badge bg-info">Approved</span>';
                        else if(expense.status === 'Paid') statusBadge = '<span class="badge bg-success">Paid</span>';
                        else if(expense.status === 'Rejected') statusBadge = '<span class="badge bg-danger">Rejected</span>';
                        else statusBadge = '<span class="badge bg-secondary">' + expense.status + '</span>';
                        
                        var date = new Date(expense.created_at).toLocaleDateString();
                        
                        var row = '<tr>' +
                            '<td>' + date + '</td>' +
                            '<td>' + empName + '</td>' +
                            '<td>' + catName + '</td>' +
                            '<td>₹' + parseFloat(expense.amount).toFixed(2) + '</td>' +
                            '<td>' + statusBadge + '</td>' +
                            '</tr>';
                        tbody.append(row);
                    });
                } else {
                    tbody.append('<tr><td colspan="5" class="text-center py-4 text-muted">No recent activity found.</td></tr>');
                }
                
                // Render Trend Chart
                if(window.trendChartInstance) { window.trendChartInstance.destroy(); }
                var trendLabels = [];
                var trendData = [];
                if(response.expense_trend && response.expense_trend.length > 0) {
                    $.each(response.expense_trend, function(i, item) {
                        trendLabels.push(item.date);
                        trendData.push(item.count);
                    });
                    
                    var trendOptions = {
                        series: [{ name: 'Expenses', data: trendData }],
                        chart: { type: 'area', height: 280, toolbar: { show: false } },
                        colors: ['#0d6efd'],
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2 },
                        xaxis: { categories: trendLabels, type: 'datetime' },
                        tooltip: { x: { format: 'dd MMM yyyy' } }
                    };
                    window.trendChartInstance = new ApexCharts(document.querySelector("#trend-chart"), trendOptions);
                    window.trendChartInstance.render();
                } else {
                    $('#trend-chart').html('<p class="text-center text-muted mt-5">No trend data available.</p>');
                }
                
                // Render Status Pie Chart
                if(window.statusChartInstance) { window.statusChartInstance.destroy(); }
                var statusData = [
                    parseFloat(response.pending_amount),
                    parseFloat(response.approved_amount),
                    parseFloat(response.paid_amount),
                    parseFloat(response.rejected_amount)
                ];
                
                if (statusData.reduce((a, b) => a + b, 0) > 0) {
                    var statusOptions = {
                        series: statusData,
                        chart: { type: 'pie', height: 320 },
                        labels: ['Pending', 'Approved', 'Paid', 'Rejected'],
                        colors: ['#ffc107', '#0dcaf0', '#198754', '#dc3545'],
                        legend: { position: 'bottom' },
                        dataLabels: { enabled: true, formatter: function (val) { return val.toFixed(1) + "%" } },
                        tooltip: { y: { formatter: function(val) { return '₹' + val.toFixed(2); } } }
                    };
                    window.statusChartInstance = new ApexCharts(document.querySelector("#status-chart"), statusOptions);
                    window.statusChartInstance.render();
                } else {
                    $('#status-chart').html('<p class="text-center text-muted mt-5">No data available for chart.</p>');
                }
    
                // Render Category Bar Chart
                if(window.categoryChartInstance) { window.categoryChartInstance.destroy(); }
                var catLabels = [];
                var catData = [];
                if(response.category_expenses && response.category_expenses.length > 0) {
                    $.each(response.category_expenses, function(i, item) {
                        catLabels.push(item.category_name);
                        catData.push(parseFloat(item.total_amount));
                    });
                    
                    var catOptions = {
                        series: [{ name: 'Total Amount', data: catData }],
                        chart: { type: 'bar', height: 320, toolbar: { show: false } },
                        plotOptions: { bar: { borderRadius: 4, horizontal: true, distributed: true } },
                        colors: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#fd7e14', '#20c997', '#0dcaf0'],
                        dataLabels: { enabled: false },
                        xaxis: { categories: catLabels, labels: { formatter: function(val) { return '₹' + val; } } },
                        legend: { show: false },
                        tooltip: { y: { formatter: function(val) { return '₹' + val.toFixed(2); } } }
                    };
                    window.categoryChartInstance = new ApexCharts(document.querySelector("#category-chart"), catOptions);
                    window.categoryChartInstance.render();
                } else {
                    $('#category-chart').html('<p class="text-center text-muted mt-5">No data available for chart.</p>');
                }
            },
            error: function() {
                $('#recent-activity-body').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load data.</td></tr>');
            }
        });
    }
});
</script>
@endpush
@endsection
