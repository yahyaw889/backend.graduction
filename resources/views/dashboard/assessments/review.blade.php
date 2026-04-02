@extends('layouts.app')

@section('content')

<div class="page-header mb-3">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('assessments.index') }}" class="icon-btn" title="Back to assessments">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="d-flex align-items-center gap-2">
            <div class="avatar bg-primary bg-opacity-10 text-primary">
                {{ strtoupper(substr($assessment->user->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <div class="fw-bold" style="font-size:1.1rem; line-height:1.2;">{{ $assessment->user->name ?? 'Unknown Patient' }}</div>
                <div class="text-muted" style="font-size:.8rem;">Assessment #{{ $assessment->id }} • {{ $assessment->created_at->format('M d, Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        {{-- Image Details --}}
        <div class="card h-100 shadow-sm border-0" style="border-radius:16px; overflow:hidden;">
            @if($assessment->image_path)
                <img src="{{ asset('storage/' . $assessment->image_path) }}" class="card-img-top" alt="Uploaded Image" style="object-fit:cover; height:280px; width:100%; border-bottom:1px solid var(--border-color);">
            @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:280px; border-bottom:1px solid var(--border-color); color:var(--text-muted);">
                    <i class="fas fa-image fa-3x opacity-25"></i>
                </div>
            @endif
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="color:var(--text-main); font-size:.9rem; text-transform:uppercase; letter-spacing:.5px;">AI Prediction</h6>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted" style="font-size:.85rem;">Condition:</span>
                    <span class="fw-bold text-primary" style="font-size:.9rem;">{{ $assessment->prediction ?? 'Analyzing...' }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted" style="font-size:.85rem;">Confidence:</span>
                    <span class="fw-bold" style="font-size:.9rem;">{{ $assessment->confidence ? number_format($assessment->confidence, 2) . '%' : 'N/A' }}</span>
                </div>

                @php
                    $riskColor = match(strtolower($assessment->risk_level ?? '')) {
                        'high'   => 'danger',
                        'medium' => 'warning',
                        'low'    => 'success',
                        default  => 'secondary'
                    };
                @endphp
                <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                    <span class="text-muted" style="font-size:.85rem;">Risk Level:</span>
                    <span class="badge bg-{{ $riskColor }} bg-opacity-10 text-{{ $riskColor }} fw-bold" style="font-size:.75rem; padding:.35em .6em;">
                        {{ ucfirst($assessment->risk_level ?? 'Unknown') }}
                    </span>
                </div>

                @if($assessment->symptoms && count($assessment->symptoms) > 0)
                    <div class="mt-3">
                        <span class="text-muted d-block mb-2" style="font-size:.85rem;">Reported Symptoms:</span>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($assessment->symptoms as $symptom)
                                <span class="badge bg-light text-dark border" style="font-size:.7rem; font-weight:500;">{{ $symptom->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        {{-- Doctor Recommendation Form --}}
        <div class="card shadow-sm border-0 h-100" style="border-radius:16px;">
            <div class="card-header border-0 bg-transparent px-4 py-4 d-flex align-items-center gap-2">
                <div class="avatar bg-success bg-opacity-10 text-success rounded-circle" style="width:36px;height:36px;font-size:.9rem;"><i class="fas fa-user-md"></i></div>
                <h5 class="mb-0 fw-bold" style="font-size:1.1rem;">Medical Recommendation</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('assessments.review', $assessment->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600; font-size:.85rem; color:var(--text-muted);">Doctor's Notes & Advice</label>
                        <textarea name="recommendation" class="form-control" rows="8" placeholder="Type your diagnosis, recommendations, and prescribed treatments here." style="font-size:.95rem; border-radius:12px; resize:none;" required>{{ old('recommendation', $assessment->recommendation) }}</textarea>
                        <small class="text-muted mt-2 d-block" style="font-size:.75rem;">This recommendation will be visible to the patient.</small>
                    </div>

                    <div class="row align-items-center bg-light rounded-3 p-3 mx-0 mb-4 border">
                        <div class="col-md-7">
                            <div class="fw-bold" style="font-size:.9rem;">Mark as Completed</div>
                            <div class="text-muted" style="font-size:.75rem;">This will notify the patient and change the status from pending.</div>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <select name="status" class="form-select fw-bold shadow-sm" style="border-radius:8px;">
                                <option value="pending" {{ $assessment->status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                                <option value="completed" {{ $assessment->status === 'completed' ? 'selected' : '' }}>Completed (Notify Patient)</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm" style="border-radius:10px; font-weight:600;">
                            <i class="fas fa-paper-plane me-2"></i> Submit Recommendation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
