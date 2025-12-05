@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold">المحادثة مع {{ $user->name }} 💬</h3>
        <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary">عودة</a>
    </div>

    <div class="card" style="height: 70vh;">
        <div class="card-body d-flex flex-column">
            <div class="flex-grow-1 overflow-auto mb-3" id="chat-messages">
                @foreach ($messages as $message)
                    <div class="d-flex mb-3 {{ $message->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}"
                        data-message-id="{{ $message->id }}">
                        <div class="card {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}"
                            style="max-width: 70%;">
                            <div class="card-body p-2">
                                <p class="mb-0">{{ $message->message }}</p>
                                <small class="{{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}"
                                    style="font-size: 0.75rem;">
                                    {{ $message->created_at->format('H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Typing indicator -->
            <div id="typing-indicator" class="mb-2" style="display: none;">
                <small class="text-muted">
                    <i class="fas fa-circle-notch fa-spin"></i> {{ $user->name }} يكتب...
                </small>
            </div>

            <form id="chat-form" class="d-flex gap-2">
                @csrf
                <input type="text" name="message" id="message-input" class="form-control"
                    placeholder="اكتب رسالتك هنا..." required autocomplete="off">
                <button type="submit" class="btn btn-primary" id="send-btn">
                    <i class="fas fa-paper-plane"></i> إرسال
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const userId = {{ $user->id }};
            const authUserId = {{ auth()->id() }};
            const chatMessages = document.getElementById('chat-messages');
            const messageInput = document.getElementById('message-input');
            const chatForm = document.getElementById('chat-form');
            const typingIndicator = document.getElementById('typing-indicator');
            const sendBtn = document.getElementById('send-btn');

            let lastMessageId = {{ $messages->last()->id ?? 0 }};
            let typingTimeout = null;
            let sendingMessage = false; // Prevent duplicate sends
            const messageIds = new Set(); // Track message IDs to prevent duplicates

            // Initialize message IDs from existing messages
            document.querySelectorAll('[data-message-id]').forEach(el => {
                const id = parseInt(el.getAttribute('data-message-id'));
                if (id) messageIds.add(id);
            });

            // Scroll to bottom of chat
            function scrollToBottom() {
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }

            // Add message to chat (with duplicate prevention)
            function addMessage(message, isSent = false) {
                // Prevent duplicate messages
                if (messageIds.has(message.id)) {
                    console.log('Message already exists, skipping:', message.id);
                    return;
                }

                messageIds.add(message.id);

                const messageDiv = document.createElement('div');
                messageDiv.className =
                    `d-flex mb-3 ${message.sender_id == authUserId ? 'justify-content-end' : 'justify-content-start'}`;
                messageDiv.setAttribute('data-message-id', message.id);

                const cardClass = message.sender_id == authUserId ? 'bg-primary text-white' : 'bg-light';
                const timeClass = message.sender_id == authUserId ? 'text-white-50' : 'text-muted';

                messageDiv.innerHTML = `
                    <div class="card ${cardClass}" style="max-width: 70%; ${isSent ? 'opacity: 0; transform: translateY(10px);' : ''}">
                        <div class="card-body p-2">
                            <p class="mb-0">${escapeHtml(message.message)}</p>
                            <small class="${timeClass}" style="font-size: 0.75rem;">
                                ${message.created_at}
                            </small>
                        </div>
                    </div>
                `;

                if (chatMessages) {
                    chatMessages.appendChild(messageDiv);

                    // Animate new message
                    if (isSent) {
                        setTimeout(() => {
                            const card = messageDiv.querySelector('.card');
                            if (card) {
                                card.style.transition = 'all 0.3s ease';
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }
                        }, 10);
                    }

                    scrollToBottom();
                    lastMessageId = message.id;
                }
            }

            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Send message via AJAX
            if (chatForm) {
                chatForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    // Prevent duplicate submissions
                    if (sendingMessage) {
                        console.log('Already sending a message, please wait...');
                        return;
                    }

                    const message = messageInput.value.trim();
                    if (!message) return;

                    // Set sending flag
                    sendingMessage = true;

                    // Disable form while sending
                    sendBtn.disabled = true;
                    messageInput.disabled = true;

                    try {
                        const response = await fetch(`{{ route('chat.send', $user->id) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: message
                            })
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();

                        if (data.success) {
                            // Add message to UI
                            addMessage(data.message, true);
                            messageInput.value = '';
                        } else {
                            throw new Error(data.message || 'Failed to send message');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        alert('حدث خطأ أثناء إرسال الرسالة. الرجاء المحاولة مرة أخرى.');
                    } finally {
                        // Always re-enable form
                        sendingMessage = false;
                        sendBtn.disabled = false;
                        messageInput.disabled = false;
                        messageInput.focus();
                    }
                });
            }

            // Typing indicator with debouncing
            let lastTypingTime = 0;
            const typingDebounceTime = 1000; // Send typing event max once per second

            if (messageInput) {
                messageInput.addEventListener('input', function() {
                    const now = Date.now();

                    // Debounce typing indicator
                    if (now - lastTypingTime > typingDebounceTime) {
                        lastTypingTime = now;

                        // Send typing event
                        fetch(`{{ route('chat.typing', $user->id) }}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        }).catch(err => console.error('Typing indicator error:', err));
                    }
                });
            }

            // Listen for new messages via Laravel Echo
            if (window.Echo) {
                const channelId = [authUserId, userId].sort((a, b) => a - b).join('-');

                window.Echo.private(`chat.${channelId}`)
                    .listen('MessageSent', (e) => {
                        console.log('New message received:', e);

                        // Only add if message is from the other user (not from current user)
                        if (e.sender_id != authUserId) {
                            addMessage({
                                id: e.id,
                                message: e.message,
                                sender_id: e.sender_id,
                                created_at: new Date(e.created_at).toLocaleTimeString('ar-EG', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })
                            });
                        }
                    })
                    .listen('.user.typing', (e) => {
                        console.log('User typing:', e);

                        // Show typing indicator if other user is typing
                        if (e.sender_id != authUserId && typingIndicator) {
                            typingIndicator.style.display = 'block';

                            // Hide after 2 seconds
                            clearTimeout(window.typingIndicatorTimeout);
                            window.typingIndicatorTimeout = setTimeout(() => {
                                typingIndicator.style.display = 'none';
                            }, 2000);
                        }
                    });
            }

            // Initial scroll
            scrollToBottom();

            // Focus on input
            if (messageInput) {
                messageInput.focus();
            }
        </script>
    @endpush
@endsection
