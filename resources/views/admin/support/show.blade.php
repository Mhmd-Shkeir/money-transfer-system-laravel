@extends('layouts.dashboard')
@section('title','Support Ticket')
@section('page-title','Support Ticket')
@section('page-subtitle','View message')
@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Ticket #{{ $ticket->id }}</h5>
        <div>
            @if($ticket->status !== 'closed')
                <form method="POST" action="{{ route('admin.support.close', $ticket->id) }}" style="display:inline">
                    @csrf
                    <button class="btn btn-sm btn-danger">Close Ticket</button>
                </form>
            @endif
            <a href="{{ route('admin.support') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <p><strong>From:</strong> {{ $ticket->user?->first_name }} {{ $ticket->user?->last_name }} &lt;{{ $ticket->user?->email }}&gt;</p>
        <p><strong>Source:</strong> {{ ucfirst($ticket->source ?? 'user') }}</p>
        <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
        <hr>
        <div style="white-space:pre-wrap">{{ $ticket->message }}</div>
        <hr>

        {{-- Threaded messages --}}
        @if($ticket->messages && $ticket->messages->count())
            <h6>Conversation</h6>
            <div class="mb-3">
                @foreach($ticket->messages as $m)
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $m->sender_role === 'admin' ? 'Admin' : ($m->user?->first_name ?? 'User') }}</strong>
                                    @if($m->user)
                                        <small class="text-muted"> — {{ $m->user->email }}</small>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $m->created_at ? $m->created_at->format('Y-m-d H:i') : '' }}</small>
                            </div>
                            <div style="white-space:pre-wrap;margin-top:8px">{{ $m->message }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Reply form for admin --}}
        <hr>
        <h6>Send Reply</h6>
        <form method="POST" action="{{ route('admin.support.reply', $ticket->id) }}">
            @csrf
            <div class="form-group">
                <textarea name="message" class="form-control" rows="5" required></textarea>
            </div>
            <div class="mt-2">
                <button class="btn btn-primary">Send Reply</button>
            </div>
        </form>
        <hr>
        <p class="text-muted">Submitted: {{ $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '' }}</p>
    </div>
</div>

@endsection
