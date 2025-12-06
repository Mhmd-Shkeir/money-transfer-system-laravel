@extends('layouts.dashboard')
@section('title', $title . ' - Reports')
@section('page-title', $title)
@section('page-subtitle', 'List and inspect ' . strtolower($title))
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $title }}</h5>
        <div>
            <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-secondary">Back to Reports</a>
        </div>
    </div>
    <div class="card-body">
        @if($users->isEmpty())
            <p class="text-muted">No {{ strtolower($title) }} found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Agent Profile</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td>{{ $u->id }}</td>
                                <td>{{ $u->first_name }} {{ $u->last_name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->role }}</td>
                                <td>{{ $u->created_at->format('Y-m-d') }}</td>
                                <td>
                                    @if($u->agentProfile)
                                        <small>{{ $u->agentProfile->business_name ?? '—' }}</small>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
