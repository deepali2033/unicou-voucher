@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        <!-- Chat Area -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; min-height: 600px;">
                <!-- Header -->
                <div class="card-header bg-white border-bottom p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('disputes.index') }}" class="btn btn-light btn-sm rounded-circle me-3">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('images/user.png') }}" class="rounded-circle me-2" width="40" height="40">
                                <div>
                                    <h6 class="fw-bold mb-0" id="other-user-name">Loading...</h6>
                                    <small id="other-user-status" class="text-muted"><i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Offline</small>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-primary border px-3 py-2">#{{ $dispute->dispute_id }}</span>
                        </div>
                    </div>
                </div>

                <!-- Messages Body -->
                <div class="card-body p-4" id="chat-messages" style="height: 450px; overflow-y: auto; background-color: #fcfcfc;">
                    @foreach($dispute->messages as $message)
                        @include('dashboard.disputes.partials.message', ['message' => $message])
                    @endforeach
                    <div id="typing-indicator" class="d-none mb-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-light p-2 px-3 rounded-pill">
                                <span class="small text-muted italic" id="typing-text">typing...</span>
                                <span class="typing-dots"><span>.</span><span>.</span><span>.</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer / Input -->
                <div class="card-footer bg-white border-top p-3">
                    <form id="chat-form" action="{{ route('disputes.send', $dispute->id) }}" method="POST">
                        @csrf
                        <div class="input-group gap-2">
                            <input type="text" id="message-input" name="message" class="form-control border-0 bg-light p-3" 
                                   placeholder="Type your message..." style="border-radius: 0.8rem;" autocomplete="off">
                            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" 
                                    style="width: 50px; border-radius: 0.8rem;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-3 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-wand-magic-sparkles text-primary me-2"></i>
                            <span class="small fw-semibold">AI Response Helper</span>
                        </div>
                        <button class="btn btn-sm text-primary p-0 fw-bold" style="font-size: 0.8rem;">Get Suggestion</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 1rem;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Dispute Details</h5>
                    
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Status</label>
                        @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isSupport())
                        <form action="{{ route('disputes.status', $dispute->id) }}" method="POST">
                            @csrf
                            <select name="status" class="form-select border-0 bg-light p-3" onchange="this.form.submit()" style="border-radius: 0.8rem;">
                                <option value="open" {{ $dispute->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="pending" {{ $dispute->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="resolved" {{ $dispute->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $dispute->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </form>
                        @else
                        <div class="p-3 bg-light rounded-3 fw-bold text-uppercase">
                            {{ $dispute->status }}
                        </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Subject</label>
                        <p class="fw-bold mb-0">{{ $dispute->subject }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Description</label>
                        <p class="text-muted small mb-0">{{ $dispute->description }}</p>
                    </div>

                    <div class="mb-0">
                        <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Opened On</label>
                        <p class="mb-0">{{ $dispute->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                <div class="card-body p-4 text-center">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h6 class="fw-bold">Need Help?</h6>
                    <p class="text-muted small mb-0">Our support team usually responds within 2-4 hours.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const chatMessages = $('#chat-messages');
        const chatForm = $('#chat-form');
        const messageInput = $('#message-input');
        let lastMessageId = $('.message-item').last().data('id') || 0;

        // Scroll to bottom
        chatMessages.scrollTop(chatMessages[0].scrollHeight);

        // Send Message
        chatForm.on('submit', function(e) {
            e.preventDefault();
            const message = messageInput.val().trim();
            if (!message) return;

            const submitBtn = chatForm.find('button[type="submit"]');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: chatForm.attr('action'),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    message: message
                },
                success: function(response) {
                    if (response.success) {
                        // Only append if it doesn't exist yet (might have been fetched by polling)
                        if ($(`.message-item[data-id="${response.message_id}"]`).length === 0) {
                            chatMessages.append(response.message_html);
                            chatMessages.animate({ scrollTop: chatMessages[0].scrollHeight }, 300);
                        }
                        
                        messageInput.val('');
                        if (response.message_id > lastMessageId) {
                            lastMessageId = response.message_id;
                        }
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                }
            });
        });

        // Polling for new messages
        let isPolling = false;
        setInterval(function() {
            if (isPolling) return;
            isPolling = true;

            $.ajax({
                url: '{{ route("disputes.fetch", $dispute->id) }}',
                method: 'GET',
                data: { last_message_id: lastMessageId },
                success: function(response) {
                    // Update header
                    $('#other-user-name').text(response.other_user_name);
                    const statusEl = $('#other-user-status');
                    if (response.is_online) {
                        statusEl.removeClass('text-muted').addClass('text-success').html('<i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Online');
                    } else {
                        statusEl.removeClass('text-success').addClass('text-muted').html('<i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Offline');
                    }

                    // Update typing indicator
                    const typingIndicator = $('#typing-indicator');
                    if (response.is_typing) {
                        $('#typing-text').text(response.other_user_name + ' is typing');
                        typingIndicator.removeClass('d-none');
                        // Move indicator to bottom if new messages arrived
                        chatMessages.append(typingIndicator);
                    } else {
                        typingIndicator.addClass('d-none');
                    }

                    if (response.messages_html) {
                        // The controller returns a string of HTML. 
                        // We wrap it to parse individual message items and check for duplicates
                        const tempDiv = $('<div>').append(response.messages_html);
                        const newItems = tempDiv.find('.message-item');
                        
                        let appended = false;
                        newItems.each(function() {
                            const id = $(this).data('id');
                            if ($(`.message-item[data-id="${id}"]`).length === 0) {
                                // Insert before typing indicator
                                if (typingIndicator.is(':visible')) {
                                    $(this).insertBefore(typingIndicator);
                                } else {
                                    chatMessages.append($(this));
                                }
                                appended = true;
                            }
                        });

                        if (appended) {
                            chatMessages.animate({ scrollTop: chatMessages[0].scrollHeight }, 300);
                        }
                        
                        if (response.last_message_id > lastMessageId) {
                            lastMessageId = response.last_message_id;
                        }
                    }
                },
                complete: function() {
                    isPolling = false;
                }
            });
        }, 3000); // Poll every 3 seconds

        // Handle my typing
        let typingTimer;
        messageInput.on('keyup', function() {
            clearTimeout(typingTimer);
            if (messageInput.val()) {
                // Send typing status to server
                $.post('{{ route("disputes.typing", $dispute->id) }}', { _token: '{{ csrf_token() }}' });
                
                typingTimer = setTimeout(function() {
                    // Stop typing after 3s of inactivity
                }, 3000);
            }
        });
    });
</script>
@endpush
<style>
    .typing-dots span {
        animation: typing 1.4s infinite;
        font-weight: bold;
    }
    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
    
    @keyframes typing {
        0%, 100% { opacity: 0.2; }
        50% { opacity: 1; }
    }
</style>
@endsection
