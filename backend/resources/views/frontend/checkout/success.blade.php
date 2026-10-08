@extends('layouts.frontend.main')
@section('content')
<section class="sec_pad">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="payment_success_content">
                    <img src="{{ asset('assets/frontend/main/img/success.png') }}" alt="" class="mb-4" style="max-width: 150px;">
                    <h2 class="f_p f_700 t_color3 mb_20">Subscription Successful!</h2>
                    <p class="f_400 f_size_16 l_height26 mb_40">Welcome to ZipZapGo! Your subscription has been activated successfully. You now have access to all the features included in your plan.</p>
                    <a href="{{ box_route('subscription.index') }}" class="price_btn btn_hover">View My Subscription</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
