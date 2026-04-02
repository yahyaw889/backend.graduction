@extends('layouts.app')

@section('content')

<div class="page-header mb-3">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('chat.index') }}" class="icon-btn" title="Back to conversations">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="d-flex align-items-center gap-2">
            <div class="avatar bg-primary bg-opacity-10 text-primary">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="fw-bold" style="font-size:.95rem;">{{ $user->name }}</div>
                <div class="text-muted" style="font-size:.75rem;">{{ ucfirst($user->type ?? 'User') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="height: calc(100vh - 240px); min-height: 500px;">
    <div class="card-body d-flex flex-column p-0">
        {{-- Messages --}}
        <div class="flex-grow-1 overflow-auto p-4" id="chat-messages" style="scroll-behavior:smooth;">
            @foreach ($messages as $message)
                <div class="d-flex mb-3 {{ $message->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}"
                     data-message-id="{{ $message->id }}">
                    <div class="px-3 py-2 rounded-3 {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : '' }}"
                         style="max-width:70%; {{ $message->sender_id != auth()->id() ? 'background:var(--body-bg); border:1px solid var(--border-color);' : '' }}">
                        <div style="font-size:.875rem;">{{ $message->message }}</div>
                        <div class="{{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}" style="font-size:.7rem; margin-top:.2rem;">
                            {{ $message->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Typing indicator --}}
            <div id="typing-indicator" class="mb-2 d-none">
                <div class="d-flex gap-2 align-items-center">
                    <div class="avatar bg-primary bg-opacity-10 text-primary" style="width:28px;height:28px;font-size:.65rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="px-3 py-2 rounded-3" style="background:var(--body-bg); border:1px solid var(--border-color);">
                        <span class="text-muted" style="font-size:.78rem;"><i class="fas fa-ellipsis-h fa-beat"></i> {{ $user->name }} is typing…</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Bar --}}
        <div class="border-top p-3">
            <form id="chat-form" class="d-flex gap-2">
                @csrf
                <input type="text" name="message" id="message-input"
                       class="form-control flex-grow-1"
                       placeholder="Type a message…"
                       required autocomplete="off">
                <button type="submit" class="btn btn-primary px-4" id="send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const userId        = {{ $user->id }};
    const authUserId    = {{ auth()->id() }};
    const chatMessages  = document.getElementById('chat-messages');
    const messageInput  = document.getElementById('message-input');
    const chatForm      = document.getElementById('chat-form');
    const typingIndic   = document.getElementById('typing-indicator');
    const sendBtn       = document.getElementById('send-btn');

    let lastMessageId  = {{ $messages->last()->id ?? 0 }};
    let sendingMessage = false;
    const messageIds   = new Set();

    // Seed existing message IDs
    document.querySelectorAll('[data-message-id]').forEach(el => {
        const id = parseInt(el.getAttribute('data-message-id'));
        if (id) messageIds.add(id);
    });

    function scrollToBottom() {
        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    function addMessage(message, animate = false) {
        if (messageIds.has(message.id)) return;
        messageIds.add(message.id);
        lastMessageId = Math.max(lastMessageId, message.id);

        const isMine = message.sender_id == authUserId;
        const wrap   = document.createElement('div');
        wrap.className = `d-flex mb-3 ${isMine ? 'justify-content-end' : 'justify-content-start'}`;
        wrap.setAttribute('data-message-id', message.id);

        const bubble = document.createElement('div');
        bubble.className = `px-3 py-2 rounded-3`;
        bubble.style.maxWidth = '70%';
        if (isMine) {
            bubble.classList.add('bg-primary', 'text-white');
        } else {
            bubble.style.background    = 'var(--body-bg)';
            bubble.style.border        = '1px solid var(--border-color)';
        }
        if (animate) {
            bubble.style.opacity   = '0';
            bubble.style.transform = 'translateY(8px)';
            bubble.style.transition = 'all 0.25s ease';
        }

        bubble.innerHTML = `
            <div style="font-size:.875rem;">${escapeHtml(message.message)}</div>
            <div class="${isMine ? 'text-white-50' : 'text-muted'}" style="font-size:.7rem;margin-top:.2rem;">${message.created_at}</div>
        `;

        wrap.appendChild(bubble);

        // Insert before typing indicator
        chatMessages.insertBefore(wrap, typingIndic);

        if (animate) {
            requestAnimationFrame(() => {
                bubble.style.opacity = '1';
                bubble.style.transform = 'translateY(0)';
            });
        }

        scrollToBottom();
    }

    // Send message
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (sendingMessage) return;

        const text = messageInput.value.trim();
        if (!text) return;

        sendingMessage = true;
        sendBtn.disabled = true;
        messageInput.disabled = true;

        try {
            const res  = await fetch('{{ route('chat.send', $user->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: text }),
            });

            const data = await res.json();

            if (data.success) {
                addMessage(data.message, true);
                messageInput.value = '';
            } else {
                throw new Error(data.message || 'Failed to send');
            }
        } catch (err) {
            console.error(err);
            alert('Failed to send message. Please try again.');
        } finally {
            sendingMessage = false;
            sendBtn.disabled = false;
            messageInput.disabled = false;
            messageInput.focus();
        }
    });

    // Typing indicator
    let lastTypingTime = 0;
    messageInput.addEventListener('input', () => {
        const now = Date.now();
        if (now - lastTypingTime > 1000) {
            lastTypingTime = now;
            fetch('{{ route('chat.typing', $user->id) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            }).catch(console.error);
        }
    });

    // Laravel Echo (Pusher)
    if (window.Echo) {
        const channelId = [authUserId, userId].sort((a, b) => a - b).join('-');
        window.Echo.private(`chat.${channelId}`)
            .listen('MessageSent', (e) => {
                if (e.sender_id != authUserId) {
                    addMessage({
                        id: e.id,
                        message: e.message,
                        sender_id: e.sender_id,
                        created_at: new Date(e.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
                    });
                }
            })
            .listen('.user.typing', (e) => {
                if (e.sender_id != authUserId) {
                    typingIndic.classList.remove('d-none');
                    clearTimeout(window._typingTimeout);
                    window._typingTimeout = setTimeout(() => typingIndic.classList.add('d-none'), 2000);
                }
            });
    }

    scrollToBottom();
    messageInput.focus();
</script>
@endpush

@endsection
