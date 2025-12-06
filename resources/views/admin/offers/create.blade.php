@extends('layouts.dashboard')
@section('title', 'Admin - Create Offer')
@section('page-title', 'Create Offer')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.offers.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Minimum Amount (USD)</label>
                <input type="number" step="0.01" name="min_amount" class="form-control" value="{{ old('min_amount', '500.00') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Discount Percentage</label>
                <input type="number" step="0.01" name="discount_percentage" class="form-control" value="{{ old('discount_percentage', '50.00') }}" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>

            <div class="mb-3">
                <label class="form-label">Start Date</label>
                <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">End Date</label>
                <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}">
            </div>

            <button class="btn btn-primary">Create Offer</button>
            <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
