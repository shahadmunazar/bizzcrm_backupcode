@extends('layouts.vendor')

@section('content')
<style>

        
        
        
        /* Layout */
        
        
        
        /* Card Styling */
        .stat-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Progress Bar */
        .progress { height: 8px; border-radius: 10px; }
        .target-percentage { font-size: 2rem; font-weight: 800; color: var(--indigo-primary); }

        @media (max-width: 768px) {
            
            
        }
    
/* Clean up variables that might conflict or are unnecessary if layout.main handles them */
.stat-card { border: none; border-radius: 12px; transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-5px); }
</style>

<div class="page-wrapper">
    <div class="content">
        
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h3 class="fw-bold m-0">Performance Overview</h3>
                <p class="text-muted small m-0">Welcome back, Acme Corp. Here's your monthly summary.</p>
            </div>
            <button class="btn btn-white border shadow-sm rounded-3">
                <i class="fa-regular fa-calendar me-2"></i> June 2026
            </button>
        </header>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card stat-card shadow-sm h-100 p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h6 class="text-uppercase text-muted fw-bold small" id="targetLabel1">Monthly Target Achievement</h6>
                            <p class="target-percentage m-0" id="targetPercentage">0.0%</p>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle" id="targetDifference">0.0% vs last month</span>
                    </div>
                    
                    <div class="mb-2 d-flex justify-content-between small">
                        <span>Current: <strong id="targetCurrentVal">$0.00</strong></span>
                        <span class="text-muted" id="targetMaxVal">Target: $0.00</span>
                    </div>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-primary" id="targetProgressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <p class="text-muted small mb-0">You are <strong id="targetRemaining">$0.00</strong> away from your monthly milestone. Keep it up!</p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card stat-card shadow-sm h-100 p-4 bg-primary text-white">
                    <h6 class="text-uppercase fw-bold small opacity-75" id="targetLabel2">Monthly Target</h6>
                    <h2 class="fw-bold mb-3" id="targetDisplayVal">$0.00</h2>
                    <p class="small opacity-75 mb-0" id="targetSetDate">Dynamic Target</p>
                    <hr class="opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-info"></i>
                        <span class="small">Target is based on your YTD average.</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card stat-card shadow-sm p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-info-subtle text-info">
                            <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1">Revenue: Last Month</h6>
                            <h4 class="fw-bold m-0"><span id="lastMonthCommission">Loading...</span></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card stat-card shadow-sm p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success-subtle text-success">
                            <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small fw-bold mb-1" id="revenueCurrentLabel">Revenue: Current Month</h6>
                            <h4 class="fw-bold m-0"><span id="currentMonthCommission">Loading...</span></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="card stat-card shadow-sm p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold m-0">Revenue Analytics</h5>
                            <p class="text-muted small m-0">Comparing performance timeline progression trends</p>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5 small">Current Month</span>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1.5 small">Last Month</span>
                        </div>
                    </div>
                    <div style="position: relative; height:320px; width:100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
</div>
@endsection


@section('scripts')
<script>
    $(document).ready(function() {
        $.ajax({
            url: '{{ route('vendor.api.dashboard') }}',
            method: 'GET',
            success: function(data) {
                let current = parseFloat(data.currentCommission || 0);
                let last = parseFloat(data.lastMonthCommission || 0);
                let target = parseFloat(data.monthlyTarget || 0);
                let type = data.targetType || 'monthly';
                
                if (type === 'custom') {
                    $('#targetLabel1').text('Target Period Achievement');
                    $('#targetLabel2').text('Period Target');
                    $('#revenueCurrentLabel').text('Revenue: Target Period');
                    
                    let startDate = data.targetStartDate ? new Date(data.targetStartDate).toLocaleDateString() : 'Start';
                    let endDate = data.targetEndDate ? new Date(data.targetEndDate).toLocaleDateString() : 'End';
                    $('#targetSetDate').text(startDate + ' to ' + endDate);
                } else {
                    $('#targetLabel1').text('Monthly Target Achievement');
                    $('#targetLabel2').text('Monthly Target');
                    $('#revenueCurrentLabel').text('Revenue: Current Month');
                    $('#targetSetDate').text('Standard Monthly Target');
                }
                
                $('#currentMonthCommission').text('₹' + current.toFixed(2));
                $('#lastMonthCommission').text('₹' + last.toFixed(2));
                
                let pct = target > 0 ? (current / target) * 100 : 0;
                if(pct > 100) pct = 100;
                
                $('#targetPercentage').text(pct.toFixed(1) + '%');
                $('#targetProgressBar').css('width', pct + '%');
                
                $('#targetCurrentVal').text('₹' + current.toFixed(2));
                $('#targetMaxVal').text('Target: ₹' + target.toFixed(2));
                $('#targetDisplayVal').text('₹' + target.toFixed(2));
                
                let remaining = target - current;
                if(remaining < 0) remaining = 0;
                $('#targetRemaining').text('₹' + remaining.toFixed(2));
                
                let diff = 0;
                if(last > 0) {
                    diff = ((current - last) / last) * 100;
                } else if(current > 0) {
                    diff = 100;
                }
                
                if(diff >= 0) {
                    $('#targetDifference').removeClass('bg-danger-subtle text-danger border-danger-subtle').addClass('bg-success-subtle text-success border-success-subtle').text('+' + diff.toFixed(1) + '% vs last month');
                } else {
                    $('#targetDifference').removeClass('bg-success-subtle text-success border-success-subtle').addClass('bg-danger-subtle text-danger border-danger-subtle').text(diff.toFixed(1) + '% vs last month');
                }
            },
            error: function() {
                $('#currentMonthCommission').text('Error');
                $('#lastMonthCommission').text('Error');
            }
        });
    });
</script>
@endsection

