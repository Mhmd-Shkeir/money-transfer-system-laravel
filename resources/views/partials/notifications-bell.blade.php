@php
$unreadCount = auth()->check()
    ? \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count()
    : 0;

$recentNotifications = auth()->check()
    ? \App\Models\Notification::where('user_id', auth()->id())
        ->orderByDesc('created_at')
        ->limit(5)
        ->get()
    : collect();
@endphp

<div class="dropdown me-3">
    <button class="btn btn-light btn-sm position-relative"
            id="notificationDropdown"
            data-bs-toggle="dropdown"
            type="button">

        <i class="fas fa-bell"></i>

        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end"
         style="min-width: 350px; max-height: 500px; overflow-y: auto;">

        <h6 class="dropdown-header">
            Notifications
            @if($unreadCount > 0)
                <span class="badge bg-danger ms-2">{{ $unreadCount }} Unread</span>
            @endif
        </h6>

        <div class="dropdown-divider"></div>

        @if($recentNotifications->count() > 0)
            @foreach($recentNotifications as $notif)

                @php
                    $role = auth()->user()->role ?? null;
                    $link = route('user.notifications');

                    switch($notif->type) {

                        case 'new_transfer_for_agent':
                            if ($role === 'agent') {
                                $link = route('agent.incoming-requests');
                            }
                            break;

                        case 'agent_approved':
                            if ($role === 'agent') {
                                $link = route('agent.profile');
                            }
                            break;

                        case 'new_transaction':
                            if ($role === 'admin') {
                                $link = route('admin.notifications');
                            }
                            break;

                        default:
                            $link = route('user.notifications');
                    }
                @endphp

                <a href="{{ $link }}"
                   class="dropdown-item {{ !$notif->is_read ? 'bg-light' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong class="d-block small">{{ $notif->title }}</strong>
                            <small class="text-muted">
                                {{ Str::limit($notif->message, 60) }}
                            </small>
                            <br>
                            <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                        </div>

                        @unless($notif->is_read)
                            <span class="badge bg-primary ms-2">New</span>
                        @endunless
                    </div>
                </a>
            @endforeach

            <div class="dropdown-divider"></div>
            <a href="{{ route('user.notifications') }}"
               class="dropdown-item text-center text-primary small">
                <i class="fas fa-eye me-1"></i> View All Notifications
            </a>

        @else
            <div class="dropdown-item text-center text-muted py-3">
                <small>No notifications</small>
            </div>
        @endif
    </div>
</div>
