@extends('layouts.dashboard')
@section('title', 'Beneficiaries - User')
@section('page-title', 'Manage Beneficiaries')
@section('page-subtitle', 'Add and manage your beneficiaries')
@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Your Beneficiaries</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($beneficiaries->isEmpty())
                    <p class="text-muted">You have no beneficiaries yet.</p>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Country</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($beneficiaries as $b)
                                <tr>
                                    <td>{{ $b->name }}</td>
                                    <td>{{ $b->country?->name ?? '-' }}</td>
                                    <td>{{ $b->phone ?? '-' }}</td>
                                    <td>{{ $b->email ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('user.beneficiaries.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Delete beneficiary?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Add Beneficiary</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user.beneficiaries.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input name="name" class="form-control" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Country</label>
                        <select name="country_id" class="form-control" required>
                            <option value="">Select country</option>
                            @foreach(App\Models\Country::all() as $c)
                                <option value="{{ $c->id }}" {{ old('country_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" value="{{ old('email') }}">
                        @error('email')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank name (optional)</label>
                        <input name="bank_name" class="form-control" value="{{ old('bank_name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank account (optional)</label>
                        <input name="bank_account" class="form-control" value="{{ old('bank_account') }}">
                    </div>
                    <button class="btn btn-primary">Add beneficiary</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
