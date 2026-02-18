@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Add Job Vacancy</h2>
            <p class="text-muted mb-0">Post a new job for candidates to apply.</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="btn btn-light rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <form action="{{ route('jobs.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase">Job Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Support Team Manchester" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-uppercase">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="manager">Manager</option>
                            <option value="support_team">Support Team</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-uppercase">Country</label>
                        <input type="text" name="country" class="form-control" placeholder="e.g. UK, Dubai">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase">Description</label>
                        <textarea name="description" class="form-control" rows="6" placeholder="Enter job requirements, roles, and responsibilities..." required></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <hr>
                        <button type="submit" class="btn btn-primary rounded-pill px-5">Post Job</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
