@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold text-dark">System Control Center</h2>
            <p class="text-muted">Manage global settings and emergency system status.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <!-- System Settings -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Global Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.system.control.update') }}" method="POST">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Maintenance Mode</label>
                                <select name="settings[maintenance_mode]" class="form-select">
                                    <option value="off" {{ ($settings['maintenance_mode'] ?? 'off') == 'off' ? 'selected' : '' }}>Disabled (System Online)</option>
                                    <option value="on" {{ ($settings['maintenance_mode'] ?? 'off') == 'on' ? 'selected' : '' }}>Enabled (System Offline)</option>
                                </select>
                                <small class="text-muted">Prevents agents and students from accessing the dashboard.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Registration Status</label>
                                <select name="settings[registration_enabled]" class="form-select">
                                    <option value="on" {{ ($settings['registration_enabled'] ?? 'on') == 'on' ? 'selected' : '' }}>Open</option>
                                    <option value="off" {{ ($settings['registration_enabled'] ?? 'on') == 'off' ? 'selected' : '' }}>Closed</option>
                                </select>
                                <small class="text-muted">Enable or disable new user registrations.</small>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Minimum Wallet Top-up</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="settings[min_topup]" class="form-control" value="{{ $settings['min_topup'] ?? '50' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Daily Order Limit (per Agent)</label>
                                <input type="number" name="settings[daily_order_limit]" class="form-control" value="{{ $settings['daily_order_limit'] ?? '100' }}">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">System Announcement</label>
                                <textarea name="settings[announcement]" class="form-control" rows="3" placeholder="Global message shown to all users...">{{ $settings['announcement'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fas fa-save me-2"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recent Control Logs -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">System Audit Trail</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Admin</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->user->name ?? 'System' }}</td>
                                    <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                                    <td>{{ $log->description }}</td>
                                    <td><small>{{ $log->created_at ? $log->created_at->diffForHumans() : 'N/A' }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No recent system logs.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Actions -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-top border-danger border-4">
                <div class="card-body py-4">
                    <h5 class="fw-bold text-danger mb-3">Emergency Actions</h5>
                    <p class="small text-muted mb-4">These actions have immediate global impact. Use with caution.</p>
                    
                    <form action="{{ route('admin.system.toggle') }}" method="POST" class="d-grid gap-3">
                        @csrf
                        <input type="hidden" name="reason" value="Emergency System Halt">
                        <button type="submit" class="btn btn-outline-danger fw-bold py-3">
                            <i class="fas fa-power-off me-2"></i> HALT ALL TRANSACTIONS
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="d-grid gap-3">
                        <button class="btn btn-light border btn-sm text-start py-3 px-3">
                            <i class="fas fa-broom me-2 text-warning"></i> Clear System Cache
                        </button>
                        <button class="btn btn-light border btn-sm text-start py-3 px-3">
                            <i class="fas fa-database me-2 text-info"></i> Backup Database
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
