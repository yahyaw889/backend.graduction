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
                    <div
                        class="d-flex mb-3 {{ $message->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
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

            <form action="{{ route('chat.send', $user->id) }}" method="POST" class="d-flex gap-2">
                @csrf
                <input type="text" name="message" class="form-control" placeholder="اكتب رسالتك هنا..." required>
                <button type="submit" class="btn btn-primary">إرسال</button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Scroll to bottom of chat
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        </script>
    @endpush
@endsection
