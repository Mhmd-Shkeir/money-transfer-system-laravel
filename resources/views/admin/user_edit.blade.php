@extends('layouts.dashboard')

@section('title', 'Edit User - Admin')
@section('page-title', 'Edit User')
@section('page-subtitle', $user->first_name . ' ' . $user->last_name)

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                               value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                               value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', $user->phone) }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" 
                               value="{{ old('country', $user->country) }}">
                        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                               value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}">
                        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                            <option value="agent" {{ old('role', $user->role) === 'agent' ? 'selected' : '' }}>Agent</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_email_verified" class="form-check-input" 
                                   value="1" {{ old('is_email_verified', $user->is_email_verified) ? 'checked' : '' }}>
                            <label class="form-check-label">Email Verified</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_phone_verified" class="form-check-input" 
                                   value="1" {{ old('is_phone_verified', $user->is_phone_verified) ? 'checked' : '' }}>
                            <label class="form-check-label">Phone Verified</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" style="width: 16px; height: 16px; display: inline; margin-right: 4px;"></i> Save Changes
                    </button>
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
