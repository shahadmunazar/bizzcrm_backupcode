@extends('layouts.main')
@section('title', 'Invoice Templates Gallery')
@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-8">
                    <h4 class="page-title">Invoice Templates Gallery</h4>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            @foreach($templates as $template)
            <div class="col-md-4 d-flex">
                <div class="card flex-fill {{ $activeTemplate == $template['id'] ? 'border-primary' : '' }}">
                    <div class="card-body text-center d-flex flex-column">
                        <div class="mb-4 mt-3">
                            <i class="ti ti-file-invoice text-primary" style="font-size: 64px;"></i>
                        </div>
                        <h5 class="card-title mb-2">{{ $template['name'] }}</h5>
                        <p class="card-text text-muted mb-4 flex-grow-1">{{ $template['description'] }}</p>
                        
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('accounts.invoice_templates.preview', ['box_slug' => request()->segment(1), 'style' => $template['id']]) }}" target="_blank" class="btn btn-outline-warning d-flex align-items-center">
                                <i class="ti ti-eye me-1"></i> Preview
                            </a>
                            <form action="{{ route('accounts.invoice_templates.set_active', ['box_slug' => request()->segment(1)]) }}" method="POST" class="set-active-form m-0" data-template-id="{{ $template['id'] }}">
                                
                                @csrf
                                <input type="hidden" name="template_id" value="{{ $template['id'] }}">
                                <button type="button" class="btn btn-success active-btn d-flex align-items-center {{ $activeTemplate == $template['id'] ? '' : 'd-none' }}" disabled>
                                    <i class="ti ti-check me-1"></i> Active Template
                                </button>
                                <button type="submit" class="btn btn-outline-primary set-active-btn d-flex align-items-center {{ $activeTemplate == $template['id'] ? 'd-none' : '' }}">
                                    Set as Active
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>
<!-- /Page Wrapper -->

@section('scripts')
<script>
    // Unbind first to prevent multiple bindings if loaded multiple times via AJAX
    $(document).off('submit', '.set-active-form');
    
    $(document).on('submit', '.set-active-form', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
        var templateId = form.data('template-id');
        var btn = form.find('.set-active-btn');
        
        // Optional: change text to saving
        var originalText = btn.text();
        btn.text('Saving...');
        
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function(response) {
                if(response.success) {
                    // Reset all templates to non-active visually
                    $('.active-btn').addClass('d-none');
                    $('.set-active-btn').removeClass('d-none').text('Set as Active');
                    $('.card.border-primary').removeClass('border-primary');
                    
                    // Set the clicked one as active
                    form.find('.set-active-btn').addClass('d-none');
                    form.find('.active-btn').removeClass('d-none');
                    form.closest('.card').addClass('border-primary');
                    
                    // Optional: Show toast or alert
                    // toastr.success(response.message);
                }
            },
            error: function() {
                btn.text(originalText);
                alert('An error occurred. Please try again.');
            }
        });
    });
        
        </script>
@endsection
