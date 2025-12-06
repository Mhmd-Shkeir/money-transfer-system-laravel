@extends('layouts.dashboard')
@section('title', 'My Profile - User')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account settings')
@section('content')

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Profile Information</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('user.profile.update') }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                   value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                   value="{{ old('last_name', $user->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" 
                               value="{{ old('address', $user->address) }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" value="{{ $user->date_of_birth }}" disabled>
                        <small class="text-muted">Cannot be changed</small>
                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">New Password (optional)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Account Information</h5>
                <div class="mb-3">
                    <small class="text-muted">Account Type</small>
                    <p class="mb-0"><strong>{{ ucfirst($user->role === 'user' ? 'Client' : $user->role) }}</strong></p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Status</small>
                    <p class="mb-0">
                        @if($user->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Suspended</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Email Verified</small>
                    <p class="mb-0">
                        @if($user->is_email_verified)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
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
    </div>
</div>

@endsection
