@extends('layouts.dashboard')

@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-subtitle', 'View and manage your notifications')

@section('content')

<style>
    .pagination {
        margin-top: 2rem;
        justify-content: center;
    }
    
    .pagination .page-link {
        color: #0077B6;
        border-color: #dee2e6;
        padding: 0.5rem 0.75rem;
        font-size: 0.95rem;
    }
    
    .pagination .page-link:hover {
        color: #005a8d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0077B6;
        border-color: #0077B6;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        cursor: not-allowed;
        background-color: #fff;
        border-color: #dee2e6;
    }
</style>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
        <i class="fas fa-bell me-2"></i> Notifications
    </h5>

    @if($notifications->total() > 0)
        <div>
            <form method="POST" action="{{ route('notifications.mark-all-read') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    Mark All as Read
                </button>
            </form>

            <form method="POST" action="{{ route('notifications.delete-all') }}"
                  style="display: inline;"
                  onsubmit="return confirm('Are you sure you want to delete ALL notifications?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger ms-2">
                    Delete All
                </button>
            </form>
        </div>
    @endif
</div>


            <div class="card-body">
                @if($notifications->count() > 0)
                    <div class="notification-list">
                        @foreach($notifications as $notif)
                            <div class="notification-item mb-3 pb-3 border-bottom {{ !$notif->is_read ? 'bg-light rounded p-3' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <span class="badge badge-{{ notificationTypeBadgeClass($notif->type) }} me-2">
                                                {{ notificationTypeLabel($notif->type) }}
                                            </span>
                                            @if(!$notif->is_read)
                                                <span class="badge bg-primary">New</span>
                                            @endif
                                        </h6>
                                        <h6 class="mb-1">{{ $notif->title }}</h6>
                                        <p class="text-muted small mb-2">{{ $notif->message }}</p>
                                        <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="ms-2">
                                        @if(!$notif->is_read)
                                            <form method="POST" action="{{ route('notifications.mark-read', $notif->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Mark as read">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('notifications.delete', $notif->id) }}" style="display: inline;" onsubmit="return confirm('Delete this notification?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if($notif->related_transaction_id)
                                    @php
                                        $txId = $notif->related_transaction_id;
                                    @endphp
                                    <div class="mt-2">
                                        <a href="{{ route('transactions.view', $txId) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i>
                                            View Transaction
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Single Clean Pagination -->
                    @if($notifications->hasPages())
                        <nav aria-label="Pagination Navigation">
                            {{ $notifications->links('pagination::bootstrap-4') }}
                        </nav>
                    @endif
                @else
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-inbox" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-3">You have no notifications yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@php
function notificationTypeLabel($type) {
    return match($type) {
        'transfer_assigned' => 'Transfer Assigned',
        'transfer_completed' => 'Transfer Completed',
        'transfer_rejected' => 'Transfer Rejected',
        'agent_approved' => 'Agent Approved',
        'agent_rejected' => 'Agent Rejected',
        'refund_request' => 'Refund Request',
        'refund_approved' => 'Refund Approved',
        'refund_rejected' => 'Refund Rejected',
        default => ucfirst(str_replace('_', ' ', $type)),
    };
}

function notificationTypeBadgeClass($type) {
    return match($type) {
        'transfer_assigned' => 'bg-info',
        'transfer_completed' => 'bg-success',
        'transfer_rejected' => 'bg-danger',
        'agent_approved' => 'bg-success',
        'agent_rejected' => 'bg-danger',
        'refund_request' => 'bg-warning',
        'refund_approved' => 'bg-success',
        'refund_rejected' => 'bg-danger',
        default => 'bg-secondary',
    };
}
@endphp
