@extends('layouts.dashboard')

@section('title', 'Compliance - Admin')
@section('page-title', 'Compliance Management')
@section('page-subtitle', 'Monitor and manage compliance activities')

@section('content')

{{-- ADD COMPLIANCE --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Add Compliance Record</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.compliance.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <label>Type</label>
                    <input type="text" name="type" class="form-control" required placeholder="Ex: KYC, AML, Policy...">
                </div>

                <div class="col-md-8">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="2" required></textarea>
                </div>
            </div>

            <button class="btn btn-primary mt-3">Add Record</button>
        </form>
    </div>
</div>



{{-- LIST COMPLIANCE --}}
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Compliance Records</h5>
    </div>

    <div class="card-body">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            @foreach($compliances as $item)
                <tr>
                    <td>{{ $item->type }}</td>
                    <td>{{ $item->description }}</td>
                    <td>
                        <form action="{{ route('admin.compliance.update', $item->id) }}" method="POST" class="d-flex">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-control">
                                <option value="pending"  {{ $item->status=='pending'?'selected':'' }}>Pending</option>
                                <option value="approved" {{ $item->status=='approved'?'selected':'' }}>Approved</option>
                                <option value="rejected" {{ $item->status=='rejected'?'selected':'' }}>Rejected</option>
                            </select>
                            <button class="btn btn-success ms-2">Save</button>
                        </form>
                    </td>
                    <td>{{ $item->created_at ? $item->created_at->format('Y-m-d H:i') : '' }}</td>

                    <td>
                        <form action="{{ route('admin.compliance.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection
