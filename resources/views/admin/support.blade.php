@extends('layouts.dashboard')

@section('title', 'Support & Issues - Admin Dashboard')
@section('page-title', 'Customer Support')
@section('page-subtitle', 'Manage support tickets and customer issues')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="mb-2">Open Tickets</h6>
                    <h3 class="mb-0">24</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="mb-2">In Progress</h6>
                    <h3 class="mb-0">12</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-2">Resolved</h6>
                    <h3 class="mb-0">156</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="mb-2">High Priority</h6>
                    <h3 class="mb-0">3</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Support Tickets</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>User</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#TK-001</td>
                                    <td>John Doe</td>
                                    <td>Transfer not received</td>
                                    <td><span class="badge bg-danger">High</span></td>
                                    <td><span class="badge bg-warning">In Progress</span></td>
                                    <td>2025-11-10</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#TK-002</td>
                                    <td>Jane Smith</td>
                                    <td>Account verification issue</td>
                                    <td><span class="badge bg-warning">Medium</span></td>
                                    <td><span class="badge bg-info">Open</span></td>
                                    <td>2025-11-09</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
