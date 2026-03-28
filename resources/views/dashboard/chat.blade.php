@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 fw-bold">المحادثات الطبية 💬</h3>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">المحادثات</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach ($conversations as $conversation)
                        <a href="{{ route('chat.conversation', $conversation->sender_id == auth()->id() ? $conversation->receiver_id : $conversation->sender_id) }}"
                            class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    {{ $conversation->sender_id == auth()->id() ? $conversation->receiver->name : $conversation->sender->name }}
                                </h6>
                                <small class="text-muted">{{ $conversation->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-truncate">{{ $conversation->message }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card h-100 d-flex justify-content-center align-items-center p-5 text-muted">
                <h4>اختر محادثة للبدء</h4>
            </div>
        </div>
    </div>
@endsection
