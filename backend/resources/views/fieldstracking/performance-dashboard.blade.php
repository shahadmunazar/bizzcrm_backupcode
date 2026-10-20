@extends('layouts.main')
@section('title', 'Performance Dashboard')

@section('content')
<div class="page-wrapper">
<div class="content">

    <!-- Breadcrumb -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1">Performance Dashboard</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ box_route('fieldstracking.tracking-dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Field Tracking</li>
                    <li class="breadcrumb-item active" aria-current="page">Performance Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
            <div class="me-2 mb-2">
                <button class="btn btn-white border d-inline-flex align-items-center" id="exportPerformance">
                    <i class="ti ti-file-export me-1"></i>Export Report
                </button>
            </div>
            <div class="ms-2 head-icons">
                <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
                    <i class="ti ti-chevrons-up"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Filter Card -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div>
                    <h5 class="mb-1">Performance Filters</h5>
                    <p class="text-muted mb-0 fs-13">Filter performance by month, department, designation and employee.</p>
                </div>
                <button type="button" class="btn btn-light btn-sm" id="resetPerformanceFilters">
                    <i class="ti ti-refresh me-1"></i>Reset
                </button>
            </div>
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="mb-0">
                        <label class="form-label">Month & Year</label>
                        <input type="month" class="form-control" id="filterMonth" value="{{ date('Y-m') }}">
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="mb-0">
                        <label class="form-label">Department</label>
                        <select class="form-select select2" id="filterDepartment">
                            <option value="">All Departments</option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="mb-0">
                        <label class="form-label">Designation / Team</label>
                        <select class="form-select select2" id="filterTeam">
                            <option value="">All Teams</option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="mb-0">
                        <label class="form-label">Employee</label>
                        <select class="form-select select2" id="filterEmployee">
                            <option value="">All Employees</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Filter Card -->

    <!-- KPI Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <span class="avatar rounded-circle bg-primary mb-2"><i class="ti ti-users fs-16"></i></span>
                    <h6 class="fs-13 fw-medium text-default mb-1">Total Assigned Entities</h6>
                    <h3 class="mb-2" id="kpiEmployees">0</h3>
                    <span class="fs-12 text-success"><i class="fa-solid fa-caret-up me-1"></i></span>
                    <span class="fs-12 text-muted ms-1">Active Profiles</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <span class="avatar rounded-circle bg-success mb-2"><i class="ti ti-chart-line fs-16"></i></span>
                    <h6 class="fs-13 fw-medium text-default mb-1">Average Performance</h6>
                    <h3 class="mb-2" id="kpiAverage">0%</h3>
                    <span class="fs-12 text-success"><i class="fa-solid fa-caret-up me-1"></i></span>
                    <span class="fs-12 text-muted ms-1">Overall Achievement</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <span class="avatar rounded-circle bg-warning mb-2"><i class="ti ti-award fs-16"></i></span>
                    <h6 class="fs-13 fw-medium text-default mb-1">Top Performers</h6>
                    <h3 class="mb-2" id="kpiTop">0</h3>
                    <span class="fs-12 text-muted">90% and above</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <span class="avatar rounded-circle bg-danger mb-2"><i class="ti ti-alert-triangle fs-16"></i></span>
                    <h6 class="fs-13 fw-medium text-default mb-1">Needs Improvement</h6>
                    <h3 class="mb-2" id="kpiImprove">0</h3>
                    <span class="fs-12 text-muted ms-1">Below 50%</span>
                </div>
            </div>
        </div>
    </div>
    <!-- /KPI Cards -->

    <div class="row">
        <!-- Department Performance -->
        <div class="col-xxl-6 col-xl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                    <div>
                        <h5 class="mb-1">Department Performance</h5>
                        <p class="text-muted mb-0 fs-13">Average performance score by department</p>
                    </div>
                    <span class="badge badge-soft-primary">Target: 85%</span>
                </div>
                <div class="card-body">
                    <div id="departmentPerformanceChart" style="min-height:330px;"></div>
                </div>
            </div>
        </div>

        <!-- Performance Distribution -->
        <div class="col-xxl-3 col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header">
                    <h5 class="mb-1">Performance Distribution</h5>
                    <p class="text-muted mb-0 fs-13">Employees by score range</p>
                </div>
                <div class="card-body">
                    <div id="performanceDistributionChart" style="min-height:330px;"></div>
                </div>
            </div>
        </div>

        <!-- Top Performer -->
        <div class="col-xxl-3 col-xl-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Top Performer</h5>
                        <p class="text-muted mb-0 fs-13">Highest score</p>
                    </div>
                    <ul class="nav nav-pills nav-sm" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active px-2 py-1 fs-12" id="pills-this-month-tab" data-bs-toggle="pill" data-bs-target="#pills-this-month" type="button" role="tab">This Month</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-2 py-1 fs-12" id="pills-last-month-tab" data-bs-toggle="pill" data-bs-target="#pills-last-month" type="button" role="tab">Last Month</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-this-month" role="tabpanel">
                            <div class="p-3 border border-primary bg-primary-100 br-5 text-center">
                                <span class="avatar avatar-xl mx-auto mb-2">
                                    <img src="https://ui-avatars.com/api/?name=User&background=random" id="topEmployeeAvatar" class="rounded-circle border border-white" alt="Employee">
                                </span>
                                <h5 class="mb-1" id="topEmployeeName">No Data</h5>
                                <p class="fs-13 mb-2" id="topEmployeeRole">-</p>
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <i class="ti ti-award-filled text-primary fs-24 me-2"></i>
                                    <h2 class="text-primary mb-0" id="topEmployeeScore">0%</h2>
                                </div>
                                <span class="badge badge-soft-success">Excellent</span>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-last-month" role="tabpanel">
                            <div class="p-3 border border-warning bg-warning-100 br-5 text-center">
                                <span class="avatar avatar-xl mx-auto mb-2">
                                    <img src="https://ui-avatars.com/api/?name=User&background=random" id="lmTopEmployeeAvatar" class="rounded-circle border border-white" alt="Employee">
                                </span>
                                <h5 class="mb-1" id="lmTopEmployeeName">No Data</h5>
                                <p class="fs-13 mb-2">Previous Month Star</p>
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <i class="ti ti-medal text-warning fs-24 me-2"></i>
                                </div>
                                <span class="badge badge-soft-warning">Last Month Champion</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Designation Performance -->
        <div class="col-xxl-5 col-xl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-1">Designation Performance</h5>
                        <p class="text-muted mb-0 fs-13">Performance by role/designation</p>
                    </div>
                    <span class="badge badge-soft-info">Target Model</span>
                </div>
                <div class="card-body">
                    <div id="designationPerformanceChart" style="min-height:330px;"></div>
                </div>
            </div>
        </div>

        <!-- Performance Trend -->
        <div class="col-xxl-7 col-xl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-1">Performance Trend</h5>
                        <p class="text-muted mb-0 fs-13">Monthly average score</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-white border btn-sm dropdown-toggle" data-bs-toggle="dropdown">2026</button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="javascript:void(0);">2026</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0);">2025</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div id="performanceTrendChart" style="min-height:330px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Performance Header with View Switcher -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h5 class="mb-1">Detailed Performance</h5>
            <p class="text-muted mb-0 fs-13">List of targets, their assignment and achievements.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- View Mode Switcher -->
            <div class="btn-group p-1 bg-light border rounded" role="group" aria-label="View Mode">
                <button type="button" class="btn btn-sm btn-white active shadow-sm" id="viewModeGrid" title="Cards / Grid View">
                    <i class="ti ti-layout-grid me-1"></i> Cards View
                </button>
                <button type="button" class="btn btn-sm text-muted" id="viewModeTable" title="Table View">
                    <i class="ti ti-table me-1"></i> Table View
                </button>
            </div>
        </div>
    </div>
    
    <!-- Cards / Grid View -->
    <div class="row" id="performance_grid">
        <div class="col-12 text-center text-muted py-4">Loading...</div>
    </div>

    <!-- Table View -->
    <div class="card mb-4 d-none" id="performance_table_card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="performance_table">
                    <thead class="thead-light table-light">
                        <tr>
                            <th class="ps-3" style="width: 70px;">Rank</th>
                            <th>Assignee</th>
                            <th>Target Template</th>
                            <th>Period</th>
                            <th style="min-width: 250px;">Metrics Breakdown (Done / Target)</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Achievement Score</th>
                        </tr>
                    </thead>
                    <tbody id="performance_tbody">
                        <tr><td colspan="7" class="text-center text-muted py-4">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(document).ready(function() {
    const boxSlug = '{{ request()->route("box_slug") }}';
    let performanceChart = null;
    let deptChart = null;
    let desigChart = null;
    let trendChart = null;

    if($('.select2').length > 0) {
        $('.select2').select2({width: '100%'});
    }

    function setViewMode(mode) {
        if (mode === 'table') {
            $('#viewModeTable').addClass('btn-white active shadow-sm').removeClass('text-muted');
            $('#viewModeGrid').removeClass('btn-white active shadow-sm').addClass('text-muted');
            $('#performance_grid').addClass('d-none');
            $('#performance_table_card').removeClass('d-none');
        } else {
            $('#viewModeGrid').addClass('btn-white active shadow-sm').removeClass('text-muted');
            $('#viewModeTable').removeClass('btn-white active shadow-sm').addClass('text-muted');
            $('#performance_grid').removeClass('d-none');
            $('#performance_table_card').addClass('d-none');
        }
        localStorage.setItem('perf_dashboard_view', mode);
    }

    $('#viewModeGrid').click(function() { setViewMode('grid'); });
    $('#viewModeTable').click(function() { setViewMode('table'); });
    let savedView = localStorage.getItem('perf_dashboard_view') || 'grid';
    setViewMode(savedView);
    
    // Load Departments
    $.get('/' + boxSlug + '/fieldstracking/dashboard-api/departments', function(data) {
        let select = $('#filterDepartment');
        data.forEach(dept => {
            select.append(`<option value="${dept.id}">${dept.name}</option>`);
        });
        loadTeams();
        loadEmployees();
        loadPerformance();
    });

    $('#filterMonth').change(function() {
        loadPerformance();
    });

    $('#filterDepartment').change(function() {
        $('#filterTeam').val('').trigger('change.select2');
        $('#filterEmployee').val('').trigger('change.select2');
        loadTeams();
        loadEmployees();
        loadPerformance();
    });

    $('#filterTeam').change(function() {
        $('#filterEmployee').val('').trigger('change.select2');
        loadEmployees();
        loadPerformance();
    });

    $('#filterEmployee').change(function() {
        loadPerformance();
    });
    
    $('#resetPerformanceFilters').click(function() {
        $('#filterMonth').val('{{ date('Y-m') }}');
        $('#filterDepartment').val('').trigger('change.select2');
        $('#filterTeam').val('').trigger('change.select2');
        $('#filterEmployee').val('').trigger('change.select2');
        loadTeams();
        loadEmployees();
        loadPerformance();
    });

    function loadTeams() {
        let deptId = $('#filterDepartment').val();
        let select = $('#filterTeam');
        select.find('option:not(:first)').remove();
        
        let url = '/' + boxSlug + '/fieldstracking/dashboard-api/teams';
        if (deptId) {
            url += '?department_id=' + deptId;
        }
        
        $.get(url, function(data) {
            data.forEach(team => {
                select.append(`<option value="${team.id}">${team.name}</option>`);
            });
        });
    }

    function loadEmployees() {
        let deptId = $('#filterDepartment').val();
        let teamId = $('#filterTeam').val();
        let select = $('#filterEmployee');
        select.find('option:not(:first)').remove();
        
        let url = '/' + boxSlug + '/fieldstracking/dashboard-api/employees?t=1';
        if (deptId) url += '&department_id=' + deptId;
        if (teamId) url += '&team_id=' + teamId;
        
        $.get(url, function(data) {
            data.forEach(emp => {
                let name = (emp.first_name || '') + ' ' + (emp.last_name || '');
                if (!name.trim() && emp.user) name = emp.user.name;
                select.append(`<option value="${emp.id}">${name}</option>`);
            });
        });
    }

    function loadPerformance() {
        let monthFilter = $('#filterMonth').val();
        let deptId = $('#filterDepartment').val();
        let teamId = $('#filterTeam').val();
        let empId = $('#filterEmployee').val();

        let url = '/' + boxSlug + '/fieldstracking/dashboard-api/performance?t=1';
        
        if (monthFilter) {
            let parts = monthFilter.split('-');
            let year = parts[0];
            let month = parts[1];
            let startDate = monthFilter + '-01';
            let lastDay = new Date(year, month, 0).getDate();
            let endDate = monthFilter + '-' + (lastDay < 10 ? '0' + lastDay : lastDay);
            
            url += '&start_date=' + startDate + '&end_date=' + endDate + '&month=' + monthFilter + '&year=' + year;
        }
        
        if (deptId) url += '&department_id=' + deptId;
        if (teamId) url += '&team_id=' + teamId;
        if (empId) url += '&employee_id=' + empId;

        $('#performance_tbody').html('<tr><td colspan="7" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Loading detailed performance...</td></tr>');
        $('#performance_grid').html('<div class="col-12 text-center text-muted py-5"><span class="spinner-border spinner-border-sm me-2"></span>Loading detailed performance...</div>');
        
        $.get(url, function(response) {
            
            let totalAssigned = response.stats.all_performers.length + response.stats.team_performers.length;
            $('#kpiEmployees').text(totalAssigned);
            $('#kpiAverage').text(response.stats.overall_achievement + '%');
            
            let topCount = 0;
            let lowCount = 0;
            let ranges = [0, 0, 0, 0]; // 90-100, 80-89, 70-79, <70
            
            let highestScore = -1;
            let highestPerformer = null;

            response.stats.all_performers.forEach(p => {
                let score = parseFloat(p.avg_score);
                if(score >= 90) topCount++;
                if(score < 50) lowCount++;
                
                if(score >= 90) ranges[0]++;
                else if(score >= 80) ranges[1]++;
                else if(score >= 70) ranges[2]++;
                else ranges[3]++;
                
                if(score > highestScore) {
                    highestScore = score;
                    highestPerformer = p;
                }
            });
            
            response.stats.team_performers.forEach(p => {
                let score = parseFloat(p.avg_score);
                if(score >= 90) topCount++;
                if(score < 50) lowCount++;
                
                if(score >= 90) ranges[0]++;
                else if(score >= 80) ranges[1]++;
                else if(score >= 70) ranges[2]++;
                else ranges[3]++;
            });

            $('#kpiTop').text(topCount);
            $('#kpiImprove').text(lowCount);
            
            if(highestPerformer) {
                $('#topEmployeeName').text(highestPerformer.name);
                $('#topEmployeeScore').text(highestScore.toFixed(2) + '%');
                $('#topEmployeeRole').text('Employee');
                $('#topEmployeeAvatar').attr('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(highestPerformer.name) + '&background=random');
            } else {
                $('#topEmployeeName').text('No Data');
                $('#topEmployeeScore').text('0%');
                $('#topEmployeeRole').text('-');
            }

            if(response.stats.last_month_performer) {
                $('#lmTopEmployeeName').text(response.stats.last_month_performer);
                $('#lmTopEmployeeAvatar').attr('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(response.stats.last_month_performer) + '&background=random');
            } else {
                $('#lmTopEmployeeName').text('No Data');
            }

            if(performanceChart) {
                performanceChart.destroy();
            }

            var options = {
                series: ranges,
                labels: ['90-100%', '80-89%', '70-79%', 'Below 70%'],
                chart: {
                    type: 'donut',
                    height: 330
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,
                                name: { show: true },
                                value: { show: true },
                                total: { show: true, showAlways: true, label: 'Total', color: '#373d3f' }
                            }
                        }
                    }
                },
                colors: ['#28c76f', '#00cfe8', '#ff9f43', '#ea5455'],
                dataLabels: { enabled: false },
                legend: { position: 'bottom' }
            };

            performanceChart = new ApexCharts(document.querySelector("#performanceDistributionChart"), options);
            performanceChart.render();

            let deptStats = {};
            let desigStats = {};
            let trendStats = {};

            response.assignments.forEach(assignment => {
                let score = assignment.performance ? parseFloat(assignment.performance.achievement_percentage) : null;
                if (score !== null) {
                    let deptName = 'N/A';
                    if (assignment.department_id) deptName = 'Dept ' + assignment.department_id;
                    if (assignment.box_user && assignment.box_user.department) deptName = assignment.box_user.department;
                    
                    if(!deptStats[deptName]) deptStats[deptName] = {total: 0, count: 0};
                    deptStats[deptName].total += score;
                    deptStats[deptName].count++;

                    let desigName = 'Team Target';
                    if (assignment.box_user && assignment.box_user.designation) desigName = assignment.box_user.designation;
                    
                    if(!desigStats[desigName]) desigStats[desigName] = {total: 0, count: 0};
                    desigStats[desigName].total += score;
                    desigStats[desigName].count++;

                    let month = assignment.month_year || assignment.created_at.substring(0,7);
                    if(!trendStats[month]) trendStats[month] = {total: 0, count: 0};
                    trendStats[month].total += score;
                    trendStats[month].count++;
                }
            });

            const commonOpts = {chart:{toolbar:{show:false},fontFamily:'inherit',animations:{enabled:true}},dataLabels:{enabled:false},grid:{borderColor:'#eef0f3'},tooltip:{theme:'light'}};

            if(deptChart) deptChart.destroy();
            let deptCats = Object.keys(deptStats);
            let deptData = deptCats.map(k => (deptStats[k].total / deptStats[k].count).toFixed(2));
            deptChart = new ApexCharts(document.querySelector('#departmentPerformanceChart'), {
                ...commonOpts, chart:{...commonOpts.chart,type:'bar',height:330},
                series:[{name:'Performance',data:deptData}],
                xaxis:{categories:deptCats}, yaxis:{min:0,max:100,labels:{formatter:v=>v+'%'}},
                plotOptions:{bar:{borderRadius:5,columnWidth:'48%',distributed:true}},
                dataLabels:{enabled:true,formatter:v=>v+'%',offsetY:-20,style:{fontSize:'11px'}},
                colors:['#6f42c1','#0d6efd','#198754','#fd7e14','#20c997']
            });
            deptChart.render();

            if(desigChart) desigChart.destroy();
            let desigCats = Object.keys(desigStats);
            let desigData = desigCats.map(k => (desigStats[k].total / desigStats[k].count).toFixed(2));
            desigChart = new ApexCharts(document.querySelector('#designationPerformanceChart'), {
                ...commonOpts, chart:{...commonOpts.chart,type:'bar',height:330},
                series:[{name:'Average Score',data:desigData}],
                xaxis:{categories:desigCats}, yaxis:{min:0,max:100,labels:{formatter:v=>v+'%'}},
                plotOptions:{bar:{horizontal:true,borderRadius:5,barHeight:'55%'}},
                dataLabels:{enabled:true,formatter:v=>v+'%',style:{fontSize:'11px'}}
            });
            desigChart.render();

            if(trendChart) trendChart.destroy();
            let trendKeys = Object.keys(trendStats).sort();
            let trendData = trendKeys.map(k => (trendStats[k].total / trendStats[k].count).toFixed(2));
            trendChart = new ApexCharts(document.querySelector('#performanceTrendChart'), {
                ...commonOpts, chart:{...commonOpts.chart,type:'line',height:330},
                series:[{name:'Average Performance',data:trendData}],
                xaxis:{categories:trendKeys},
                yaxis:{min:0,max:100,labels:{formatter:v=>v+'%'}},
                stroke:{curve:'smooth',width:3}, markers:{size:4}
            });
            trendChart.render();

            // Update Grid & Table
            let grid = $('#performance_grid');
            let tbody = $('#performance_tbody');
            grid.empty();
            tbody.empty();

            if (response.assignments.length === 0) {
                grid.append('<div class="col-12"><div class="card"><div class="card-body text-center text-muted p-5">No performance records found.</div></div></div>');
                tbody.append('<tr><td colspan="7" class="text-center text-muted p-5">No performance records found.</td></tr>');
                return;
            }

            // Sort assignments so top ranks appear first
            response.assignments.sort((a, b) => {
                let rankA = a.box_user_id ? response.stats.all_performers.findIndex(p => p.id == a.box_user_id) : 
                           (a.team_id ? response.stats.team_performers.findIndex(p => p.id == a.team_id) : 9999);
                let rankB = b.box_user_id ? response.stats.all_performers.findIndex(p => p.id == b.box_user_id) : 
                           (b.team_id ? response.stats.team_performers.findIndex(p => p.id == b.team_id) : 9999);
                           
                if (rankA === -1) rankA = 9999;
                if (rankB === -1) rankB = 9999;
                
                return rankA - rankB;
            });

            response.assignments.forEach(assignment => {
                let score = assignment.performance ? parseFloat(assignment.performance.achievement_percentage) : 0;
                let scoreDisplay = assignment.performance ? score.toFixed(2) + '%' : 'Not calculated';
                
                let targetDisplay = assignment.performance ? assignment.performance.target_value : '-';
                let achievedDisplay = assignment.performance ? assignment.performance.actual_value : '-';
                
                let assignedTo = 'Unknown';
                let department = 'N/A';
                let designation = 'N/A';
                let avatarUrl = 'https://ui-avatars.com/api/?name=User&background=random';
                
                let rankIndex = -1;

                if (assignment.box_user_id && assignment.box_user) {
                    assignedTo = (assignment.box_user.first_name || '') + ' ' + (assignment.box_user.last_name || '');
                    if (!assignedTo.trim() && assignment.box_user.user) assignedTo = assignment.box_user.user.name;
                    
                    department = assignment.box_user.department_relation ? assignment.box_user.department_relation.name : (assignment.box_user.department || 'N/A');
                    designation = assignment.box_user.designation_relation ? assignment.box_user.designation_relation.name : (assignment.box_user.designation || 'N/A');
                    avatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(assignedTo) + '&background=random';
                    
                    rankIndex = response.stats.all_performers.findIndex(p => p.id == assignment.box_user_id);
                } else if (assignment.team_id) {
                    assignedTo = 'Team Target';
                    designation = 'Team';
                    rankIndex = response.stats.team_performers.findIndex(p => p.id == assignment.team_id);
                } else if (assignment.department_id) {
                    assignedTo = 'Department Target';
                    department = 'Department';
                }

                let rankBadge = '';
                let tableRankBadge = `<span class="badge bg-light text-dark border">#${rankIndex !== -1 ? (rankIndex + 1) : '-'}</span>`;
                if (rankIndex !== -1) {
                    let rankNum = rankIndex + 1;
                    if (rankNum === 1) {
                        rankBadge = `<span class="badge bg-warning text-white position-absolute shadow-sm" style="top: -10px; right: -10px; font-size: 14px; padding: 6px 10px; border-radius: 50%;"><i class="ti ti-crown"></i> 1</span>`;
                        tableRankBadge = `<span class="badge bg-warning text-white shadow-sm px-2 py-1"><i class="ti ti-crown"></i> #1</span>`;
                    } else if (rankNum === 2) {
                        rankBadge = `<span class="badge bg-secondary text-white position-absolute shadow-sm" style="top: -10px; right: -10px; font-size: 14px; padding: 6px 10px; border-radius: 50%;">#2</span>`;
                        tableRankBadge = `<span class="badge bg-secondary text-white shadow-sm px-2 py-1">#2</span>`;
                    } else if (rankNum === 3) {
                        rankBadge = `<span class="badge position-absolute shadow-sm" style="top: -10px; right: -10px; font-size: 14px; padding: 6px 10px; border-radius: 50%; background-color: #cd7f32; color: white;">#3</span>`;
                        tableRankBadge = `<span class="badge shadow-sm px-2 py-1" style="background-color: #cd7f32; color: white;">#3</span>`;
                    } else {
                        rankBadge = `<span class="badge bg-light text-dark position-absolute shadow-sm border" style="top: -10px; right: -10px; font-size: 12px; padding: 4px 8px; border-radius: 50%;">#${rankNum}</span>`;
                        tableRankBadge = `<span class="badge bg-light text-dark border px-2 py-1">#${rankNum}</span>`;
                    }
                }

                let targetMonth = assignment.month_year ? assignment.month_year : '-';
                let templateName = assignment.template ? assignment.template.name : '-';
                
                let badgeClass = assignment.status === 'Completed' ? 'bg-success' : (assignment.status === 'Active' ? 'bg-primary' : 'bg-secondary');
                let scoreColor = score >= 90 ? 'text-success' : (score >= 50 ? 'text-warning' : 'text-danger');
                if (!assignment.performance) scoreColor = 'text-muted';

                let metricsHtml = '';
                let tableMetricsHtml = '';

                if (assignment.template && assignment.template.metrics && assignment.template.metrics.length > 0) {
                    tableMetricsHtml = '<div class="d-flex flex-column gap-1" style="min-width: 250px; max-height: 140px; overflow-y: auto;">';
                    
                    assignment.template.metrics.forEach(metric => {
                        let metricActual = assignment.actuals ? assignment.actuals.find(a => a.metric_id === metric.id) : null;
                        
                        let mTarget = metric.target_value ? parseFloat(metric.target_value) : 0;
                        let mAchieved = metricActual ? (parseFloat(metricActual.actual_value) || 0) : 0;
                        let mLeft = mTarget - mAchieved;
                        if (mLeft < 0) mLeft = 0;
                        
                        let mWeight = metric.weight ? parseFloat(metric.weight) : 0;
                        let mPercentage = 0;
                        if (mTarget > 0) {
                            mPercentage = (mAchieved / mTarget) * 100;
                        } else if (metric.metric_type === 'boolean') {
                            mPercentage = mAchieved > 0 ? 100 : 0;
                        }
                        let mPercentageStr = mPercentage.toFixed(0) + '%';
                        let barColor = mPercentage >= 100 ? 'bg-success' : (mPercentage >= 50 ? 'bg-primary' : 'bg-warning');
                        
                        let displayTarget = mTarget;
                        let displayAchieved = mAchieved;
                        if (metric.metric_type === 'boolean') {
                            mLeft = '-';
                            displayTarget = 'Yes';
                            displayAchieved = mAchieved > 0 ? 'Yes' : 'No';
                        }
                        
                        // Card view metric block
                        metricsHtml += `
                            <div class="mb-2 pb-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fs-12 fw-medium text-truncate" style="max-width: 130px;" title="${metric.metric_name}">${metric.metric_name}</span>
                                    <span class="fs-11 text-muted text-end">Weight: <strong>${mWeight}%</strong></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-11">
                                        <span class="text-muted">Target:</span> <strong class="text-dark">${displayTarget}</strong>
                                    </div>
                                    <div class="fs-11">
                                        <span class="text-muted">Done:</span> <strong class="text-success">${displayAchieved}</strong>
                                    </div>
                                    <div class="fs-11">
                                        <span class="text-muted">Left:</span> <strong class="text-danger">${mLeft}</strong>
                                    </div>
                                </div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-primary" style="width: ${mPercentage > 100 ? 100 : mPercentage}%"></div>
                                </div>
                                <div class="text-end mt-1"><span class="fs-10 text-primary fw-medium">${mPercentageStr} Complete</span></div>
                            </div>
                        `;

                        // Table view metric item
                        tableMetricsHtml += `
                            <div class="p-1 px-2 rounded bg-light border-0 mb-1">
                                <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 11px;">
                                    <span class="fw-semibold text-dark text-truncate" style="max-width: 140px;" title="${metric.metric_name}">${metric.metric_name}</span>
                                    <span class="text-muted"><strong class="text-success">${displayAchieved}</strong> / ${displayTarget} <span class="badge bg-white text-dark border ms-1">${mPercentageStr}</span></span>
                                </div>
                                <div class="progress" style="height: 3px;">
                                    <div class="progress-bar ${barColor}" style="width: ${Math.min(mPercentage, 100)}%"></div>
                                </div>
                            </div>
                        `;
                    });
                    tableMetricsHtml += '</div>';
                } else {
                    metricsHtml = '<div class="text-center text-muted fs-12 py-3">No metrics found.</div>';
                    tableMetricsHtml = '<span class="text-muted fs-12">No metrics found</span>';
                }

                // Append Card to Grid
                grid.append(`
                    <div class="col-xxl-3 col-xl-4 col-md-6 d-flex position-relative">
                        ${rankBadge}
                        <div class="card flex-fill">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="badge ${badgeClass} badge-sm">${assignment.status}</span>
                                    <span class="fs-12 text-muted"><i class="ti ti-calendar me-1"></i>${targetMonth}</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <span class="avatar avatar-md rounded-circle me-3"><img src="${avatarUrl}" alt="Profile"></span>
                                    <div>
                                        <h6 class="mb-1">${assignedTo}</h6>
                                        <p class="fs-12 text-muted mb-0">${designation}</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <span class="badge badge-soft-primary w-100 fs-13 py-2 text-start text-truncate" title="${templateName}">
                                        <i class="ti ti-clipboard-list me-1"></i>${templateName}
                                    </span>
                                </div>
                                
                                <!-- Metrics Breakdown -->
                                <div class="mb-3 flex-grow-1" style="max-height: 120px; overflow-y: auto;">
                                    ${metricsHtml}
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                    <span class="fs-13 text-muted">Performance</span>
                                    <h4 class="mb-0 ${scoreColor}">${scoreDisplay}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                // Append Row to Table
                tbody.append(`
                    <tr>
                        <td class="ps-3 text-center align-middle">${tableRankBadge}</td>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-md rounded-circle me-2 flex-shrink-0"><img src="${avatarUrl}" alt="Profile"></span>
                                <div>
                                    <h6 class="fs-13 mb-0 text-dark fw-bold">${assignedTo}</h6>
                                    <span class="fs-12 text-muted">${designation}</span>
                                    ${department && department !== 'N/A' ? `<span class="badge badge-soft-secondary fs-10 ms-1">${department}</span>` : ''}
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                            <span class="badge badge-soft-primary fs-12 py-1 px-2 text-truncate" style="max-width: 170px;" title="${templateName}">
                                <i class="ti ti-clipboard-list me-1"></i>${templateName}
                            </span>
                        </td>
                        <td class="align-middle">
                            <span class="fs-12 text-muted text-nowrap"><i class="ti ti-calendar me-1"></i>${targetMonth}</span>
                        </td>
                        <td class="align-middle">
                            ${tableMetricsHtml}
                        </td>
                        <td class="align-middle">
                            <span class="badge ${badgeClass} badge-sm">${assignment.status}</span>
                        </td>
                        <td class="text-end pe-3 align-middle">
                            <h5 class="mb-0 ${scoreColor} fw-bold">${scoreDisplay}</h5>
                            ${assignment.performance && assignment.performance.performance_status ? `<span class="badge badge-soft-info fs-10 mt-1">${assignment.performance.performance_status}</span>` : ''}
                        </td>
                    </tr>
                `);
            });
        });
    }
});
</script>
@endsection
