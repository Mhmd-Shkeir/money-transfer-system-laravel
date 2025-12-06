@extends('layouts.dashboard')

@section('title', 'Exchange Rates - Admin')
@section('page-title', 'Exchange Rates Management')
@section('page-subtitle', 'Manage currencies and rates')

@section('content')

{{-- =========================== --}}
{{--      ADD CURRENCY FORM      --}}
{{-- =========================== --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Add New Currency</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.currencies.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <label>Currency Code (USD, EUR...)</label>
                    <input type="text" name="code" class="form-control" required maxlength="3">
                </div>

                <div class="col-md-4">
                    <label>Currency Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Symbol ($, €, £...)</label>
                    <input type="text" name="symbol" class="form-control">
                </div>
            </div>

            <button class="btn btn-primary mt-3">Add Currency</button>
        </form>
    </div>
</div>

{{-- =========================== --}}
{{--    LIST EXISTING CURRENCIES --}}
{{-- =========================== --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Existing Currencies</h5>
    </div>

    <div class="card-body">
        @if($currencies->count() == 0)
            <p class="text-muted">No currencies added yet.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Symbol</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($currencies as $currency)
                        <tr>
                            <td>{{ $currency->code }}</td>
                            <td>{{ $currency->name }}</td>
                            <td>{{ $currency->symbol }}</td>

                            <td>
                                <form action="{{ route('admin.currencies.destroy', $currency->id) }}" 
                                      method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        @endif
    </div>
</div>


{{-- =========================== --}}
{{--   ADD EXCHANGE RATE FORM    --}}
{{-- =========================== --}}
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add New Exchange Rate</h5>
    </div>

    <div class="card-body">

        @if($currencies->count() == 0)
            <p class="text-danger">Add currencies first.</p>
        @else
        <form action="{{ route('admin.rates.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <label>From Currency</label>
                    <select name="from_currency_id" class="form-control">
                        @foreach($currencies as $c)
                            <option value="{{ $c->id }}">{{ $c->getFullName() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>To Currency</label>
                    <select name="to_currency_id" class="form-control">
                        @foreach($currencies as $c)
                            <option value="{{ $c->id }}">{{ $c->getFullName() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Rate</label>
                    <input type="number" step="0.0001" name="rate" class="form-control">
                </div>
            </div>

            <button class="btn btn-primary mt-3">Add Rate</button>
        </form>
        @endif

    </div>
</div>


{{-- =========================== --}}
{{--   LIST EXISTING RATES       --}}
{{-- =========================== --}}
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Existing Rates</h5>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>From</th>
                    <th>To</th>
                    <th>Rate</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($rates as $rate)
                    <tr>
                        <td>{{ $rate->fromCurrency->getFullName() }}</td>
                        <td>{{ $rate->toCurrency->getFullName() }}</td>

                        <td>
                            <form action="{{ route('admin.rates.update', $rate->id) }}" method="POST" class="d-flex">
                                @csrf
                                @method('PUT')
                                <input type="number" step="0.0001" name="rate" value="{{ $rate->rate }}" class="form-control">
                                <button class="btn btn-success ms-2">Save</button>
                            </form>
                        </td>

                        <td>
                            <form action="{{ route('admin.rates.destroy', $rate->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>

@endsection
