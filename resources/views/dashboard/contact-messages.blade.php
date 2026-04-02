@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('dashboard.contact_messages') }}</h1>
        <div class="page-subtitle">View and respond to messages submitted via the website contact form</div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('contact-messages.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-8">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Search Messages</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email, or message content…" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fas fa-filter me-1"></i> Search</button>
                <a href="{{ route('contact-messages.index') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    @forelse ($messages as $msg)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border-0 position-relative" style="border-radius:12px; transition:transform 0.2sease, box-shadow 0.2sease;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex gap-3">
                            <div class="avatar bg-primary bg-opacity-10 text-primary" style="width:40px;height:40px;font-size:1.1rem; border-radius:50%;">
                                {{ strtoupper(mb_substr($msg->name ?? 'A', 0, 1, 'UTF-8')) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold" style="font-size:1rem;">{{ $msg->name }}</h6>
                                <a href="mailto:{{ $msg->email }}" class="text-primary text-decoration-none" style="font-size:.8rem;"><i class="fas fa-envelope me-1"></i>{{ $msg->email }}</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-3 bg-light rounded-3 mt-3 mb-3 border">
                        <p class="mb-0 text-muted" style="font-size:.85rem; line-height:1.6; word-break:break-word;">
                            "{{ $msg->message }}"
                        </p>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-auto">
                        <span class="text-muted" style="font-size:.75rem;"><i class="fas fa-clock me-1"></i>{{ $msg->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-envelope-open-text mb-3 d-block text-muted" style="font-size:3rem; opacity:.2;"></i>
            <h5 class="text-muted mb-0">No contact messages found</h5>
        </div>
    @endforelse
</div>

@if ($messages->hasPages())
    <div class="mt-4">
        {{ $messages->withQueryString()->links() }}
    </div>
@endif

@endsection
