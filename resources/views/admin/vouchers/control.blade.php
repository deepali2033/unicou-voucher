@extends('admin.layouts.app')

@section('title', 'Voucher System Control')
@section('page-title', 'Risk Control & Emergency Handling')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Emergency System Override</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Use these controls to immediately pause or disable core system functions. This should only be used during maintenance, detected fraud, or major policy changes.</p>
                    
                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="mb-0">Voucher Creation</h6>
                            <small class="text-muted">Disable the ability for users to generate new vouchers.</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input h4" type="checkbox" id="pauseCreation" checked>
                            <label class="form-check-label" for="pauseCreation"></label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="mb-0">Voucher Redemption</h6>
                            <small class="text-muted">Disable the ability for users to redeem existing vouchers.</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input h4" type="checkbox" id="pauseRedemption" checked>
                            <label class="form-check-label" for="pauseRedemption"></label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="mb-0">Agent API Access</h6>
                            <small class="text-muted">Temporarily block all external API requests from agents and resellers.</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input h4" type="checkbox" id="blockAPI">
                            <label class="form-check-label" for="blockAPI"></label>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> All actions are logged with the administrator's ID and timestamp for audit purposes.
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-end">
                    <button class="btn btn-danger">Save Emergency Settings</button>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Maintenance Schedule</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Scheduled Maintenance Time</label>
                            <div class="row">
                                <div class="col">
                                    <input type="datetime-local" class="form-control">
                                </div>
                                <div class="col">
                                    <input type="datetime-local" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maintenance Message for Users</label>
                            <textarea class="form-control" rows="3" placeholder="We are currently undergoing scheduled maintenance. Please check back later."></textarea>
                        </div>
                        <button type="button" class="btn btn-primary">Schedule Maintenance</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
