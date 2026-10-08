@extends('layouts.frontend.main')
@section('content')
@section('title', 'FAQ')
@section('description', 'Frequently Asked Questions about ZigZapGo')
<section class="breadcrumb_area">
    <img class="breadcrumb_shap" src="{{ asset('assets/frontend/main/img/breadcrumb/banner_bg.png') }}" alt="">
    <div class="container">
        <div class="breadcrumb_content text-center">
            <h1 class="f_p f_700 f_size_50 w_color l_height50 mb_20">Frequently Asked Questions</h1>
        </div>
    </div>
</section>
<section class="stratup_service_area sec_pad bg_color">
    <div class="container">
        <!-- Search Bar -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="input-group input-group-lg shadow-sm rounded">
                    <span class="input-group-text bg-white border-0"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" id="faqSearchInput" class="form-control border-0 shadow-none" placeholder="Search FAQs by question or answer...">
                </div>
            </div>
        </div>

        <div class="accordion" id="faqAccordion">
            <!-- FAQs will be loaded here via AJAX -->
        </div>
        
        <div id="faqLoader" class="text-center my-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        
        <div id="faqNoResults" class="alert alert-warning text-center mt-3" style="display: none;">
            No FAQs found matching your search.
        </div>
    </div>
</section>

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;

    function loadFaqs(searchQuery = '') {
        $('#faqAccordion').empty();
        $('#faqLoader').show();
        $('#faqNoResults').hide();

        $.ajax({
            url: "{{ route('frontend.faq.api') }}",
            type: "GET",
            data: { search: searchQuery },
            dataType: "json",
            success: function(response) {
                $('#faqLoader').hide();
                if(response && response.status && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(function(faq, index) {
                        let isFirst = (index === 0 && searchQuery === ''); // Only open first if no search
                        let isSearchMatch = (searchQuery !== ''); // Open all if it's a search match
                        
                        let isOpen = isFirst || isSearchMatch;
                        
                        let collapsedClass = isOpen ? '' : 'collapsed';
                        let expandedAttr = isOpen ? 'true' : 'false';
                        let showClass = isOpen ? 'show' : '';

                        html += `
                            <div class="accordion-item mb-3 border-0 shadow-sm rounded faq-item">
                                <h2 class="accordion-header" id="heading${faq.id}">
                                    <button class="accordion-button ${collapsedClass} fw-semibold fs-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${faq.id}" aria-expanded="${expandedAttr}" aria-controls="collapse${faq.id}">
                                        ${faq.question}
                                    </button>
                                </h2>
                                <div id="collapse${faq.id}" class="accordion-collapse collapse ${showClass}" aria-labelledby="heading${faq.id}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        ${faq.answer}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#faqAccordion').html(html);
                } else {
                    $('#faqNoResults').show();
                }
            },
            error: function(xhr, status, error) {
                $('#faqLoader').hide();
                console.error("Error loading FAQs:", error);
            }
        });
    }

    // Load initial FAQs on page load
    loadFaqs();

    // Listen to search input with a debounce of 300ms
    $('#faqSearchInput').on('input', function() {
        let query = $(this).val().trim();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadFaqs(query);
        }, 300);
    });
});
</script>
@endpush
@endsection
