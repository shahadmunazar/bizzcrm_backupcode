<?php

namespace App\Http\Controllers\FieldTracking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function paymentAdmin() { return view('fieldstracking.payment-admin'); }
    public function paymentGateways() { return view('fieldstracking.payment-gateways'); }
    public function payment() { return view('fieldstracking.payment'); }
    public function payments() { return view('fieldstracking.payments'); }
}
