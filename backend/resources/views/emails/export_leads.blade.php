@extends('emails.layouts.master')
@section('title', 'Leads Export')
@section('content')
    <h2 style="font-size: 20px; color: #333;">Hello,</h2>
    <p style="color: #555; font-size: 16px;">
        Your requested leads export is ready and attached to this email.
    </p>
    <p style="color: #555; font-size: 16px;">
        Total Leads Exported: <strong>{{ $count }}</strong>
    </p>
    <p style="color: #999; font-size: 14px; margin-top: 30px;">
        Thank you.
    </p>
@endsection
