<footer class="bg-dark text-white py-5" style="margin-bottom:0; margin-top: auto; background-color:#121416; width: 100%;">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-box icon-box-sm me-2" style="background-color: var(--primary-color);">
                        <span>💰</span>
                    </div>
                    <span class="fw-semibold">Jaybtak Pro</span>
                </div>
                <p class="text-white-50">Send money globally with confidence</p>
            </div>
            
            <div class="col-md-3">
                <h5 class="mb-3">Company</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('about') }}" class="text-white-50 text-decoration-none d-block mb-2">About Us</a></li>
                    <li><a href="{{ route('about') }}#faq" class="text-white-50 text-decoration-none d-block mb-2">FAQ</a></li>
                    <li><a href="{{ route('contact') }}" class="text-white-50 text-decoration-none d-block mb-2">Contact</a></li>
                </ul>
            </div>
            
            <div class="col-md-3">
                <h5 class="mb-3">Legal</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('terms') }}" class="text-white-50 text-decoration-none d-block mb-2">Terms of Service</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-white-50 text-decoration-none d-block mb-2">Privacy Policy</a></li>
                </ul>
            </div>
            
            <div class="col-md-3">
                <h5 class="mb-3">Support</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none d-block mb-2">Help Center</a></li>
                    <li><a href="{{ route('user.track') }}" class="text-white-50 text-decoration-none d-block mb-2">Track Transfer</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none d-block mb-2">Security</a></li>
                </ul>
            </div>
        </div>
        
        <hr class="border-secondary my-4">
        
        <div class="text-center text-white-50">
            <p class="mb-0">&copy; {{ date('Y') }} Jaybtak Pro. All rights reserved.</p>
        </div>
    </div>
</footer>
