@extends('layouts.main')
@section('title', 'User Task Progress')
@section('content')

<div class="page-wrapper">
<div class="content">
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h3 class="page-title">User Task Progress</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard', ['box_slug' => request()->route('box_slug')]) }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Task Progress</li>
            </ul>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            <div class="d-flex justify-content-sm-end align-items-center">
                <div class="input-icon position-relative me-2" style="width: 250px;">
                    <span class="input-icon-addon">
                        <i class="ti ti-calendar text-gray-9"></i>
                    </span>
                    <input type="text" class="form-control" id="pageDateFilter" placeholder="All Time">
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="userProgressContainer">
        <!-- User Cards will be injected here by JS -->
        <div class="col-12 text-center" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Date Range Picker
        if ($("#pageDateFilter").length > 0) {
            $("#pageDateFilter").daterangepicker({
                autoUpdateInput: false,
                locale: { cancelLabel: "Clear" }
            });

            $("#pageDateFilter").on("apply.daterangepicker", function(ev, picker) {
                $(this).val(picker.startDate.format("YYYY-MM-DD") + " - " + picker.endDate.format("YYYY-MM-DD"));
                fetchUserProgress();
            });

            $("#pageDateFilter").on("cancel.daterangepicker", function(ev, picker) {
                $(this).val("");
                fetchUserProgress();
            });
        }

        // Fetch Data
        function fetchUserProgress() {
            let startDate = "";
            let endDate = "";
            let dateVal = $("#pageDateFilter").val();
            if (dateVal && $("#pageDateFilter").data("daterangepicker")) {
                startDate = $("#pageDateFilter").data("daterangepicker").startDate.format("YYYY-MM-DD");
                endDate = $("#pageDateFilter").data("daterangepicker").endDate.format("YYYY-MM-DD");
            }

            let requestData = {};
            if (startDate && endDate) {
                requestData.start_date = startDate;
                requestData.end_date = endDate;
            }

            $("#userProgressContainer .user-card-item").remove();
            $("#loadingSpinner").show();

            $.ajax({
                url: "{{ route('users.task.progress.data', ['box_slug' => request()->route('box_slug')]) }}",
                method: "GET",
                data: requestData,
                success: function(res) {
                    $("#loadingSpinner").hide();
                    if (res.success && res.data) {
                        if(res.data.length === 0) {
                            $("#userProgressContainer").append('<div class="col-12 text-center user-card-item mt-5"><p class="text-muted">No users found.</p></div>');
                            return;
                        }

                        res.data.forEach(function(user, index) {
                            // Top Performer Logic (First user in the sorted array)
                            let isTopPerformer = (index === 0 && user.closed > 0);
                            let borderClass = isTopPerformer ? "border-warning border-2 shadow-sm" : "";
                            let crownBadge = isTopPerformer ? `<span class="badge bg-warning text-white ms-2" title="Top Performer" style="background-color: #ff9f43 !important;"><i class="fa-solid fa-crown me-1"></i> Top Performer</span>` : "";

                            let html = `
                                <div class="col-md-6 col-lg-4 user-card-item d-flex">
                                    <div class="card flex-fill ${borderClass}">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <a href="#" class="me-3" style="width: 50px; height: 50px; border-radius: 6px; overflow: hidden; display: block; flex-shrink: 0;">
                                                    <img src="${user.profile_image}" alt="User Image" style="width: 100%; height: 100%; object-fit: cover;">
                                                </a>
                                                <div class="overflow-hidden">
                                                    <h6 class="fs-14 fw-medium text-truncate mb-1"><a href="#">${user.name}</a> ${crownBadge}</h6>
                                                    <p class="text-muted fs-12 mb-0 text-truncate">${user.email || 'N/A'}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fs-12 fw-medium">Open Tasks (${user.open})</span>
                                                <span class="fs-12 text-muted">${user.open_percent}%</span>
                                            </div>
                                            <div class="progress progress-sm mb-3">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: ${user.open_percent}%" aria-valuenow="${user.open_percent}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fs-12 fw-medium">Closed Tasks (${user.closed})</span>
                                                <span class="fs-12 text-muted">${user.closed_percent}%</span>
                                            </div>
                                            <div class="progress progress-sm mb-3">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: ${user.closed_percent}%" aria-valuenow="${user.closed_percent}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-2 mt-3 pt-2 border-top">
                                                <h6 class="mb-0 fs-14 fw-bold text-dark">Total Tasks</h6>
                                                <h6 class="mb-0 fs-14 fw-bold text-danger">${user.total}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $("#userProgressContainer").append(html);
                        });
                    }
                }
            });
        }

        fetchUserProgress();
    });
</script>
@endpush
