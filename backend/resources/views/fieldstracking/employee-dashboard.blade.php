@extends('layouts.main')
@section('title', 'My Performance Dashboard')

@section('content')
<div class="page-wrapper">
 <div class="content">
  <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
   <div class="my-auto mb-2"><h2 class="mb-1">My Performance Dashboard</h2><nav><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ box_route('fieldstracking.tracking-dashboard') }}"><i class="ti ti-smart-home"></i></a></li><li class="breadcrumb-item">Dashboard</li><li class="breadcrumb-item active">My Performance</li></ol></nav></div>
   <div class="d-flex align-items-center flex-wrap">
       <div class="me-2 mb-2"><input type="month" class="form-control" id="myMonth" value="{{ date('Y-m') }}"></div>
       <div class="me-2 mb-2"><button class="btn btn-primary" id="myFilterBtn">Filter</button></div>
       <div class="me-2 mb-2"><button class="btn btn-white border" id="exportMyPerformance"><i class="ti ti-file-export me-1"></i>Export</button></div>
   </div>
  </div>

  <div class="row">
   <div class="col-xl-4 d-flex"><div class="card flex-fill"><div class="card-body"><div class="d-flex align-items-center"><span class="avatar avatar-xl rounded-circle me-3"><img src="https://ui-avatars.com/api/?name=User&background=random" id="myAvatar" class="rounded-circle" alt="Profile"></span><div><h4 class="mb-1" id="myName">Loading...</h4><p class="mb-1 text-muted" id="myDesignation">-</p><span class="badge badge-soft-primary" id="myDepartment">-</span></div></div></div></div></div>
   <div class="col-xl-2 col-md-4 d-flex"><div class="card flex-fill"><div class="card-body"><span class="avatar rounded-circle bg-primary mb-2"><i class="ti ti-chart-line"></i></span><h6 class="fs-13">My Performance</h6><h3 class="mb-0" id="myPerformanceScore">0%</h3></div></div></div>
   <div class="col-xl-2 col-md-4 d-flex"><div class="card flex-fill"><div class="card-body"><span class="avatar rounded-circle bg-success mb-2"><i class="ti ti-target"></i></span><h6 class="fs-13">Target Achieved</h6><h3 class="mb-0" id="myTargetAchieved">0%</h3></div></div></div>
   <div class="col-xl-2 col-md-4 d-flex"><div class="card flex-fill"><div class="card-body"><span class="avatar rounded-circle bg-warning mb-2"><i class="ti ti-list-check"></i></span><h6 class="fs-13">Tasks Completed</h6><h3 class="mb-0" id="myTasksCompleted">0/0</h3></div></div></div>
   <div class="col-xl-2 col-md-4 d-flex"><div class="card flex-fill"><div class="card-body"><span class="avatar rounded-circle bg-info mb-2"><i class="ti ti-medal"></i></span><h6 class="fs-13">My Rank (Team)</h6><h3 class="mb-0" id="myTeamRank">N/A</h3></div></div></div>
  </div>

  <div class="row">
   <div class="col-xl-8 d-flex"><div class="card flex-fill"><div class="card-header d-flex justify-content-between"><div><h5 class="mb-1">My Performance Trend</h5><p class="text-muted mb-0 fs-13">Monthly performance score</p></div><span class="badge badge-soft-success">Target: 85%</span></div><div class="card-body"><div id="myPerformanceTrend" style="min-height:330px"></div></div></div></div>
   <div class="col-xl-4 d-flex"><div class="card flex-fill"><div class="card-header"><h5 class="mb-1">My Performance Score</h5><p class="text-muted mb-0 fs-13">Current review period</p></div><div class="card-body"><div id="myScoreChart" style="min-height:330px"></div></div></div></div>
  </div>

  <div class="row">
   <div class="col-xl-6 d-flex"><div class="card flex-fill"><div class="card-header"><h5 class="mb-1">My Targets</h5><p class="text-muted mb-0 fs-13">Target vs achieved</p></div><div class="card-body" id="myTargetsProgressContainer">
       <div class="text-center text-muted">Loading targets...</div>
   </div></div></div>
   
   <div class="col-xl-6 d-flex"><div class="card flex-fill"><div class="card-header"><h5 class="mb-1">Performance Summary</h5><p class="text-muted mb-0 fs-13">Your current review status</p></div><div class="card-body"><div class="p-4 border border-success bg-success-100 br-5 text-center" id="performanceSummaryBox"><span class="avatar avatar-xl bg-success text-white mx-auto mb-3" id="performanceSummaryIcon"><i class="ti ti-award fs-28"></i></span><h3 class="mb-1" id="performanceSummaryTitle">Calculating...</h3><p class="text-muted mb-3" id="performanceSummaryText">Evaluating your performance.</p><h2 class="text-success mb-1" id="performanceSummaryScore">0%</h2><span class="badge badge-success" id="performanceSummaryBadge">Evaluating</span><div id="performanceSummaryRank" class="mt-3 fw-bold text-dark d-none"></div><div id="performanceSummaryTop" class="mt-2 text-primary fw-bold d-none"><i class="ti ti-crown"></i> Top Performer (Filtered): <span></span></div><div id="performanceSummaryLastMonth" class="mt-2 text-success fw-bold d-none"><i class="ti ti-star"></i> Last Month's Top Performer: <span></span></div></div></div></div></div>
  </div>

  <div class="card"><div class="card-header d-flex justify-content-between align-items-center"><div><h5 class="mb-1">My Performance Details</h5><p class="text-muted mb-0 fs-13">Detailed performance metrics for your account</p></div><a href="employee-details.html" class="btn btn-primary btn-sm"><i class="ti ti-user me-1"></i>My Profile</a></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Metric</th><th>Target</th><th>Achieved</th><th>Score</th><th>Status</th></tr></thead><tbody id="my_performance_tbody"><tr><td colspan="5" class="text-center">Loading...</td></tr></tbody></table></div></div></div>
 </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(document).ready(function() {
    const boxSlug = '{{ request()->route("box_slug") }}';
    
    let trendChart = null;
    let scoreChart = null;

    loadMyPerformance();

    $('#myFilterBtn').click(function() {
        loadMyPerformance();
    });

    function loadMyPerformance() {
        let userId = '{{ auth()->user()->id }}'; 
        
        let url = '/' + boxSlug + '/fieldstracking/dashboard-api/performance?t=1&my_performance=1';
        
        let myMonth = $('#myMonth').val();
        
        if (myMonth) {
            let parts = myMonth.split('-');
            let year = parts[0];
            let month = parts[1];
            let startDate = myMonth + '-01';
            let lastDay = new Date(year, month, 0).getDate();
            let endDate = myMonth + '-' + (lastDay < 10 ? '0' + lastDay : lastDay);
            
            url += '&start_date=' + startDate + '&end_date=' + endDate;
        }
        
        $.get(url, function(response) {
            
            // Because of RBAC, if they are not admin, response.assignments only has THEIR assignments.
            // If they are admin, we should actually pass employee_id = their box_user_id.
            // To ensure we only process THEIR data, we can look at the first assignment's box_user, or we can fetch a specific 'my-performance' endpoint.
            
            let assignments = response.assignments;
            
            // To be perfectly accurate, we should find the current BoxUser's name.
            // We can just rely on the assignments array.
            let myName = 'Unknown User';
            let myDept = 'N/A';
            let myDesig = 'N/A';
            
            if(assignments.length > 0 && assignments[0].box_user) {
                let bu = assignments[0].box_user;
                myName = (bu.first_name || '') + ' ' + (bu.last_name || '');
                if(!myName.trim() && bu.user) myName = bu.user.name;
                
                myDept = bu.department_relation ? bu.department_relation.name : (bu.department || 'N/A');
                myDesig = bu.designation_relation ? bu.designation_relation.name : (bu.designation || 'N/A');
            }
            
            $('#myName').text(myName);
            $('#myDepartment').text(myDept);
            $('#myDesignation').text(myDesig);
            $('#myAvatar').attr('src', 'https://ui-avatars.com/api/?name=' + encodeURIComponent(myName) + '&background=random');
            
            let totalTasks = assignments.length;
            let completedTasks = 0;
            let totalScore = 0;
            let scoredTasks = 0;
            
            let trendStats = {};

            let tbody = $('#my_performance_tbody');
            tbody.empty();

            if (totalTasks === 0) {
                tbody.append('<tr><td colspan="5" class="text-center">No assignments found.</td></tr>');
            } else {
                assignments.forEach(assignment => {
                    if(assignment.status === 'Completed') completedTasks++;
                    
                    let score = assignment.performance ? parseFloat(assignment.performance.achievement_percentage) : null;
                    if (score !== null) {
                        totalScore += score;
                        scoredTasks++;
                        
                        let month = assignment.month_year || assignment.created_at.substring(0,7);
                        if(!trendStats[month]) trendStats[month] = {total: 0, count: 0};
                        trendStats[month].total += score;
                        trendStats[month].count++;
                    }
                });

                let avgScore = scoredTasks > 0 ? (totalScore / scoredTasks).toFixed(2) : 0;
                let taskCompletionRate = totalTasks > 0 ? ((completedTasks / totalTasks) * 100).toFixed(0) : 0;
                
                let taskBadge = taskCompletionRate >= 90 ? 'success' : (taskCompletionRate >= 50 ? 'warning' : 'danger');
                let taskText = taskCompletionRate >= 90 ? 'Excellent' : (taskCompletionRate >= 50 ? 'On Track' : 'Needs Improvement');

                let perfBadge = avgScore >= 90 ? 'success' : (avgScore >= 50 ? 'warning' : 'danger');
                let perfText = avgScore >= 90 ? 'Excellent' : (avgScore >= 50 ? 'On Track' : 'Needs Improvement');
                
                assignments.forEach(assignment => {
                    if (assignment.template && assignment.template.metrics) {
                        assignment.template.metrics.forEach(metric => {
                            let targetValue = parseFloat(metric.target_value) || 0;
                            let achievedValue = 0;
                            
                            if (assignment.actuals) {
                                let actual = assignment.actuals.find(a => a.metric_id === metric.id);
                                if (actual) {
                                    achievedValue = parseFloat(actual.actual_value) || 0;
                                }
                            }
                            
                            let metricScore = targetValue > 0 ? ((achievedValue / targetValue) * 100).toFixed(0) : (achievedValue > 0 ? 100 : 0);
                            
                            let mBadge = metricScore >= 90 ? 'success' : (metricScore >= 50 ? 'warning' : 'danger');
                            let mText = metricScore >= 90 ? 'Excellent' : (metricScore >= 50 ? 'On Track' : 'Needs Improvement');
                            
                            tbody.append(`
                                <tr>
                                    <td>${metric.metric_name}</td>
                                    <td>${targetValue}</td>
                                    <td>${achievedValue}</td>
                                    <td><span class="badge badge-soft-${mBadge}">${metricScore}%</span></td>
                                    <td><span class="badge badge-${mBadge}">${mText}</span></td>
                                </tr>
                            `);
                        });
                    }
                });

                tbody.append(`
                    <tr class="fw-bold bg-light">
                        <td>Overall Performance</td>
                        <td>-</td>
                        <td>-</td>
                        <td><span class="badge badge-soft-${perfBadge}">${avgScore}%</span></td>
                        <td><span class="badge badge-${perfBadge}">${perfText}</span></td>
                    </tr>
                `);
            }

            let avgScore = scoredTasks > 0 ? (totalScore / scoredTasks).toFixed(2) : 0;
            let taskCompletionRate = totalTasks > 0 ? ((completedTasks / totalTasks) * 100).toFixed(0) : 0;
            
            $('#myPerformanceScore').text(avgScore + '%');
            $('#myTargetAchieved').text(avgScore + '%');
            $('#myTasksCompleted').text(`${completedTasks}/${totalTasks}`);
            
            if (response.stats && response.stats.my_rank) {
                $('#myTeamRank').text(`${response.stats.my_rank} / ${response.stats.team_size}`);
            } else {
                $('#myTeamRank').text('N/A');
            }
            
            let targetsContainer = $('#myTargetsProgressContainer');
            targetsContainer.empty();

            if (assignments.length === 0) {
                targetsContainer.append('<div class="text-center text-muted">No targets assigned.</div>');
            } else {
                let colors = ['success', 'primary', 'info', 'warning', 'danger', 'secondary'];
                let colorIndex = 0;
                
                assignments.forEach(assignment => {
                    if (assignment.template && assignment.template.metrics) {
                        assignment.template.metrics.forEach(metric => {
                            let targetValue = parseFloat(metric.target_value) || 0;
                            let achievedValue = 0;
                            
                            if (assignment.actuals) {
                                let actual = assignment.actuals.find(a => a.metric_id === metric.id);
                                if (actual) {
                                    achievedValue = parseFloat(actual.actual_value) || 0;
                                }
                            }
                            
                            let percentage = 0;
                            if (targetValue > 0) {
                                percentage = (achievedValue / targetValue) * 100;
                            } else if (metric.metric_type === 'boolean') {
                                percentage = achievedValue > 0 ? 100 : 0;
                            }
                            
                            let displayPercentage = percentage.toFixed(0);
                            let widthPercentage = percentage > 100 ? 100 : percentage; // Cap width at 100% for UI
                            let badgeClass = colors[colorIndex % colors.length];
                            colorIndex++;

                            targetsContainer.append(`
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>${metric.metric_name || 'Metric'} <small class="text-muted">(${assignment.template.name})</small></span>
                                        <strong>${displayPercentage}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-${badgeClass}" style="width:${widthPercentage}%"></div>
                                    </div>
                                </div>
                            `);
                        });
                    }
                });
                
                if(targetsContainer.children().length === 0) {
                     targetsContainer.append('<div class="text-center text-muted">No metrics found for assigned targets.</div>');
                }
            }

            // Summary Box colors
            let summaryBox = $('#performanceSummaryBox');
            let summaryIcon = $('#performanceSummaryIcon');
            let summaryTitle = $('#performanceSummaryTitle');
            let summaryText = $('#performanceSummaryText');
            let summaryScore = $('#performanceSummaryScore');
            let summaryBadge = $('#performanceSummaryBadge');

            summaryBox.removeClass('border-success border-warning border-danger bg-success-100 bg-warning-100 bg-danger-100');
            summaryIcon.removeClass('bg-success bg-warning bg-danger');
            summaryScore.removeClass('text-success text-warning text-danger');
            summaryBadge.removeClass('badge-success badge-warning badge-danger');

            if(avgScore >= 80) {
                summaryBox.addClass('border-success bg-success-100');
                summaryIcon.addClass('bg-success');
                summaryScore.addClass('text-success');
                summaryBadge.addClass('badge-success');
                
                summaryTitle.text('Excellent Performance');
                summaryText.text('You are performing above the expected target.');
                summaryBadge.text('Above Target');
            } else if(avgScore >= 50) {
                summaryBox.addClass('border-warning bg-warning-100');
                summaryIcon.addClass('bg-warning');
                summaryScore.addClass('text-warning');
                summaryBadge.addClass('badge-warning');
                
                summaryTitle.text('Good Performance');
                summaryText.text('You are on track but have room for improvement.');
                summaryBadge.text('On Track');
            } else {
                summaryBox.addClass('border-danger bg-danger-100');
                summaryIcon.addClass('bg-danger');
                summaryScore.addClass('text-danger');
                summaryBadge.addClass('badge-danger');
                
                summaryTitle.text('Needs Improvement');
                summaryText.text('Your performance is below the expected target.');
                summaryBadge.text('Below Target');
            }
            summaryScore.text(avgScore + '%');
            
            if (response.stats && response.stats.my_rank) {
                $('#performanceSummaryRank').removeClass('d-none').html(`<i class="ti ti-medal"></i> Your Team Rank: <strong>${response.stats.my_rank} / ${response.stats.team_size}</strong>`);
            } else {
                $('#performanceSummaryRank').addClass('d-none');
            }
            
            if (response.stats && response.stats.top_team_performer) {
                $('#performanceSummaryTop').removeClass('d-none').find('span').text(response.stats.top_team_performer);
            } else {
                $('#performanceSummaryTop').addClass('d-none');
            }

            if (response.stats && response.stats.last_month_performer) {
                $('#performanceSummaryLastMonth').removeClass('d-none').find('span').text(response.stats.last_month_performer);
            } else {
                $('#performanceSummaryLastMonth').addClass('d-none');
            }

            // Charts
            let trendKeys = Object.keys(trendStats).sort();
            let trendData = trendKeys.map(k => (trendStats[k].total / trendStats[k].count).toFixed(2));
            
            if(trendChart) trendChart.destroy();
            trendChart = new ApexCharts(document.querySelector('#myPerformanceTrend'),{
                chart:{type:'line',height:330,toolbar:{show:false}},
                series:[{name:'My Performance',data:trendData}],
                xaxis:{categories:trendKeys.length ? trendKeys : ['No Data']},
                yaxis:{min:0,max:100,labels:{formatter:v=>v+'%'}},
                stroke:{curve:'smooth',width:3},markers:{size:4},dataLabels:{enabled:false}
            });
            trendChart.render();

            if(scoreChart) scoreChart.destroy();
            scoreChart = new ApexCharts(document.querySelector('#myScoreChart'),{
                chart:{type:'radialBar',height:330},
                series:[avgScore],
                labels:['Performance'],
                colors: [avgScore >= 80 ? '#28c76f' : (avgScore >= 50 ? '#ff9f43' : '#ea5455')],
                plotOptions:{
                    radialBar:{
                        hollow:{size:'62%'},
                        dataLabels:{name:{show:true},value:{fontSize:'28px',fontWeight:600,formatter:v=>v+'%'}}
                    }
                }
            });
            scoreChart.render();
        });
    }

    document.getElementById('exportMyPerformance').addEventListener('click',function(){
        const rows=[['Target Name','Target Month','Target','Achieved','Score','Status']];
        $('#my_performance_tbody tr').each(function() {
            let cols = $(this).find('td');
            if(cols.length > 1) {
                let rowData = [];
                cols.each(function() { rowData.push($(this).text().trim()); });
                rows.push(rowData);
            }
        });
        const csv=rows.map(r=>r.map(v=>'"'+v.replace(/"/g,'""')+'"').join(',')).join('\n'); 
        const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'}); 
        const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='my-performance-report.csv'; a.click(); URL.revokeObjectURL(a.href);
    });
});
</script>
@endsection
