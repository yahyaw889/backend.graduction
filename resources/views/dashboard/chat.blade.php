@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Conversations</h1>
        <div class="page-subtitle">Patient–doctor messaging overview</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <h6 class="mb-0 fw-bold">All Conversations</h6>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($conversations as $conversation)
                        @php
                            $authId = auth()->id();
                            $otherId = $conversation->sender_id == $authId
                                ? $conversation->receiver_id
                                : $conversation->sender_id;
                            $otherUser = $conversation->sender_id == $authId
                                ? $conversation->receiver
                                : $conversation->sender;
                        @endphp
                        <a href="{{ route('chat.conversation', $otherId) }}"
                           class="list-group-item list-group-item-action border-0 border-bottom px-3 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar bg-primary bg-opacity-10 text-primary flex-shrink-0" style="font-size:.8rem;">
                                    {{ strtoupper(substr($otherUser->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-600" style="font-size:.875rem; font-weight:600;">{{ $otherUser->name ?? 'Unknown' }}</span>
                                        <span class="text-muted" style="font-size:.72rem;">{{ $conversation->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size:.8rem;">{{ $conversation->message }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted py-5" style="font-size:.875rem;">
                            <i class="fas fa-comments mb-2 d-block" style="font-size:2rem; opacity:.3;"></i>
                            No conversations yet
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card h-100 d-flex justify-content-center align-items-center" style="min-height:400px;">
            <div class="text-center text-muted p-5">
                <i class="fas fa-comments mb-3 d-block" style="font-size:3rem; opacity:.2;"></i>
                <h6 class="fw-bold mb-1" style="color:var(--text-main);">Select a conversation</h6>
                <div style="font-size:.85rem;">Choose a conversation from the list to view messages</div>
            </div>
        </div>
    </div>
</div>

@endsection
