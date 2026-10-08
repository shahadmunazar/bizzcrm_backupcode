@extends('layouts.frontend.main')
@section('content')
<section class="sec_pad">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <h2 class="f_p f_700 t_color3 mb_20 text-center">Checkout Summary</h2>
                        <p class="f_400 f_size_16 l_height26 mb_20 text-center">Review your plan details before proceeding to payment.</p>
                        
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                            <span class="f_size_16 fw-bold">Plan</span>
                            <span class="f_size_16">{{ $plan->name }} ({{ ucfirst($plan->type) }})</span>
                        </div>
                        
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                            <span class="f_size_16 fw-bold">Subtotal</span>
                            <span class="f_size_16" id="summary-subtotal">@if($plan->currency == 'INR') ₹ @elseif($plan->currency == 'USD') $ @else {{ $plan->currency }} @endif{{ number_format($plan->price, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between border-bottom pb-3 mb-4 text-success" id="discount-row" style="display: none;">
                            <span class="f_size_16 fw-bold">Discount</span>
                            <span class="f_size_16">- @if($plan->currency == 'INR') ₹ @elseif($plan->currency == 'USD') $ @else {{ $plan->currency }} @endif<span id="summary-discount">0.00</span></span>
                        </div>

                        <div class="d-flex justify-content-between border-bottom pb-3 mb-4">
                            <span class="f_size_20 fw-bold t_color3">Total</span>
                            <span class="f_size_20 fw-bold t_color3" id="summary-total">@if($plan->currency == 'INR') ₹ @elseif($plan->currency == 'USD') $ @else {{ $plan->currency }} @endif<span id="summary-total-val">{{ number_format($plan->price, 2) }}</span></span>
                        </div>

                        @if($plan->trial_period_days > 0)
                            <div class="alert alert-warning border-0 small mb-4">
                                <i class="ti ti-clock-filled me-1"></i>
                                <strong>Trial Period:</strong> Your first {{ $plan->trial_period_days }} days are free! 
                                <br>
                                <span class="text-muted">A nominal <strong>₹1</strong> will be charged now for verification. Full payment will be automatically deducted after the trial ends.</span>
                            </div>
                        @endif

                        <div class="coupon-section mb-4 p-3 bg-light rounded">
                            <h5 class="mb-3 f_size_16">Have a coupon code?</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" id="coupon_code" placeholder="Enter coupon code">
                                <button class="btn btn-primary" type="button" id="apply-coupon-btn">Apply</button>
                            </div>
                            <small id="coupon-message" class="form-text d-block mt-2"></small>
                        </div>

                        <form action="{{ isset($box_slug) ? box_route('box.checkout.process', ['box_slug' => $box_slug, 'planId' => $plan->id]) : route('checkout.process', ['planId' => $plan->id]) }}" method="POST" id="checkout-form">
                            @csrf
                            <input type="hidden" name="coupon_code" id="hidden_coupon_code" value="">
                            <button type="submit" class="price_btn btn_hover" style="width: 100%; border: none;">Proceed to Pay</button>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $('#apply-coupon-btn').on('click', function(e) {
            e.preventDefault();
            let code = $('#coupon_code').val().trim();
            let msgBox = $('#coupon-message');
            
            if (!code) {
                msgBox.text('Please enter a coupon code.').removeClass('text-success').addClass('text-danger');
                return;
            }

            msgBox.text('Applying...').removeClass('text-danger text-success').addClass('text-info');
            $(this).prop('disabled', true);

            $.ajax({
                url: "{{ isset($box_slug) ? box_route('box.checkout.apply_coupon', ['box_slug' => $box_slug, 'planId' => $plan->id]) : route('checkout.apply_coupon', ['planId' => $plan->id]) }}",
                type: 'POST',
                data: {
                    code: code
                },
                success: function(response) {
                    $('#apply-coupon-btn').prop('disabled', false);
                    if (response.success) {
                        msgBox.text(response.message).removeClass('text-danger text-info').addClass('text-success');
                        
                        $('#discount-row').show();
                        $('#summary-discount').text(response.discount.toFixed(2));
                        $('#summary-total-val').text(response.new_price.toFixed(2));
                        
                        // Set hidden input for form submission
                        $('#hidden_coupon_code').val(code);
                        
                        // Disable input
                        $('#coupon_code').prop('readonly', true);
                        $('#apply-coupon-btn').hide();
                    } else {
                        msgBox.text(response.message).removeClass('text-info text-success').addClass('text-danger');
                        $('#hidden_coupon_code').val('');
                    }
                },
                error: function(xhr) {
                    $('#apply-coupon-btn').prop('disabled', false);
                    msgBox.text('Failed to apply coupon.').removeClass('text-info text-success').addClass('text-danger');
                }
            });
        });
    });
</script>
@endpush
@endsection
