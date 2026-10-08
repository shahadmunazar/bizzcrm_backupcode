@extends('layouts.frontend.main')
@section('content')
@section('title', 'Delete Account')
@section('description', 'Information on how to request the deletion of your account and personal data.')

<section class="breadcrumb_area">
    <img class="breadcrumb_shap" src="{{ asset('assets/frontend/main/img/breadcrumb/banner_bg.png') }}" alt="">
    <div class="container">
        <div class="breadcrumb_content text-center">
            <h1 class="f_p f_700 f_size_50 w_color l_height50 mb_20">Delete Account</h1>
        </div>
    </div>
</section>
<section class="agency_service_area bg_color">
    <div class="container">
        <p class="update-date" style="font-style: italic; color: #666; margin-bottom: 30px;">Last updated: {{ date('m/d/Y') }}</p>

        <h2>01. Identification</h2>
        <p>{{ config('app.name', 'Bizzdigital') }} is committed to giving you control over your personal data. This page outlines the process for requesting the deletion of your account and associated data.</p>

        <h2>02. How the Account & Data Deletion Process Works</h2>
        <p>You can request the deletion of your account and all associated personal data directly from your dashboard. Our deletion process involves the following secure steps:</p>
        
        <h4>Step 1: Submit Request</h4>
        <ul style="margin-left: 20px; margin-bottom: 20px;">
            <li>Log into your {{ config('app.name', 'Bizzdigital') }} account on our website or mobile app.</li>
            <li>Navigate to the "Delete Account" section in your dashboard.</li>
            <li>Fill out the deletion request form with your reason and submit it.</li>
        </ul>

        <h4>Step 2: Email Verification (OTP)</h4>
        <ul style="margin-left: 20px; margin-bottom: 20px;">
            <li>To protect your account from unauthorized deletion, we will send a 6-digit One-Time Password (OTP) to your registered email address.</li>
            <li>Enter this OTP on the verification page to confirm your identity.</li>
        </ul>

        <h4>Step 3: Administrator Review & Processing</h4>
        <ul style="margin-left: 20px; margin-bottom: 20px;">
            <li>Once your email is verified, your request is marked for review by our administrators.</li>
            <li>Our team will securely process the deletion and permanently remove your data in accordance with our retention policy.</li>
            <li>You can track the live status of your deletion request (e.g., Pending Verification, Pending Review, Approved) on the same Delete Account page.</li>
        </ul>

        <h2>03. Data Retention Policy: What is Deleted vs. Kept</h2>
        <p>When your deletion request is processed, we handle your data in accordance with the principle of data minimization and legal compliance:</p>

        <h4>Data that is Permanently Deleted</h4>
        <p>The following personal data will be irreversibly deleted or anonymized within 30 days of your request:</p>
        <ul style="margin-left: 20px; margin-bottom: 20px;">
            <li>Account and authentication credentials (e-mail, password hashes).</li>
            <li>Profile information (name, profile picture, gender, etc.).</li>
            <li>Personal preferences and app activity.</li>
        </ul>

        <h4>Data that must be Retained (Legal Obligation)</h4>
        <p>Under applicable law, certain information cannot be immediately deleted and must be kept for specific retention periods:</p>
        <ul style="margin-left: 20px; margin-bottom: 20px;">
            <li>Billing data, transaction history, and invoices are retained to comply with tax and accounting obligations.</li>
            <li>Customer support communications and logs are retained for legal defense purposes in case of disputes.</li>
            <li>We securely store and isolate any retained data so it is never used for marketing or active profiling purposes.</li>
        </ul>
    </div>
</section>

@endsection
