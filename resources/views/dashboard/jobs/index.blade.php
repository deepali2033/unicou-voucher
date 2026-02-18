@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Job Vacancies</h2>
            <p class="text-muted mb-0">Manage your job postings for Manager and Support Team.</p>
        </div>
        <a href="{{ route('jobs.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i> Add Job Vacancy
        </a>
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
                            <th class="border-0 px-4 py-3">Title</th>
                            <th class="border-0 py-3">Category</th>
                            <th class="border-0 py-3">Country</th>
                            <th class="border-0 py-3">Status</th>
                            <th class="border-0 py-3">Posted At</th>
                            <th class="border-0 py-3 text-end px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vacancies as $vacancy)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold text-dark">{{ $vacancy->title }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $vacancy->category == 'manager' ? 'primary' : 'info' }}-subtle text-{{ $vacancy->category == 'manager' ? 'primary' : 'info' }}">
                                    {{ ucfirst(str_replace('_', ' ', $vacancy->category)) }}
                                </span>
                            </td>
                            <td>{{ $vacancy->country ?? 'Global' }}</td>
                            <td>
                                <span class="badge bg-{{ $vacancy->status == 'open' ? 'success' : 'danger' }}">
                                    {{ strtoupper($vacancy->status) }}
                                </span>
                            </td>
                            <td>{{ $vacancy->created_at->format('d M, Y') }}</td>
                            <td class="text-end px-4">
                                <form action="{{ route('jobs.destroy', $vacancy->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger rounded-pill" onclick="return confirm('Are you sure you want to delete this vacancy?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No job vacancies found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
