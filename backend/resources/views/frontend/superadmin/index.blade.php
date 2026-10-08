
@extends('layouts.main')
@section('content')

<!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content">

				<div class="row">
					<div class="col-md-12">
						<div class="page-header">
							<div class="row align-items-center ">
								<div class="col-md-4">
									<h3 class="page-title">Dashboard</h3>
								</div>
								<div class="col-md-8 float-end ms-auto">
									<div class="d-flex title-head">
										<div class="daterange-picker d-flex align-items-center justify-content-center">
											<div class="form-sort me-2">
												<i class="ti ti-calendar"></i>
												<input type="text" class="form-control  date-range bookingrange">
											</div>
											<div class="head-icons mb-0">
												<a href="leads-dashboard.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh">
													<i class="ti ti-refresh-dot"></i>
												</a>
												<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header">
													<i class="ti ti-chevrons-up"></i>
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Welcome Wrap -->
				<div class="welcome-wrap mb-4">
					<div class=" d-flex align-items-center justify-content-between flex-wrap">
						<div class="mb-3">
							<h2 class="mb-1 text-white">Welcome Back, {{ Auth::check() ? Auth::user()->name : 'Guest' }}</h2>
							<p class="text-light" id="dash_new_companies_subtitle">Loading...</p>
						</div>
						<div class="d-flex align-items-center flex-wrap mb-1">
							<a href="company.html" class="btn btn-dark btn-md me-2 mb-2">Companies</a>
							<a href="packages.html" class="btn btn-light btn-md mb-2">All Packages</a>
						</div>
					</div>
				</div>
				<!-- /Welcome Wrap -->

				<div class="row">

					<!-- Total Companies -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<span class="avatar avatar-md rounded bg-dark mb-3">
										<i class="ti ti-building fs-16"></i>
									</span>
									<span class="badge bg-success fw-normal mb-3" id="dash_total_comp_perc">
										...
									</span>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h2 class="mb-1" id="dash_total_companies">...</h2>
										<p class="fs-13">Total Companies</p>
									</div>
									<div class="company-bar1">5,10,7,5,10,7,5</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Total Companies -->

					<!-- Active Companies -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<span class="avatar avatar-md rounded bg-dark mb-3">
										<i class="ti ti-carousel-vertical fs-16"></i>
									</span>
									<span class="badge bg-danger fw-normal mb-3" id="dash_active_comp_perc">
										...
									</span>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h2 class="mb-1" id="dash_active_companies">...</h2>
										<p class="fs-13">Active Companies</p>
									</div>
									<div class="company-bar2">5,3,7,6,3,10,5</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Active Companies -->

					<!-- Total Subscribers -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<span class="avatar avatar-md rounded bg-dark mb-3">
										<i class="ti ti-chalkboard-off fs-16"></i>
									</span>
									<span class="badge bg-success fw-normal mb-3" id="dash_total_subs_perc">
										...
									</span>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h2 class="mb-1" id="dash_total_subscribers">...</h2>
										<p class="fs-13">Total Subscribers</p>
									</div>
									<div class="company-bar3">8,10,10,8,8,10,8</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Total Subscribers -->

					<!-- Total Earnings -->
					<div class="col-xl-3 col-sm-6 d-flex">
						<div class="card flex-fill">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<span class="avatar avatar-md rounded bg-dark mb-3">
										<i class="ti ti-businessplan fs-16"></i>
									</span>
									<span class="badge bg-danger fw-normal mb-3" id="dash_total_earn_perc">
										...
									</span>
								</div>
								<div class="d-flex align-items-center justify-content-between">
									<div>
										<h2 class="mb-1" id="dash_total_earnings">...</h2>
										<p class="fs-13">Total Earnings</p>
									</div>
									<div class="company-bar4">5,10,7,5,10,7,5</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Total Earnings -->

				</div>

				<div class="row">

					<!-- Companies -->
					<div class="col-xxl-3 col-lg-6 d-flex">
						<div class="card flex-fill">
							<div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
								<h5 class="mb-2">Companies</h5>
								<div class="dropdown mb-2">
									<a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown" id="company_filter_btn">
										<i class="ti ti-calendar me-1"></i><span class="filter-text">This Week</span>
									</a>
									<ul class="dropdown-menu  dropdown-menu-end p-3" id="company_filter_menu">
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="this_month">This Month</a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="this_week">This Week</a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="today">Today</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="card-body">
								<div id="dyn-company-chart"></div>
								<p class="f-13 d-inline-flex align-items-center" id="dash_companies_month_stats"><span class="badge badge-success me-1">...</span> ...</p>
							</div>
						</div>
					</div>
					<!-- /Companies -->

					<!-- Revenue -->
					<div class="col-lg-6 d-flex">
						<div class="card flex-fill">
							<div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
								<h5 class="mb-2">Revenue</h5>
								<div class="dropdown mb-2">
									<a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown" id="revenue_filter_btn">
										<i class="ti ti-calendar me-1"></i><span class="filter-text">2025</span>
									</a>
									<ul class="dropdown-menu  dropdown-menu-end p-3" id="revenue_filter_menu">
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="2024">2024</a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="2025">2025</a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="2026">2026</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="card-body pb-0">
								<div class="d-flex align-items-center justify-content-between flex-wrap">
									<div class="mb-1">
                                        <h5 class="mb-1" id="dash_revenue_big">...</h5>
                                        <p id="dash_revenue_stats"><span class="text-success fw-bold">...</span> increased from last year</p>
									</div>
                                    <p class="fs-13 text-gray-9 d-flex align-items-center mb-1"><i class="ti ti-circle-filled me-1 fs-6 text-primary"></i>Revenue</p>
								</div>
								<div id="dyn-revenue-income"></div>
							</div>
						</div>
					</div>
					<!-- /Revenue -->

					<!-- Top Plans -->
					<div class="col-xxl-3 col-xl-12 d-flex">
						<div class="card flex-fill">
							<div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
								<h5 class="mb-2">Top Plans</h5>
								<div class="dropdown mb-2">
									<a href="javascript:void(0);" class="btn btn-white border btn-sm d-inline-flex align-items-center" data-bs-toggle="dropdown" id="plan_filter_btn">
										<i class="ti ti-calendar me-1"></i><span class="filter-text">This Month</span>
									</a>
									<ul class="dropdown-menu  dropdown-menu-end p-3" id="plan_filter_menu">
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="this_month">This Month</a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="this_week">This Week</a>
										</li>
										<li>
											<a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="today">Today</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="card-body">
								<div id="dyn-plan-overview"></div>
								<div id="top_plans_list">
									<!-- Dynamically populated via AJAX -->
								</div>
							</div>
						</div>
					</div>
					<!-- /Top Plans -->

				</div>

				<div class="row">

					<!-- Recent Transactions -->
					<div class="col-xxl-4 col-xl-12 d-flex">
						<div class="card flex-fill">
							<div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
								<h5 class="mb-2">Recent Transactions</h5>
								<a href="purchase-transaction.html" class="btn btn-light btn-md mb-2">View All</a>
							</div>
							<div class="card-body pb-2" id="recent_transactions_list">
								<!-- Dynamically populated via AJAX -->
							</div>
						</div>
					</div>
					<!-- /Recent Transactions -->

					<!-- Recently Registered -->
					<div class="col-xxl-4 col-xl-6 d-flex">
						<div class="card flex-fill">
							<div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
								<h5 class="mb-2">Recently Registered</h5>
								<a href="purchase-transaction.html" class="btn btn-light btn-md mb-2">View All</a>
							</div>
							<div class="card-body pb-2" id="recently_registered_list">
								<!-- Dynamically populated via AJAX -->
							</div>
						</div>
					</div>
					<!-- /Recent Registered -->

					<!-- Recent Plan Expired -->
					<div class="col-xxl-4 col-xl-6 d-flex">
						<div class="card flex-fill">
							<div class="card-header pb-2 d-flex align-items-center justify-content-between flex-wrap">
								<h5 class="mb-2">Recent Plan Expired</h5>
								<div class="dropdown mb-2">
                                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white border btn-sm d-inline-flex align-items-center fs-13" data-bs-toggle="dropdown">
                                        Expired
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end p-3">
                                        <ul class="nav d-block">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item d-block rounded-1">Expired</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item d-block rounded-1">Request</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
							</div>
							<div class="card-body pb-2" id="recent_expired_list">
								<!-- Dynamically populated via AJAX -->
							</div>
						</div>
					</div>
					<!-- /Recent Plan Expired -->

				</div>

			</div>
		</div>
		<!-- /Page Wrapper -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (typeof $ !== 'undefined') {
        let currentCompanyFilter = 'this_week';
        let currentRevenueYear = '2025';
        let currentPlanFilter = 'this_month';

        let companyChartInst = null;
        let revenueChartInst = null;
        let planChartInst = null;

        function loadDashboardData() {
            $.ajax({
                url: window.location.href, 
                type: 'GET',
                data: {
                    company_filter: currentCompanyFilter,
                    revenue_year: currentRevenueYear,
                    plan_filter: currentPlanFilter
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        $('#dash_total_companies').text(response.data.total_companies);
                        $('#dash_active_companies').text(response.data.active_companies);
                        $('#dash_total_subscribers').text(response.data.total_subscribers);
                        $('#dash_total_earnings').text(response.data.total_earnings);

                        let assetBase = "{{ asset('assets/img/icons') }}";

                        // Render Recent Transactions
                        let transactionsHtml = '';
                        if (response.data.recent_transactions && response.data.recent_transactions.length > 0) {
                            response.data.recent_transactions.forEach(function(item, index) {
                                let iconIndex = (index % 10) + 1;
                                let iconNum = iconIndex < 10 ? '0' + iconIndex : iconIndex;
                                let initial = item.company_name ? item.company_name.charAt(0).toUpperCase() : 'C';
                                let imgSrc = item.logo ? item.logo : `${assetBase}/company-icon-${iconNum}.svg`;
                                transactionsHtml += `
                                    <div class="d-sm-flex justify-content-between flex-wrap mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border flex-shrink-0" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background-color: #f3f4f6; text-decoration: none;">
                                                <img src="${imgSrc}" class="img-fluid w-100 h-100" style="object-fit: contain;" alt="img" onerror="this.onerror=null; this.outerHTML='<span class=\\'text-primary fw-bold fs-14\\'>${initial}</span>'">
                                            </a>
                                            <div class="ms-2 flex-fill">
                                                <h6 class="fs-medium text-truncate mb-1"><a href="javascript:void(0);">${item.company_name}</a></h6>
                                                <p class="fs-13 d-inline-flex align-items-center"><span class="text-info">${item.id_label}</span><i class="ti ti-circle-filled fs-4 text-primary mx-1"></i>${item.date}</p>
                                            </div>
                                        </div>
                                        <div class="text-sm-end mb-2">
                                            <h6 class="mb-1">${item.amount}</h6>
                                            <p class="fs-13">${item.plan_name}</p>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            transactionsHtml = '<p class="text-center text-muted">No recent transactions.</p>';
                        }
                        $('#recent_transactions_list').html(transactionsHtml);

                        // Render Recently Registered
                        let registeredHtml = '';
                        if (response.data.recently_registered && response.data.recently_registered.length > 0) {
                            response.data.recently_registered.forEach(function(item, index) {
                                let iconIndex = (index % 10) + 1;
                                let iconNum = iconIndex < 10 ? '0' + iconIndex : iconIndex;
                                let initial = item.company_name ? item.company_name.charAt(0).toUpperCase() : 'C';
                                let imgSrc = item.logo ? item.logo : `${assetBase}/company-icon-${iconNum}.svg`;
                                registeredHtml += `
                                    <div class="d-sm-flex justify-content-between flex-wrap mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border flex-shrink-0" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background-color: #f3f4f6; text-decoration: none;">
                                                <img src="${imgSrc}" class="img-fluid w-100 h-100" style="object-fit: contain;" alt="img" onerror="this.onerror=null; this.outerHTML='<span class=\\'text-primary fw-bold fs-14\\'>${initial}</span>'">
                                            </a>
                                            <div class="ms-2 flex-fill">
                                                <h6 class="fs-medium text-truncate mb-1"><a href="javascript:void(0);">${item.company_name}</a></h6>
                                                <p class="fs-13">${item.plan_name}</p>
                                            </div>
                                        </div>
                                        <div class="text-sm-end mb-2">
                                            <p class="fs-13 mb-1">${item.users_count}</p>
                                            <h6 class="fs-13 fw-normal">${item.domain}</h6>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            registeredHtml = '<p class="text-center text-muted">No recently registered companies.</p>';
                        }
                        $('#recently_registered_list').html(registeredHtml);

                        // Render Recent Plan Expired
                        let expiredHtml = '';
                        if (response.data.recent_expired && response.data.recent_expired.length > 0) {
                            response.data.recent_expired.forEach(function(item, index) {
                                let iconIndex = (index % 10) + 1;
                                let iconNum = iconIndex < 10 ? '0' + iconIndex : iconIndex;
                                let initial = item.company_name ? item.company_name.charAt(0).toUpperCase() : 'C';
                                let imgSrc = item.logo ? item.logo : `${assetBase}/company-icon-${iconNum}.svg`;
                                expiredHtml += `
                                    <div class="d-sm-flex justify-content-between flex-wrap mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border flex-shrink-0" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background-color: #f3f4f6; text-decoration: none;">
                                                <img src="${imgSrc}" class="img-fluid w-100 h-100" style="object-fit: contain;" alt="img" onerror="this.onerror=null; this.outerHTML='<span class=\\'text-primary fw-bold fs-14\\'>${initial}</span>'">
                                            </a>
                                            <div class="ms-2 flex-fill">
                                                <h6 class="fs-medium text-truncate mb-1"><a href="javascript:void(0);">${item.company_name}</a></h6>
                                                <p class="fs-13">${item.expired_date}</p>
                                            </div>
                                        </div>
                                        <div class="text-sm-end mb-2">
                                            <a href="javascript:void(0);" class="link-info text-decoration-underline d-block mb-1">Send Reminder</a>
                                            <p class="fs-13">${item.plan_name}</p>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            expiredHtml = '<p class="text-center text-muted">No recently expired plans.</p>';
                        }
                        $('#recent_expired_list').html(expiredHtml);

                        // Top metrics
                        $('#dash_total_comp_perc').text(response.data.companies_perc_str);
                        $('#dash_active_comp_perc').text('-12%'); 
                        $('#dash_total_subs_perc').text('+6%'); 
                        $('#dash_total_earn_perc').text('-16%'); 

                        // New companies subtitle
                        $('#dash_new_companies_subtitle').text(response.data.new_companies_today + ' New Companies Subscribed Today !!!');

                        // Companies month stats
                        $('#dash_companies_month_stats').html('<span class="badge badge-success me-1">' + response.data.companies_perc_str + '</span> ' + response.data.last_month_companies + ' Companies from last month');

                        // Revenue stats
                        $('#dash_revenue_big').text(response.data.total_earnings);
                        $('#dash_revenue_stats').html('<span class="text-success fw-bold">+40%</span> increased from last year'); 

                        // Top plans
                        let plansHtml = '';
                        if (response.data.top_plans && response.data.top_plans.length > 0) {
                            response.data.top_plans.forEach(function(plan, idx) {
                                let mbClass = (idx === response.data.top_plans.length - 1) ? 'mb-0' : 'mb-2';
                                plansHtml += `
                                    <div class="d-flex align-items-center justify-content-between ${mbClass}">
                                        <p class="f-13 mb-0"><i class="ti ti-circle-filled text-${plan.color} me-1"></i>${plan.name}</p>
                                        <p class="f-13 fw-medium text-gray-9">${plan.percentage}</p>
                                    </div>
                                `;
                            });
                        } else {
                            plansHtml = '<p class="text-muted fs-13">No plan data available</p>';
                        }
                        $('#top_plans_list').html(plansHtml);

                        // Update Charts
                        if (typeof ApexCharts !== 'undefined') {
                            if(companyChartInst) companyChartInst.destroy();
                            var companyChartOptions = {
                                chart: { height: 240, type: 'bar', toolbar: { show: false } },
                                colors: ['#212529'],
                                responsive: [{ breakpoint: 480, options: { legend: { position: 'bottom', offsetX: -10, offsetY: 0 } } }],
                                plotOptions: { bar: { borderRadius: 10, borderRadiusWhenStacked: 'all', horizontal: false, endingShape: 'rounded', colors: { backgroundBarColors: ['#f3f4f5'], backgroundBarOpacity: 0.5, hover: { enabled: true, borderColor: '#F26522' } } } },
                                series: [{ name: 'Company', data: response.data.charts.company.series }],
                                xaxis: { categories: response.data.charts.company.categories, labels: { style: { colors: '#6B7280', fontSize: '13px' } } },
                                yaxis: { labels: { offsetX: -15, show: false } },
                                grid: { borderColor: '#E5E7EB', strokeDashArray: 5, padding: { left: -8 } },
                                legend: { show: false }, dataLabels: { enabled: false }, fill: { opacity: 1 }
                            };
                            companyChartInst = new ApexCharts(document.querySelector("#dyn-company-chart"), companyChartOptions);
                            companyChartInst.render();

                            if(revenueChartInst) revenueChartInst.destroy();
                            var revenueChartOptions = {
                                chart: { height: 230, type: 'bar', stacked: true, toolbar: { show: false } },
                                colors: ['#E70D0D', '#F8F9FA'],
                                responsive: [{ breakpoint: 480, options: { legend: { position: 'bottom', offsetX: -10, offsetY: 0 } } }],
                                plotOptions: { bar: { borderRadius: 5, borderRadiusWhenStacked: 'all', horizontal: false, endingShape: 'rounded' } },
                                series: [{ name: 'Income', data: response.data.charts.revenue.series }],
                                xaxis: { categories: response.data.charts.revenue.categories, labels: { style: { colors: '#6B7280', fontSize: '13px' } } },
                                yaxis: { 
                                    min: 0, 
                                    labels: { 
                                        offsetX: -15, 
                                        style: { colors: '#6B7280', fontSize: '13px' }, 
                                        formatter: function (value) { 
                                            return value > 1000 ? (value/1000).toFixed(1) + "K" : value; 
                                        } 
                                    } 
                                },
                                grid: { borderColor: 'transparent', strokeDashArray: 5, padding: { left: -8 } },
                                legend: { show: false }, dataLabels: { enabled: false },
                                tooltip: { y: { formatter: function (val) { return val; } } },
                                fill: { opacity: 1 }
                            };
                            revenueChartInst = new ApexCharts(document.querySelector("#dyn-revenue-income"), revenueChartOptions);
                            revenueChartInst.render();

                            if(planChartInst) planChartInst.destroy();
                            var donutChartOptions = {
                                chart: { height: 240, type: 'donut', toolbar: { show: false } },
                                colors: ['#FFC107', '#1B84FF', '#F26522'],
                                series: response.data.charts.plans.series.length ? response.data.charts.plans.series : [0],
                                labels: response.data.charts.plans.labels.length ? response.data.charts.plans.labels : ['No Data'],
                                plotOptions: { pie: { donut: { size: '50%', labels: { show: false }, borderRadius: 30 } } },
                                stroke: { lineCap: 'round', show: true, width: 0, colors: '#fff' },
                                dataLabels: { enabled: false }, legend: { show: false },
                                responsive: [{ breakpoint: 480, options: { chart: { height: 180 }, legend: { position: 'bottom' } } }]
                            };
                            planChartInst = new ApexCharts(document.querySelector("#dyn-plan-overview"), donutChartOptions);
                            planChartInst.render();
                        }
                    }
                },
                error: function(xhr) {
                    console.error("Error loading dashboard data:", xhr);
                }
            });
        }

        // Initial Load
        loadDashboardData();

        // Filters binding
        $('#company_filter_menu a').click(function(e) {
            e.preventDefault();
            currentCompanyFilter = $(this).data('filter');
            $('#company_filter_btn .filter-text').text($(this).text());
            loadDashboardData();
        });
        
        $('#revenue_filter_menu a').click(function(e) {
            e.preventDefault();
            currentRevenueYear = $(this).data('filter');
            $('#revenue_filter_btn .filter-text').text($(this).text());
            loadDashboardData();
        });
        
        $('#plan_filter_menu a').click(function(e) {
            e.preventDefault();
            currentPlanFilter = $(this).data('filter');
            $('#plan_filter_btn .filter-text').text($(this).text());
            loadDashboardData();
        });
    }
});
</script>
@endsection
