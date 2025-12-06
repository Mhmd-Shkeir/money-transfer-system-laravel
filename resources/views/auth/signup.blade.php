<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 40px; }
        .signup-container { max-width: 900px; margin: 0 auto; padding: 20px; }
        .card { box-shadow: 0 0 15px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Sign Up</h3>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('signup') }}">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                                   class="form-control @error('first_name') is-invalid @enderror" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                                   class="form-control @error('last_name') is-invalid @enderror" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required>
                    </div>

                    <div class="row mb-3">
        <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password"
                class="form-control @error('password') is-invalid @enderror" required>
            <!-- <small class="text-muted">
                Password must:
                <ul>
                    <li>Be 8-30 characters long</li>
                    <li>Include at least one uppercase letter</li>
                    <li>Include at least one lowercase letter</li>
                    <li>Include at least one number</li>
                    <li>Include at least one special character (@$!%*#?&)</li>
                </ul>
            </small> -->
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
        </div>
    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                                   class="form-control @error('phone') is-invalid @enderror" required>
                        </div>
                        <div class="col-md-6">
                            <label for="country" class="form-label">Country</label>
                            <input id="country" type="text" name="country" value="{{ old('country') }}"
                                   class="form-control @error('country') is-invalid @enderror" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input id="address" type="text" name="address" value="{{ old('address') }}"
                               class="form-control @error('address') is-invalid @enderror" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                   class="form-control @error('date_of_birth') is-invalid @enderror" required>
                        </div>
                        <div class="col-md-6">
                            <label for="national_id" class="form-label">National ID</label>
                            <input id="national_id" type="text" name="national_id" value="{{ old('national_id') }}"
                                   class="form-control @error('national_id') is-invalid @enderror" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Account Type <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">-- Select your account type --</option>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Client (Send/Receive Money)</option>
                            <option value="agent" {{ old('role') === 'agent' ? 'selected' : '' }}>Agent/Partner Store</option>
                        </select>
                        @error('role')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Store Name Field (visible only for agents) -->
                    <div class="mb-3" id="store-name-field" style="display: none;">
                        <label for="store_name" class="form-label">Store Name <span class="text-danger">*</span></label>
                        <input type="text" id="store_name" name="store_name" 
                               class="form-control @error('store_name') is-invalid @enderror" 
                               value="{{ old('store_name') }}"
                               placeholder="e.g., ABC Money Transfer, Quick Cash Store">
                        @error('store_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="text-muted">Your store name will be visible to customers</small>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Register</button>
                        <a href="{{ url('/') }}" class="btn btn-link">Home</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide store name field based on role selection
        const roleSelect = document.getElementById('role');
        const storeNameField = document.getElementById('store-name-field');
        const storeNameInput = document.getElementById('store_name');

        function toggleStoreNameField() {
            if (roleSelect.value === 'agent') {
                storeNameField.style.display = 'block';
                storeNameInput.required = true;
            } else {
                storeNameField.style.display = 'none';
                storeNameInput.required = false;
            }
        }

        roleSelect.addEventListener('change', toggleStoreNameField);
        
        // Check on page load (if form had validation error)
        toggleStoreNameField();
    </script>