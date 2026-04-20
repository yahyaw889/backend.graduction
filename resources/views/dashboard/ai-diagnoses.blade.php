@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">AI Diagnoses</h1>
        <div class="page-subtitle">View and manage AI skin disease diagnosis records</div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('ai-diagnoses.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label mb-1" style="font-size:.78rem; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search diagnosis or patient..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fas fa-filter me-1"></i> Filter</button>
                <a href="{{ route('ai-diagnoses.index') }}" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Diagnoses Table --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Image</th>
                        <th>Patient Details</th>
                        <th>Reported Symptoms</th>
                        <th>AI Diagnosis</th>
                        <th>Confidence</th>
                        <th>Date</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($diagnoses as $record)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ asset('storage/' . $record->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $record->image_path) }}" alt="Skin Image" class="rounded" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #ddd;">
                                </a>
                            </td>
                            <td>
                                <div class="fw-600" style="font-size:.875rem;">
                                    {{ $record->user ? $record->user->name : 'Guest' }}
                                </div>
                                <div class="text-muted" style="font-size:.75rem;">
                                    {{ $record->patient_age ? $record->patient_age . ' yrs' : 'Age N/A' }} | 
                                    {{ $record->patient_gender ?: 'Gender N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="text-muted text-truncate" style="max-width:200px; font-size:.855rem;">
                                    {{ $record->reported_symptoms ?: 'None' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.8rem;">
                                    {{ $record->diagnosis ?: 'Pending/Error' }}
                                </span>
                            </td>
                            <td>
                                @if($record->confidence_percentage)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar {{ $record->confidence_percentage > 80 ? 'bg-success' : ($record->confidence_percentage > 50 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ $record->confidence_percentage }}%"></div>
                                    </div>
                                    <span style="font-size:.75rem; font-weight:600;">{{ $record->confidence_percentage }}%</span>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="font-size:.855rem; color:var(--text-muted);">{{ $record->created_at->format('M d, Y') }}</td>
                            <td class="pe-4 text-end">
                                <button class="btn btn-sm btn-outline-info" 
                                    onclick="viewDetails('{{ addslashes($record->diagnosis) }}', '{{ addslashes(json_encode($record->symptoms_detected)) }}', '{{ addslashes($record->recommendation) }}')"
                                    style="border-radius:6px; font-size:.78rem; padding:.25rem .6rem;">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="deleteRecord({{ $record->id }})"
                                    style="border-radius:6px; font-size:.78rem; padding:.25rem .6rem;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-robot mb-2 d-block" style="font-size:2rem; opacity:.3;"></i>
                                No AI diagnoses found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($diagnoses->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $diagnoses->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- View Details Modal --}}
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow:hidden;">
            <div class="modal-header border-0 px-4 py-3" style="background: rgba(13, 110, 253, 0.04);">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar bg-info text-white shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-robot" style="font-size: .8rem;"></i></div>
                    <h5 class="modal-title fw-bold mb-0" style="font-size: 1.05rem;">AI Analysis Details</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <h6 class="fw-bold text-primary mb-2">Final Diagnosis</h6>
                <p id="detail_diagnosis" class="mb-4 text-dark bg-light p-2 rounded"></p>

                <h6 class="fw-bold text-secondary mb-2">Detected Symptoms (by AI)</h6>
                <ul id="detail_symptoms" class="mb-4 text-muted" style="font-size: 0.9rem;"></ul>

                <h6 class="fw-bold text-success mb-2">Recommendation</h6>
                <p id="detail_recommendation" class="mb-0 text-muted" style="font-size: 0.9rem;"></p>
            </div>
            <div class="modal-footer border-0 px-4 py-3 bg-light">
                <button type="button" class="btn btn-secondary px-4 shadow-sm" style="border-radius:10px;" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteRecordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="deleteRecordForm" method="POST">
            @csrf @method('DELETE')
            <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow:hidden;">
                <div class="modal-header border-0 px-4 py-3 bg-danger bg-opacity-10">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-danger text-white shadow-sm" style="width: 32px; height: 32px;"><i class="fas fa-exclamation-triangle" style="font-size: .8rem;"></i></div>
                        <h5 class="modal-title fw-bold text-danger mb-0" style="font-size: 1.05rem;">Delete Record</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 pt-4 text-center">
                    <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem; opacity:.2;"></i>
                    <p class="mb-0" style="font-size: 1rem;">Are you sure you want to delete this diagnosis record?</p>
                </div>
                <div class="modal-footer border-0 px-4 py-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4 shadow-sm" style="border-radius:10px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius:10px;">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function viewDetails(diagnosis, symptomsJson, recommendation) {
        document.getElementById('detail_diagnosis').textContent = diagnosis;
        document.getElementById('detail_recommendation').textContent = recommendation;
        
        let symptomsList = document.getElementById('detail_symptoms');
        symptomsList.innerHTML = '';
        
        try {
            let symptoms = JSON.parse(symptomsJson);
            if (Array.isArray(symptoms) && symptoms.length > 0) {
                symptoms.forEach(s => {
                    let li = document.createElement('li');
                    li.textContent = s;
                    symptomsList.appendChild(li);
                });
            } else {
                symptomsList.innerHTML = '<li>No specific symptoms identified.</li>';
            }
        } catch (e) {
            symptomsList.innerHTML = '<li>' + symptomsJson + '</li>';
        }
        
        new bootstrap.Modal(document.getElementById('viewDetailsModal')).show();
    }

    function deleteRecord(id) {
        document.getElementById('deleteRecordForm').action = '/ai-diagnoses/' + id;
        new bootstrap.Modal(document.getElementById('deleteRecordModal')).show();
    }
</script>
@endpush

@endsection
