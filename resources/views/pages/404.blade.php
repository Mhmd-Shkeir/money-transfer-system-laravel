@extends('layouts.app')

@section('title', '404 - Page Not Found')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="mb-5">
                    <div class="display-1 fw-bold text-primary-custom mb-3">404</div>
                    <h2 class="mb-3">Page Not Found</h2>
                    <p class="text-muted mb-4">
                        Oops! The page you're looking for doesn't exist. It might have been moved or deleted.
                    </p>
                </div>

                <!-- Illustration -->
                <div class="mb-5">
                    <div class="icon-box mx-auto mb-4" style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color));">
                        <i data-lucide="search" style="width: 60px; height: 60px; color: white;"></i>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i data-lucide="home" class="me-2" style="width: 16px; height: 16px;"></i>
                        Go Home
                    </a>
                    <button onclick="history.back()" class="btn btn-outline-primary">
                        <i data-lucide="arrow-left" class="me-2" style="width: 16px; height: 16px;"></i>
                        Go Back
                    </button>
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary">
                        <i data-lucide="help-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                        Contact Support
                    </a>
                </div>

                <!-- Quick Links -->
                <div class="mt-5 pt-5 border-top">
                    <h5 class="mb-4">You might be looking for:</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('user.send') }}" class="text-decoration-none">
                                <div class="card h-100 border hover-shadow" style="transition: all 0.2s;">
                                    <div class="card-body text-start">
                                        <i data-lucide="send" class="text-primary-custom mb-2" style="width: 24px; height: 24px;"></i>
                                        <h6 class="mb-1">Send Money</h6>
                                        <small class="text-muted">Transfer money globally</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('user.track') }}" class="text-decoration-none">
                                <div class="card h-100 border hover-shadow" style="transition: all 0.2s;">
                                    <div class="card-body text-start">
                                        <i data-lucide="map-pin" class="text-primary-custom mb-2" style="width: 24px; height: 24px;"></i>
                                        <h6 class="mb-1">Track Transfer</h6>
                                        <small class="text-muted">Monitor your transaction</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('about') }}" class="text-decoration-none">
                                <div class="card h-100 border hover-shadow" style="transition: all 0.2s;">
                                    <div class="card-body text-start">
                                        <i data-lucide="info" class="text-primary-custom mb-2" style="width: 24px; height: 24px;"></i>
                                        <h6 class="mb-1">About Us</h6>
                                        <small class="text-muted">Learn more about us</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('faq') }}" class="text-decoration-none">
                                <div class="card h-100 border hover-shadow" style="transition: all 0.2s;">
                                    <div class="card-body text-start">
                                        <i data-lucide="help-circle" class="text-primary-custom mb-2" style="width: 24px; height: 24px;"></i>
                                        <h6 class="mb-1">FAQ</h6>
                                        <small class="text-muted">Find answers</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-shadow:hover {
        box-shadow: 0 4px 12px rgba(0, 119, 182, 0.15);
        transform: translateY(-2px);
    }
</style>
@endpush
@endsection
