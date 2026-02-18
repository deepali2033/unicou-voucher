@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Job Applications</h2>
            <p class="text-muted mb-0">Review candidates who applied for job vacancies.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3">Candidate</th>
                            <th class="border-0 py-3">Job Title</th>
                            <th class="border-0 py-3">Country</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Applied At</th>
                            <th class="border-0 py-3 text-end px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold text-dark">{{ $app->name }}</div>
                                <div class="small text-muted">{{ $app->email }}</div>
                                <div class="small text-muted">{{ $app->phone }}</div>
                            </td>
                            <td>
                                <div class="fw-600">{{ $app->vacancy->title }}</div>
                                <span class="badge bg-light text-primary border">{{ ucfirst(str_replace('_', ' ', $app->vacancy->category)) }}</span>
                            </td>
                            <td>{{ $app->country }}</td>
                            <td>
                                <span class="badge bg-{{ $app->status == 'pending' ? 'warning' : ($app->status == 'selected' ? 'success' : 'danger') }}">
                                    {{ strtoupper($app->status) }}
                                </span>
                            </td>
                            <td>{{ $app->created_at->format('d M, Y') }}</td>
                            <td class="text-end px-4">
                                <button class="btn btn-sm btn-light rounded-pill" onclick="viewDetails({{ $app }})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                @if($app->status == 'pending')
                                <form action="{{ route('jobs.application.status', $app->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="selected">
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill" onclick="return confirm('Select this candidate?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('jobs.application.status', $app->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Reject this candidate?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('jobs.application.destroy', $app->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger rounded-pill" onclick="return confirm('Delete this application?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No applications found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Application Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4" id="modalContent">
                <!-- Filled via JS -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewDetails(app) {
    let storageUrl = "{{ asset('storage') }}";
    let content = `
        <div class="row">
            <div class="col-md-6 mb-4">
                <h6 class="fw-bold text-primary mb-3">Basic Information</h6>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Full Name</label>
                    <span class="fw-bold">${app.name}</span>
                </div>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Email</label>
                    <span>${app.email}</span>
                </div>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Phone</label>
                    <span>${app.phone}</span>
                </div>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">WhatsApp</label>
                    <span>${app.whatsapp_number}</span>
                </div>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">DOB</label>
                    <span>${new Date(app.dob).toLocaleDateString()}</span>
                </div>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Job applied for</label>
                    <span class="text-primary fw-bold">${app.vacancy.title}</span>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <h6 class="fw-bold text-primary mb-3">Address & Identity</h6>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Address</label>
                    <span>${app.address}, ${app.city}, ${app.state}, ${app.country} - ${app.post_code}</span>
                </div>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">ID Type & Number</label>
                    <span>${app.id_type} (${app.id_number})</span>
                </div>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Designation</label>
                    <span>${app.designation}</span>
                </div>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Social Link</label>
                    <a href="${app.social_link}" target="_blank">${app.social_link || 'N/A'}</a>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <h6 class="fw-bold text-primary mb-3">Bank Details</h6>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Bank Name</label>
                    <span>${app.bank_name} (${app.bank_country})</span>
                </div>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Account / IBAN</label>
                    <span>${app.bank_account_number}</span>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <h6 class="fw-bold text-primary mb-3">Reference Information</h6>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Reference</label>
                    <span>${app.reference_name} (${app.organization_name})</span>
                </div>
                <div class="mb-2">
                    <label class="small text-muted text-uppercase fw-bold d-block">Ref Contact</label>
                    <span>${app.reference_email} / ${app.reference_phone}</span>
                </div>
            </div>
            <div class="col-12 border-top pt-3 mt-2">
                <h6 class="fw-bold text-primary mb-3">Documents</h6>
                <div class="d-flex gap-3">
                    <a href="${storageUrl}/${app.id_document}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-id-card me-1"></i> View ID
                    </a>
                    <a href="${storageUrl}/${app.photograph}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-image me-1"></i> View Photo
                    </a>
                    <a href="${storageUrl}/${app.reference_letter}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-file-pdf me-1"></i> Ref Letter
                    </a>
                </div>
            </div>
        </div>
    `;
    $('#modalContent').html(content);
    $('#detailsModal').modal('show');
}
</script>
@endpush
@endsection
