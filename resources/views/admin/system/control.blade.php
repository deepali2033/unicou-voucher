@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">System Control (Emergency)</h4>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 {{ $status === 'running' ? 'border-start border-success border-4' : 'border-start border-danger border-4' }}">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="fw-bold mb-1">Current System Status: 
                                <span class="text-{{ $status === 'running' ? 'success' : 'danger' }} text-uppercase">{{ $status }}</span>
                            </h5>
                            <p class="text-muted mb-0">Use the toggle on the right to instantly STOP or RESUME the entire voucher system. This will affect all agents and students.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <form action="{{ route('admin.system.toggle') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Reason for action</label>
                                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="e.g. Server Maintenance" required>
                                </div>
                                <button type="submit" class="btn {{ $status === 'running' ? 'btn-danger' : 'btn-success' }} btn-lg w-100 fw-bold">
                                    @if($status === 'running')
                                        <i class="fas fa-stop-circle me-2"></i> STOP ALL VOUCHERS
                                    @else
                                        <i class="fas fa-play-circle me-2"></i> RESUME SYSTEM
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">System Status Log</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Action</th>
                            <th>Reason</th>
                            <th>Date & Time</th>
                            <th>Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>
                                <span class="badge {{ $log['action'] === 'Stopped' ? 'bg-danger' : 'bg-success' }}">
                                    {{ $log['action'] }}
                                </span>
                            </td>
                            <td>{{ $log['reason'] }}</td>
                            <td>{{ $log['date']->format('d M Y, h:i A') }}</td>
                            <td>Administrator</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
