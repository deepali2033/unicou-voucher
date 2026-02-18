@extends('layouts.auth')

@section('title', 'Careers - UniCou')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Join Our Team</h1>
        <p class="text-muted">Explore open positions and start your career with UniCou.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @forelse($vacancies as $vacancy)
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h3 class="fw-bold mb-1">{{ $vacancy->title }}</h3>
                        <div class="mb-3">
                            <span class="badge bg-primary-subtle text-primary">{{ ucfirst(str_replace('_', ' ', $vacancy->category)) }}</span>
                            <span class="ms-2 text-muted small"><i class="fas fa-map-marker-alt me-1"></i> {{ $vacancy->country ?? 'Global' }}</span>
                        </div>
                        <p class="text-muted mb-4">{{ Str::limit($vacancy->description, 200) }}</p>
                    </div>
                    <a href="{{ route('careers.apply', $vacancy->id) }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                        Apply Now <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="mb-4"><i class="fas fa-search fa-3x text-light"></i></div>
            <h4>No job openings at the moment.</h4>
            <p class="text-muted">Check back later or follow us for updates.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
