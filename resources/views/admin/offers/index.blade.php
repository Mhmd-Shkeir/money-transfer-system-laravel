@extends('layouts.dashboard')
@section('title', 'Admin - Offers')
@section('page-title', 'Offers')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Offers</h5>
        <a href="{{ route('admin.offers.create') }}" class="btn btn-primary">Create Offer</a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Min Amount</th>
                    <th>Discount %</th>
                    <th>Active</th>
                    <th>Period</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offers as $offer)
                    <tr>
                        <td>{{ $offer->id }}</td>
                        <td>{{ $offer->name }}</td>
                        <td>${{ number_format($offer->min_amount, 2) }}</td>
                        <td>{{ number_format($offer->discount_percentage, 2) }}%</td>
                        <td>{{ $offer->is_active ? 'Yes' : 'No' }}</td>
                        <td>
                            @if($offer->start_date){{ $offer->start_date->toDateString() }}@endif
                            -
                            @if($offer->end_date){{ $offer->end_date->toDateString() }}@endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
