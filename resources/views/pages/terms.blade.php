@extends('layouts.app')

@section('title', 'Terms of Service - Jaybtak Pro')

@section('content')
    @include('partials.navbar')

    <section class="py-5 bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <h1 class="mb-4">Terms of Service</h1>
                            <p class="text-muted mb-5">Last updated: {{ date('F d, Y') }}</p>

                            <div class="mb-5">
                                <h3 class="mb-3">1. Introduction</h3>
                                <p class="text-muted">
                                    Welcome to Jaybtak Pro. These Terms of Service ("Terms") govern your use of our money transfer services. 
                                    By accessing or using our services, you agree to be bound by these Terms.
                                </p>
                            </div>

                            <div class="mb-5">
                                <h3 class="mb-3">2. Eligibility</h3>
                                <p class="text-muted mb-3">To use our services, you must:</p>
                                <ul class="text-muted">
                                    <li>Be at least 18 years of age</li>
                                    <li>Have the legal capacity to enter into a binding agreement</li>
                                    <li>Provide accurate and complete registration information</li>
                                    <li>Comply with all applicable laws and regulations</li>
                                    <li>Not be located in a restricted jurisdiction</li>
                                </ul>
                            </div>

                            <div class="mb-5">
                                <h3 class="mb-3">3. Account Registration</h3>
                                <p class="text-muted mb-3">
                                    When you create an account with Jaybtak Pro:
                                </p>
                                <ul class="text-muted">
                                    <li>You must provide accurate, current, and complete information</li>
                                    <li>You are responsible for maintaining the confidentiality of your account credentials</li>
                                    <li>You must notify us immediately of any unauthorized access</li>
                                    <li>You are responsible for all activities that occur under your account</li>
                                </ul>
                            </div>

                            <div class="mb-5">
                                <h3 class="mb-3">4. Money Transfer Services</h3>
                                <h5 class="mb-3">4.1 Service Description</h5>
                                <p class="text-muted mb-3">
                                    Jaybtak Pro facilitates international money transfers. We act as an intermediary between senders 
                                    and recipients, working with partner banks and payment providers.
                                </p>

                                <h5 class="mb-3">4.2 Transaction Limits</h5>
                                <ul class="text-muted mb-3">
                                    <li>Minimum transfer amount: $10 USD</li>
                                    <li>Maximum transfer amount varies by country and verification level</li>
                                    <li>Daily, weekly, and monthly limits may apply</li>
                                </ul>

                                <h5 class="mb-3">4.3 Fees and Exchange Rates</h5>
                                <ul class="text-muted">
                                    <li>Transfer fees are displayed before you confirm your transaction</li>
                                    <li>Exchange rates are determined at the time of transfer</li>
                                    <li>Additional fees may be charged by recipient banks</li>
                                </ul>
                            </div>

                            <div class="mb-5">
                                <h3 class="mb-3">5. Verification and Compliance</h3>
                                <p class="text-muted mb-3">
                                    We are required by law to verify the identity of our users. You agree to provide:
                                </p>
                                <ul class="text-muted">
                                    <li>Valid government-issued identification</li>
                                    <li>Proof of address</li>
                                    <li>Additional information as required for compliance</li>
                                    <li>Source of funds documentation for large transactions</li>
                                </ul>
                            </div>

                            <div class="mb-5">
                                <h3 class="mb-3">6. Prohibited Activities</h3>
                                <p class="text-muted mb-3">You may not use our services to:</p>
                                <ul class="text-muted">
                                    <li>Engage in illegal activities or money laundering</li>
                                    <li>Finance terrorism or other criminal activities</li>
                                    <li>Violate any applicable laws or regulations</li>
                                    <li>Impersonate another person or entity</li>
                                    <li>Interfere with or disrupt our services</li>
                                    <li>Attempt to gain unauthorized access to our systems</li>
                                </ul>
                            </div>

                            <div class="mb-5">
                                <h3 class="mb-3">7. Cancellation and Refunds</h3>
                                <p class="text-muted mb-3">
                                    Transactions may be cancelled before funds are disbursed to the recipient. Cancellation policies vary 
                                    by delivery method:
                                </p>
                                <ul class="text-muted">
                                    <li>Bank transfers: Cancellable within 30 minutes of initiation</li>
                                    <li>Cash pickup: Cancellable before recipient collects funds</li>
                                    <li>Refunds are processed within 5-10 business days</li>
                                </ul>
                            </div>

                            <div class="mb-5">
                                <h3 class="mb-3">8. Limitation of Liability</h3>
                                <p class="text-muted">
                                    Jaybtak Pro shall not be liable for any indirect, incidental, special, consequential, or punitive 
                                    damages resulting from your use of our services. Our total liability shall not exceed the amount of 
                                    fees paid by you in the transaction giving rise to the claim.
                                </p>
                            </div>

                            <div class="mb-5">
                                <h3 class="mb-3">9. Dispute Resolution</h3>
                                <p class="text-muted">
                                    Any disputes arising from these Terms shall be resolved through binding arbitration in accordance with 
                                    the rules of the American Arbitration Association. You waive your right to participate in class action lawsuits.
                                </p>
                            </div>

                            <div class="mb-5">
                                <h3 class="mb-3">10. Changes to Terms</h3>
                                <p class="text-muted">
                                    We reserve the right to modify these Terms at any time. We will notify you of significant changes via 
                                    email or through our platform. Your continued use of our services constitutes acceptance of the modified Terms.
                                </p>
                            </div>

                            <div class="mb-5">
                                <h3 class="mb-3">11. Contact Information</h3>
                                <p class="text-muted mb-2">
                                    If you have questions about these Terms, please contact us at:
                                </p>
                                <p class="text-muted mb-0">
                                    Email: legal@Jaybtakpro.com<br>
                                    Phone: +1 (800) 123-4567<br>
                                    Address: 123 Money Street, Financial District, New York, NY 10004
                                </p>
                            </div>

                            <div class="alert alert-info" style="border-radius: var(--border-radius);">
                                <i data-lucide="info" class="me-2" style="width: 20px; height: 20px;"></i>
                                Please read these terms carefully before using our services. If you do not agree with any part of these terms, 
                                you should not use Jaybtak Pro.
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('privacy') }}" class="btn btn-outline-primary me-2">Privacy Policy</a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
