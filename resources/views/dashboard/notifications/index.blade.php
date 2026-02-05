@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Notifications</h4>
        <button class="btn btn-sm btn-outline-secondary">Mark all as read</button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                <div class="list-group-item p-4 {{ $notification->unread() ? 'bg-light' : '' }}">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="d-flex gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas {{ ($notification->data['type'] ?? '') == 'user_created' ? 'fa-user-plus' : 'fa-bell' }}"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">{{ ($notification->data['type'] ?? '') == 'user_created' ? 'New User Created' : 'Notification' }}</h6>
                                <p class="mb-1 text-muted">{{ $notification->data['message'] ?? 'New notification received' }}</p>
                                <small class="text-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-5 text-center">
                    <div class="text-muted">
                        <i class="fas fa-bell-slash fa-3x mb-3"></i>
                        <p>No notifications found.</p>
                    </div>
                </div>
                @endforelse
            </div>
            <div class="p-3">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection