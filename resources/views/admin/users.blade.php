@extends('layouts.dashboard')

@section('title', 'Users - Admin')
@section('page-title', 'User Management')
@section('page-subtitle', 'List of registered users')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">All Users</h5>
                <form class="d-flex" method="GET" action="{{ route('admin.users') }}">
                    <input name="q" value="{{ request('q') }}" class="form-control form-control-sm me-2" placeholder="Search name or email">
                    <button class="btn btn-sm btn-outline-secondary">Search</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>{{ optional($user->last_login)->format('M j, Y H:i') ?? 'Never' }}</td>
                                <td>
                                    @if($user->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary" title="View details">
                                        <i data-lucide="eye" style="width: 16px; height: 16px;"></i> View
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning" title="Edit user">
                                        <i data-lucide="edit" style="width: 16px; height: 16px;"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
