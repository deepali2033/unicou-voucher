@extends('admin.layouts.app')

@section('title', 'Add Credit')
@section('page-title', 'Customer Satisfaction & Flexibility')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Manual Credit Adjustment</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Customer</label>
                            <select class="form-select select2">
                                <option value="">Search by Name, Email or ID...</option>
                                <option>John Doe (john@example.com) - #USR-1001</option>
                                <option>Sarah Smith (sarah@support.com) - #USR-2005</option>
                                <option>Agent Zero (agent0@business.com) - #AGT-5001</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Credit Amount (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" placeholder="0.00" min="0" max="300" step="0.01">
                            </div>
                            <div class="form-text text-danger">
                                <i class="fas fa-info-circle me-1"></i> Maximum limit: <strong>USD 300.00</strong>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Reason / Reference</label>
                            <select class="form-select mb-2">
                                <option value="">Select Reason...</option>
                                <option value="promotion">Promotional Credit</option>
                                <option value="compensation">Dispute Compensation</option>
                                <option value="correction">Balance Correction</option>
                                <option value="bonus">Referral Bonus</option>
                                <option value="other">Other (Specify below)</option>
                            </select>
                            <textarea class="form-control" rows="3" placeholder="Additional notes for the transaction log..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <div class="d-flex">
                                <i class="fas fa-shield-alt me-3 mt-1 fs-5"></i>
                                <div>
                                    <strong>Security Notice:</strong>
                                    <p class="mb-0 small">This action will be recorded in the audit logs. Frequent large manual credits may be flagged for review.</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-primary btn-lg">Add Credit Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
