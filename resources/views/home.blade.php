@extends('layouts.app')

@section('title', 'Jaybtak Pro - Send Money Globally, Instantly')

@section('content')

    <!-- 🟦 Hero Section -->
    <section class="py-5" style="background-color: #fff;">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Send Money Globally, <span class="text-primary">Instantly</span>
                    </h1>
                    <p class="lead text-secondary mb-4">
                        Transfer money to over 200 countries with the best exchange rates and lowest fees. Fast, secure, and reliable.
                    </p>
                    <div class="d-flex gap-3 mb-5">
                        <a href="{{ route('signup.form') }}" class="btn btn-primary d-inline-flex align-items-center">
                            Get Started 
                            <i data-lucide="arrow-right" class="ms-2" style="width: 16px; height: 16px;"></i>
                        </a>
                        <a href="{{ route('about') }}" class="btn btn-outline-primary">Learn More</a>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-4 text-center text-md-start">
                            <h3 class="text-primary mb-1">200+</h3>
                            <p class="text-secondary mb-0">Countries</p>
                        </div>
                        <div class="col-4 text-center text-md-start">
                            <h3 class="text-primary mb-1">$5B+</h3>
                            <p class="text-secondary mb-0">Transferred</p>
                        </div>
                        <div class="col-4 text-center text-md-start">
                            <h3 class="text-primary mb-1">5M+</h3>
                            <p class="text-secondary mb-0">Users</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 text-center">
                    <div class="shadow-lg rounded overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=800&h=500&fit=crop" 
                             alt="Money transfer illustration" 
                             class="img-fluid rounded" 
                             style="height: 400px; object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- 🟧 How It Works -->
    <section class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">How It Works</h2>
                <p class="text-secondary">Send money in 3 simple steps</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width: 48px; height: 48px; background-color: var(--primary-color, #0d6efd); color: #fff; font-weight: bold;">
                        1
                    </div>
                    <h4 class="fw-semibold mb-2">Enter Amount</h4>
                    <p class="text-secondary">Choose the recipient country and enter the amount you want to send.</p>
                </div>

                <div class="col-md-4 text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width: 48px; height: 48px; background-color: var(--primary-color, #0d6efd); color: #fff; font-weight: bold;">
                        2
                    </div>
                    <h4 class="fw-semibold mb-2">Choose Delivery</h4>
                    <p class="text-secondary">Select how the recipient will receive the money — bank transfer or cash pickup.</p>
                </div>

                <div class="col-md-4 text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width: 48px; height: 48px; background-color: var(--primary-color, #0d6efd); color: #fff; font-weight: bold;">
                        3
                    </div>
                    <h4 class="fw-semibold mb-2">Track Transfer</h4>
                    <p class="text-secondary">Monitor your transfer in real-time until it reaches the recipient.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- 🟩 Why Choose Us -->
    <section class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Why Choose Us</h2>
                <p class="text-secondary">Trusted by millions worldwide</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 p-4 shadow-sm">
                        <i data-lucide="shield" class="text-primary mb-3" style="width: 48px; height: 48px;"></i>
                        <h4 class="fw-semibold mb-2">Secure & Safe</h4>
                        <p class="text-secondary">Bank-level encryption and security for all your transactions.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 p-4 shadow-sm">
                        <i data-lucide="zap" class="text-primary mb-3" style="width: 48px; height: 48px;"></i>
                        <h4 class="fw-semibold mb-2">Fast Transfer</h4>
                        <p class="text-secondary">Money reaches in minutes with our express service.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 p-4 shadow-sm">
                        <i data-lucide="globe" class="text-primary mb-3" style="width: 48px; height: 48px;"></i>
                        <h4 class="fw-semibold mb-2">Global Reach</h4>
                        <p class="text-secondary">Send money to over 200 countries and territories.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 p-4 shadow-sm">
                        <i data-lucide="trending-up" class="text-primary mb-3" style="width: 48px; height: 48px;"></i>
                        <h4 class="fw-semibold mb-2">Best Rates</h4>
                        <p class="text-secondary">Competitive exchange rates with transparent pricing.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 p-4 shadow-sm">
                        <i data-lucide="lock" class="text-primary mb-3" style="width: 48px; height: 48px;"></i>
                        <h4 class="fw-semibold mb-2">Licensed & Regulated</h4>
                        <p class="text-secondary">Fully compliant with international financial regulations.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 p-4 shadow-sm">
                        <i data-lucide="users" class="text-primary mb-3" style="width: 48px; height: 48px;"></i>
                        <h4 class="fw-semibold mb-2">24/7 Support</h4>
                        <p class="text-secondary">Our customer support team is always ready to help.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
