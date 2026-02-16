@php
    $isMe = $message->user_id === auth()->id();
@endphp

<div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} mb-4 message-item" data-id="{{ $message->id }}">
    <div class="message-wrapper" style="max-width: 75%;">
        @if(!$isMe)
        <div class="small text-muted mb-1 ms-1">{{ $message->user->name }}</div>
        @endif
        
        <div class="message-box p-3 {{ $isMe ? 'bg-primary text-white' : 'bg-light text-dark' }}" 
             style="border-radius: 1.2rem; {{ $isMe ? 'border-bottom-right-radius: 0.2rem;' : 'border-bottom-left-radius: 0.2rem;' }}">
            <div class="message-content">
                {{ $message->message }}
            </div>
        </div>
        
        <div class="d-flex align-items-center {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} mt-1 px-1">
            <span class="text-muted small" style="font-size: 0.7rem;">{{ $message->created_at->format('h:i A') }}</span>
            @if($isMe)
            <i class="fas fa-check-double ms-1 {{ $message->is_read ? 'text-primary' : 'text-muted' }}" style="font-size: 0.7rem;"></i>
            @endif
        </div>
    </div>
</div>
