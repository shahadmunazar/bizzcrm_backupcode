@extends('layouts.main')
@section('title', 'My Subscription')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-8">
                    <h4 class="page-title">My Subscription</h4>
                    <p class="text-muted mb-0">Manage your plan and billing details.</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- /Page Header -->
        
        <style>
            .bg-primary-soft { background-color: rgba(13, 110, 253, 0.1) !important; }
            .bg-soft-info { background-color: rgba(13, 202, 240, 0.1) !important; }
            .progress-bar { transition: width 1s ease-in-out; }
            .usage-container { border: 1px solid rgba(0,0,0,0.05); }
            .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
            .smaller { font-size: 0.75rem; }
        </style>

        <!-- Payment Tabs -->
        <ul class="nav nav-tabs border-bottom border-light mb-4 payment-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#account-usage" role="tab">Account usage</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#plan-usage" role="tab">Plan Usage</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#upgrade-plan" role="tab">Upgrade Plan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#payment-methods" role="tab">Payment methods</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#billing-information" role="tab">Billing information</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#invoices" role="tab">Invoices</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#credit-notes" role="tab">Credit notes and refunds</a>
            </li>
        </ul>


        <div id="subscription-tabs-container">
            <div class="text-center py-5 mt-5">
                <i class="ti ti-loader fa-spin fs-1 text-primary mb-3"></i>
                <h5 class="text-muted">Loading your subscription details...</h5>
            </div>
        </div>
    </div>
</div>
<style>
    .payment-tabs .nav-link {
        color: #6b7280;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.5rem 1rem 1rem 1rem;
        font-weight: 500;
        background: transparent;
        border-radius: 0;
    }
    .payment-tabs .nav-link.active {
        color: #5e2ced;
        border-bottom: 2px solid #5e2ced;
        background: transparent;
    }
    .payment-tabs .nav-link:hover:not(.active) {
        border-bottom: 2px solid #d1d5db;
        color: #374151;
    }
    .active_plan_info {
        border-left: 5px solid #5e2ced;
    }
    .price_item_card {
        padding: 40px 30px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        text-align: center;
        position: relative;
        margin-bottom: 30px;
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    .price_item_card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(94, 44, 237, 0.1);
    }
    .price_item_card.active {
        border-color: #5e2ced;
        background: rgba(94, 44, 237, 0.02);
    }
    .active-tag {
        position: absolute;
        top: 0;
        right: 30px;
        background: #5e2ced;
        color: #fff;
        padding: 5px 15px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        border-radius: 0 0 10px 10px;
    }
    .pricing-pills {
        padding: 5px;
    }
    .pricing-pills .nav-link {
        color: #6c757d;
        font-weight: 600;
        transition: all 0.2s;
    }
    .pricing-pills .nav-link.active {
        background-color: #5e2ced !important;
        color: white;
        box-shadow: 0 4px 10px rgba(94, 44, 237, 0.3);
    }
    .comparison-table th, .comparison-table td {
        vertical-align: middle;
        padding: 18px 15px;
    }
    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
    }
    .bg-soft-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .bg-soft-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.1);
    }
    .feature-list li {
        color: #555;
        font-size: 14px;
    }
    .italic {
        font-style: italic;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    function loadSubscriptionDetails() {
        $.ajax({
            url: "{{ route('subscription.razorpay_details', ['box_slug' => request('box_slug')]) }}",
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.html) {
                    $('#subscription-tabs-container').html(response.html);
                } else {
                    $('#subscription-tabs-container').html('<div class="text-center py-5 text-danger">Failed to load subscription details.</div>');
                }
            },
            error: function() {
                $('#subscription-tabs-container').html('<div class="text-center py-5 text-danger">An error occurred while loading.</div>');
            }
        });
    }

    // Initial load
    loadSubscriptionDetails();

    // Handle AJAX Cancellation
    $(document).on('submit', '#cancelSubscriptionForm', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#confirmCancelBtn');
        const spinner = submitBtn.find('.spinner-border');
        
        // UI Feedback
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Close Modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('cancelModal'));
                    if (modal) modal.hide();
                    
                    // Show success message (using alert for now, can be replaced with toastr)
                    alert(response.message);
                    
                    // Refresh the tabs content
                    loadSubscriptionDetails();
                } else {
                    alert(response.message || 'Cancellation failed.');
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred. Please try again.';
                alert(message);
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });
});
</script>
@endpush
@endsection
