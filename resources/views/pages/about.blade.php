@extends('layouts.app')

@section('title', 'About Jaybtak Pro')

@section('content')

    <!-- Hero Section -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%); color: white;">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">About Jaybtak Pro</h1>
                    <p class="lead mb-4">
                        We're revolutionizing global money transfers with cutting-edge technology, transparent pricing, and exceptional customer service.
                    </p>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=400&fit=crop" 
                         alt="About us" 
                         class="img-fluid rounded shadow-lg" 
                         style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission -->
    <section class="py-5 bg-white">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=400&fit=crop" 
                         alt="Our Mission" 
                         class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Our Mission</h2>
                    <p class="text-secondary mb-3">
                        Jaybtak Pro was founded with a simple mission: to make international money transfers accessible, affordable, and trustworthy for everyone.
                    </p>
                    <p class="text-secondary mb-3">
                        We believe that geography should never be a barrier to sending money to loved ones. That's why we've built a platform that combines the latest technology with a human touch.
                    </p>
                    <p class="text-secondary">
                        Our commitment to transparency, security, and customer satisfaction sets us apart in the fintech industry.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Our Core Values</h2>
                <p class="text-secondary">What drives everything we do</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 p-4 shadow-sm text-center">
                        <i data-lucide="heart" class="text-danger mb-3 mx-auto" style="width: 48px; height: 48px;"></i>
                        <h5 class="fw-semibold mb-2">Customer First</h5>
                        <p class="text-secondary">We put our customers at the heart of everything we do.</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100 p-4 shadow-sm text-center">
                        <i data-lucide="shield" class="text-primary mb-3 mx-auto" style="width: 48px; height: 48px;"></i>
                        <h5 class="fw-semibold mb-2">Security</h5>
                        <p class="text-secondary">Your funds and data are protected with military-grade encryption.</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100 p-4 shadow-sm text-center">
                        <i data-lucide="eye" class="text-success mb-3 mx-auto" style="width: 48px; height: 48px;"></i>
                        <h5 class="fw-semibold mb-2">Transparency</h5>
                        <p class="text-secondary">No hidden fees. You know exactly what you're paying.</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card h-100 p-4 shadow-sm text-center">
                        <i data-lucide="zap" class="text-warning mb-3 mx-auto" style="width: 48px; height: 48px;"></i>
                        <h5 class="fw-semibold mb-2">Innovation</h5>
                        <p class="text-secondary">Constantly improving through technology and feedback.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story -->
    <section class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Our Story</h2>
            </div>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="timeline">
                        <div class="timeline-item mb-5 pb-5 border-bottom">
                            <div class="d-flex gap-4">
                                <div class="text-primary fw-bold" style="min-width: 100px;">2020</div>
                                <div>
                                    <h5 class="fw-semibold mb-2">Founded</h5>
                                    <p class="text-secondary">Jaybtak Pro was founded by a team of fintech experts with a vision to democratize international money transfers.</p>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item mb-5 pb-5 border-bottom">
                            <div class="d-flex gap-4">
                                <div class="text-primary fw-bold" style="min-width: 100px;">2021</div>
                                <div>
                                    <h5 class="fw-semibold mb-2">Launched Platform</h5>
                                    <p class="text-secondary">We officially launched our platform, serving customers across 50+ countries on day one.</p>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item mb-5 pb-5 border-bottom">
                            <div class="d-flex gap-4">
                                <div class="text-primary fw-bold" style="min-width: 100px;">2022</div>
                                <div>
                                    <h5 class="fw-semibold mb-2">Expanded Services</h5>
                                    <p class="text-secondary">Added Agent network, cash pickup services, and expanded to 150+ countries.</p>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="d-flex gap-4">
                                <div class="text-primary fw-bold" style="min-width: 100px;">2024</div>
                                <div>
                                    <h5 class="fw-semibold mb-2">Milestone: 5M Users</h5>
                                    <p class="text-secondary">Reached 5 million registered users and processed over $5 billion in transfers globally.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold mb-3">Why Choose Jaybtak Pro?</h2>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex gap-3">
                        <div>
                            <i data-lucide="check-circle" class="text-success" style="width: 24px; height: 24px; flex-shrink: 0;"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-2">Best Exchange Rates</h5>
                            <p class="text-secondary mb-0">Real-time rates with the lowest markup in the industry.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-3">
                        <div>
                            <i data-lucide="check-circle" class="text-success" style="width: 24px; height: 24px; flex-shrink: 0;"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-2">Low Fees</h5>
                            <p class="text-secondary mb-0">Transparent pricing with no hidden charges.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-3">
                        <div>
                            <i data-lucide="check-circle" class="text-success" style="width: 24px; height: 24px; flex-shrink: 0;"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-2">Fast Transfers</h5>
                            <p class="text-secondary mb-0">Most transfers complete within minutes, not days.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-3">
                        <div>
                            <i data-lucide="check-circle" class="text-success" style="width: 24px; height: 24px; flex-shrink: 0;"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-2">24/7 Support</h5>
                            <p class="text-secondary mb-0">Our support team is always ready to help you.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-3">
                        <div>
                            <i data-lucide="check-circle" class="text-success" style="width: 24px; height: 24px; flex-shrink: 0;"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-2">Fully Regulated</h5>
                            <p class="text-secondary mb-0">Licensed and compliant with international standards.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-3">
                        <div>
                            <i data-lucide="check-circle" class="text-success" style="width: 24px; height: 24px; flex-shrink: 0;"></i>
                        </div>
                        <div>
                            <h5 class="fw-semibold mb-2">Bank-Level Security</h5>
                            <p class="text-secondary mb-0">Military-grade encryption protects your data.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%); color: white;">
        <div class="container py-5 text-center">
            <h2 class="fw-bold mb-4">Ready to Send Money Globally?</h2>
            <p class="lead mb-4">Join millions of people who trust us with their international money transfers.</p>
            <a href="{{ route('signup.form') }}" class="btn btn-light btn-lg d-inline-flex align-items-center">
                Get Started Now
                <i data-lucide="arrow-right" class="ms-2" style="width: 20px; height: 20px;"></i>
            </a>
        </div>
    </section>

@endsection
