@extends('layouts.dashboard')

@section('title', 'User Details')
@section('page-title', 'User Details')
@section('page-subtitle', 'Overview')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h5>{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                        <i data-lucide="edit" style="width: 16px; height: 16px; display: inline; margin-right: 4px;"></i> Edit User
                    </a>
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">Back to Users</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">User Information</h6>
                    <table class="table table-sm">
                        <tr><th>First Name</th><td>{{ $user->first_name }}</td></tr>
                        <tr><th>Last Name</th><td>{{ $user->last_name }}</td></tr>
                        <tr><th>Email</th><td>{{ $user->email }}</td></tr>
                        <tr><th>Phone</th><td>{{ $user->phone ?? '—' }}</td></tr>
                        <tr><th>Country</th><td>{{ $user->country ?? '—' }}</td></tr>
                        <tr><th>Date of Birth</th><td>{{ optional($user->date_of_birth)->format('M j, Y') ?? '—' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Account Status</h6>
                    <table class="table table-sm">
                        <tr><th>Role</th><td><span class="badge bg-primary">{{ ucfirst($user->role) }}</span></td></tr>
                        <tr><th>Status</th><td>
                            @if($user->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                            @endif
                        </td></tr>
                        <tr><th>Email Verified</th><td>{{ $user->is_email_verified ? '✓ Yes' : '✗ No' }}</td></tr>
                        <tr><th>Phone Verified</th><td>{{ $user->is_phone_verified ? '✓ Yes' : '✗ No' }}</td></tr>
                        <tr><th>Last Login</th><td>{{ optional($user->last_login)->format('M j, Y H:i') ?? 'Never' }}</td></tr>
                        <tr><th>Joined</th><td>{{ $user->created_at->format('M j, Y') }}</td></tr>
                    </table>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">Activity</h6>
                    <ul class="list-unstyled">
                        <li><strong>Transactions:</strong> {{ $user->transactions()->count() }}</li>
                        <li><strong>Beneficiaries:</strong> {{ $user->beneficiaries()->count() }}</li>
                        <li><strong>Reviews Given:</strong> {{ $user->reviews()->count() }}</li>
                        <li><strong>Disputes:</strong> {{ $user->disputes()->count() }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
