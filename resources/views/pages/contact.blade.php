@extends('layouts.app')

@section('title', 'Contact Us - MoneyTransfer Pro')

@section('content')
    @include('partials.navbar')

    <section class="py-5 bg-light">
        <div class="container py-5">
            <div class="row">
                <!-- Contact Form -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <h2 class="mb-4">Get in Touch</h2>
                            <p class="text-muted mb-4">
                                Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                            </p>

                            @if(session('success'))
                                <div class="alert alert-success" style="border-radius: var(--border-radius);">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('contact.submit') }}">
                                @csrf

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold">Full Name</label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name') }}"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">Email Address</label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}"
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="subject" class="form-label fw-semibold">Subject</label>
                                        <input type="text" 
                                               class="form-control @error('subject') is-invalid @enderror" 
                                               id="subject" 
                                               name="subject" 
                                               value="{{ old('subject') }}"
                                               required>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="message" class="form-label fw-semibold">Message</label>
                                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                                  id="message" 
                                                  name="message" 
                                                  rows="5" 
                                                  required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i data-lucide="send" class="me-2" style="width: 16px; height: 16px;"></i>
                                            Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-5">
                    <div class="h-100 d-flex flex-column gap-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-box bg-primary-light mb-3">
                                    <i data-lucide="mail" class="text-primary-custom" style="width: 24px; height: 24px;"></i>
                                </div>
                                <h5 class="mb-2">Email Us</h5>
                                <p class="text-muted mb-0">support@moneytransferpro.com</p>
                                <p class="text-muted mb-0">info@moneytransferpro.com</p>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-box bg-success-light mb-3">
                                    <i data-lucide="phone" class="text-success-custom" style="width: 24px; height: 24px;"></i>
                                </div>
                                <h5 class="mb-2">Call Us</h5>
                                <p class="text-muted mb-0">+1 (800) 123-4567</p>
                                <p class="text-muted mb-0">Available 24/7</p>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-box bg-accent-light mb-3">
                                    <i data-lucide="map-pin" style="width: 24px; height: 24px; color: var(--accent-color);"></i>
                                </div>
                                <h5 class="mb-2">Visit Us</h5>
                                <p class="text-muted mb-0">123 Money Street</p>
                                <p class="text-muted mb-0">Financial District</p>
                                <p class="text-muted mb-0">New York, NY 10004</p>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm gradient-bg text-white">
                            <div class="card-body p-4">
                                <h5 class="mb-3">Business Hours</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Monday - Friday:</span>
                                    <span>9:00 AM - 6:00 PM</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Saturday:</span>
                                    <span>10:00 AM - 4:00 PM</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Sunday:</span>
                                    <span>Closed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.footer')
@endsection
