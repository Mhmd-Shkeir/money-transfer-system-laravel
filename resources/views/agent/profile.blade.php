@extends('layouts.dashboard')

@section('title', 'Agent Profile')
@section('page-title', 'Agent Profile')
@section('page-subtitle', 'Manage your business profile and operating hours')
@section('content')

<div class="row g-4">
    <!-- Main Content Area -->
    <div class="col-lg-8">
        <!-- Business Information Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Business Profile</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('agent.profile.update') }}" class="needs-validation">
                    @csrf

                    <!-- Personal Information Section -->
                    <div class="mb-4">
                        <h6 class="mb-3 fw-bold border-bottom pb-2">Personal Information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                       value="{{ old('first_name', $user->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                       value="{{ old('last_name', $user->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="mb-4">
                        <h6 class="mb-3 fw-bold border-bottom pb-2">Contact Information</h6>
                        <div class="mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Business Details Section -->
                    <div class="mb-4">
                        <h6 class="mb-3 fw-bold border-bottom pb-2">Business Details</h6>
                        <div class="mb-3">
                            <label class="form-label">Business Name *</label>
                            <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                                   value="{{ old('business_name', $agentProfile->business_name ?? '') }}" 
                                   placeholder="Your registered business name" required>
                            @error('business_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Business Registration Number *</label>
                                <input type="text" name="business_registration_number" 
                                       class="form-control @error('business_registration_number') is-invalid @enderror" 
                                       value="{{ old('business_registration_number', $agentProfile->business_registration_number ?? '') }}" 
                                       placeholder="BRN123456789" required>
                                @error('business_registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tax ID</label>
                                <input type="text" name="tax_id" class="form-control @error('tax_id') is-invalid @enderror" 
                                       value="{{ old('tax_id', $agentProfile->tax_id ?? '') }}" placeholder="Tax identification number">
                                @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Business Description</label>
                            <textarea name="business_description" class="form-control @error('business_description') is-invalid @enderror" 
                                      rows="3" placeholder="Describe your business and services">{{ old('business_description', $agentProfile->business_description ?? '') }}</textarea>
                            @error('business_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Office Address</label>
                            <input type="text" name="office_address" class="form-control @error('office_address') is-invalid @enderror" 
                                   value="{{ old('office_address', $agentProfile->office_address ?? '') }}" 
                                   placeholder="Full business address">
                            @error('office_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Country *</label>
                                <select name="country_id" class="form-select @error('country_id') is-invalid @enderror" required>
                                    <option value="">-- Select Country --</option>
                                    @foreach($countries ?? [] as $country)
                                        <option value="{{ $country->id }}" {{ old('country_id', $agentProfile->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Operating Currency *</label>
                                <select name="currency_id" class="form-select @error('currency_id') is-invalid @enderror" required>
                                    <option value="">-- Select Currency --</option>
                                    @foreach($currencies ?? [] as $currency)
                                        <option value="{{ $currency->id }}" {{ old('currency_id', $agentProfile->currency_id ?? '') == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('currency_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Business Contact Section -->
                    <div class="mb-4">
                        <h6 class="mb-3 fw-bold border-bottom pb-2">Business Contact & Online</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Business Phone</label>
                                <input type="tel" name="business_phone" class="form-control @error('business_phone') is-invalid @enderror" 
                                       value="{{ old('business_phone', $agentProfile->business_phone ?? '') }}" 
                                       placeholder="+1 (555) 123-4567">
                                @error('business_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Business Email</label>
                                <input type="email" name="business_email" class="form-control @error('business_email') is-invalid @enderror" 
                                       value="{{ old('business_email', $agentProfile->business_email ?? '') }}" 
                                       placeholder="business@example.com">
                                @error('business_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" 
                                   value="{{ old('website', $agentProfile->website ?? '') }}" 
                                   placeholder="https://www.example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Service Description</label>
                            <textarea name="service_description" class="form-control @error('service_description') is-invalid @enderror" 
                                      rows="3" placeholder="Describe the services you provide">{{ old('service_description', $agentProfile->service_description ?? '') }}</textarea>
                            @error('service_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Location & Timezone Section -->
                    <div class="mb-4">
                        <h6 class="mb-3 fw-bold border-bottom pb-2">Location & Timezone</h6>

                        <div class="mb-3">
                            <label class="form-label">Timezone *</label>
                            <select name="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                                <option value="">-- Select Timezone --</option>
                                @foreach($timezones as $tzKey => $tzName)
                                    <option value="{{ $tzKey }}" {{ old('timezone', $agentProfile->timezone ?? 'UTC') == $tzKey ? 'selected' : '' }}>
                                        {{ $tzName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Latitude (GPS)</label>
                                <input type="number" name="latitude" class="form-control @error('latitude') is-invalid @enderror" 
                                       value="{{ old('latitude', $agentProfile->latitude ?? '') }}" 
                                       step="0.00000001" placeholder="e.g., 40.7128">
                                <small class="text-muted">Map coordinates for business location</small>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitude (GPS)</label>
                                <input type="number" name="longitude" class="form-control @error('longitude') is-invalid @enderror" 
                                       value="{{ old('longitude', $agentProfile->longitude ?? '') }}" 
                                       step="0.00000001" placeholder="e.g., -74.0060">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Operating Hours Section -->
                    <div class="mb-4">
                        <h6 class="mb-3 fw-bold border-bottom pb-2">Operating Hours</h6>
                        <p class="text-muted mb-3"><small>Set your operating hours for each day. Mark as closed if not operating on that day.</small></p>

                        @php
                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        @endphp

                        @foreach($days as $index => $day)
                            @php
                                $dayLower = strtolower($day);
                                $businessHour = $businessHours->where('day_of_week', $index)->first();
                                $isClosed = $businessHour ? $businessHour->is_closed : true;
                                $openTime = $businessHour ? $businessHour->opening_time : '09:00';
                                $closeTime = $businessHour ? $businessHour->closing_time : '18:00';
                            @endphp
                            <div class="row align-items-center mb-2 pb-2 border-bottom">
                                <div class="col-md-3">
                                    <strong>{{ $day }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="closed_{{ $dayLower }}" 
                                               id="closed_{{ $dayLower }}" {{ $isClosed ? 'checked' : '' }}
                                               onchange="toggleBusinessHours('{{ $dayLower }}')">
                                        <label class="form-check-label small" for="closed_{{ $dayLower }}">
                                            Closed
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <input type="time" name="open_{{ $dayLower }}" 
                                           class="form-control form-control-sm business-hour-input-{{ $dayLower }}"
                                           value="{{ $openTime }}" {{ $isClosed ? 'disabled' : '' }}>
                                </div>
                                <div class="col-md-3">
                                    <input type="time" name="close_{{ $dayLower }}" 
                                           class="form-control form-control-sm business-hour-input-{{ $dayLower }}"
                                           value="{{ $closeTime }}" {{ $isClosed ? 'disabled' : '' }}>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('agent.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Account Status Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Account Status</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Account Type</small>
                    <p class="mb-0"><strong>Agent</strong></p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Current Status</small>
                    <p class="mb-0">
                        @if($user->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Suspended</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Member Since</small>
                    <p class="mb-0"><strong>{{ $user->created_at->format('M d, Y') }}</strong></p>
                </div>
                <div>
                    <small class="text-muted">Last Login</small>
                    <p class="mb-0"><strong>{{ $user->last_login ? $user->last_login->format('M d, Y H:i') : 'Never' }}</strong></p>
                </div>
            </div>
        </div>

        <!-- Business Status Card -->
        @if($agentProfile)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Business Status</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Business Name</small>
                    <p class="mb-0"><strong>{{ $agentProfile->business_name ?? 'Not set' }}</strong></p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Approval Status</small>
                    <p class="mb-0">
                        @if($agentProfile->is_approved)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending Review</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Commission Rate</small>
                    <p class="mb-0"><strong>{{ $agentProfile->commission_rate }}%</strong></p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Operating Country</small>
                    <p class="mb-0"><strong>{{ $agentProfile->country->name ?? 'Not set' }}</strong></p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Operating Currency</small>
                    <p class="mb-0">
                        @if($agentProfile->currency)
                            <strong>
                                <span class="badge bg-info">{{ $agentProfile->currency->code }}</span>
                                {{ $agentProfile->currency->name }}
                            </strong>
                        @else
                            <span class="text-muted">Not set</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Operating Hours Summary -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">Operating Hours Summary</h6>
            </div>
            <div class="card-body">
                @if($businessHours->count() > 0)
                    <div class="small">
                        @foreach($businessHours->sortBy('day_of_week') as $hours)
                            <div class="mb-2 pb-2 border-bottom">
                                <strong>{{ $hours->getDayName() }}</strong><br>
                                @if(!$hours->is_closed)
                                    <span class="text-success">{{ $hours->opening_time }} - {{ $hours->closing_time }}</span>
                                @else
                                    <span class="text-danger">Closed</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No business hours set yet</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function toggleBusinessHours(day) {
        const isChecked = document.getElementById('closed_' + day).checked;
        const inputs = document.querySelectorAll('.business-hour-input-' + day);
        inputs.forEach(input => {
            input.disabled = isChecked;
        });
    }

    // Bootstrap form validation
    (function () {
        'use strict';
        window.addEventListener('load', function () {
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    }());
</script>

@endsection
