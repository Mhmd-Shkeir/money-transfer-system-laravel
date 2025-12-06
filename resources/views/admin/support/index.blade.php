@extends('layouts.dashboard')
@section('title','Support Tickets')
@section('page-title','Support Tickets')
@section('page-subtitle','Messages from clients and agents')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Support Tickets</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($tickets->isEmpty())
            <p class="text-muted">No support tickets.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>From</th>
                            <th>Subject</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $t)
                            <tr>
                                <td>{{ $t->id }}</td>
                                <td>{{ $t->user?->first_name }} {{ $t->user?->last_name }} <br><small class="text-muted">{{ $t->user?->email }}</small></td>
                                <td>{{ $t->subject }}</td>
                                <td>{{ ucfirst($t->source ?? 'user') }}</td>
                                <td>{{ ucfirst($t->status) }}</td>
                                <td>{{ $t->created_at ? $t->created_at->format('Y-m-d H:i') : '' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.support.show', $t->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $tickets->links() }}</div>
        @endif
    </div>
</div>

@endsection
