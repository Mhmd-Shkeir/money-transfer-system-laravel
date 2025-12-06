@extends('layouts.dashboard')
@section('title','Customer Support')
@section('page-title','Customer Support')
@section('page-subtitle','Contact our support team')
@section('content')

<div class="card">
    <div class="card-header"><h5>Contact Support</h5></div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('support.send') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input name="subject" class="form-control" required value="{{ old('subject') }}">
                @error('subject') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="6" required>{{ old('message') }}</textarea>
                @error('message') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button class="btn btn-primary">Send to Support</button>
        </form>
    </div>
</div>

@if(!empty($tickets) && $tickets->count())
    <div class="card mt-4">
        <div class="card-header"><h5>My Support Requests</h5></div>
        <div class="card-body">
            @foreach($tickets as $t)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>#{{ $t->id }} {{ $t->subject }}</strong>
                            <div><small class="text-muted">Status: {{ ucfirst($t->status) }} — Submitted: {{ $t->created_at ? $t->created_at->format('Y-m-d H:i') : '' }}</small></div>
                        </div>
                    </div>
                    <div class="mt-2 p-2 border" style="white-space:pre-wrap">{{ $t->message }}</div>

                    @if($t->messages && $t->messages->count())
                        <div class="mt-2">
                            <h6>Replies</h6>
                            @foreach($t->messages as $m)
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div><strong>{{ $m->sender_role === 'admin' ? 'Admin' : ($m->user?->first_name ?? 'User') }}</strong></div>
                                            <div><small class="text-muted">{{ $m->created_at ? $m->created_at->format('Y-m-d H:i') : '' }}</small></div>
                                        </div>
                                        <div class="mt-2" style="white-space:pre-wrap">{{ $m->message }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection
