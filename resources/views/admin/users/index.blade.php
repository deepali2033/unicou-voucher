@extends('admin.layouts.app')

@section('title', 'User Management')
@section('page-title', 'Organizational Hierarchy Control')

@section('page-actions')
    <button class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create New User
    </button>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 koa-tb-card">
        <div class="card-header border-0 bg-light">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <ul class="nav nav-pills card-header-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">All</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Managers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Support Team</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Reseller Agents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Agents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Students</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" placeholder="Search users...">
                        <button class="btn btn-outline-secondary btn-sm"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0 koa-tb-cnt">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>User Info</th>
                            <th>Role / Type</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white p-2 me-3">JD</div>
                                    <div>
                                        <div class="fw-bold">John Doe</div>
                                        <div class="small text-muted">john@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-primary">Manager</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>2 hours ago</td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info" title="Login as User"><i class="fas fa-sign-in-alt"></i></button>
                                    <button class="btn btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-outline-warning" title="Freeze"><i class="fas fa-snowflake"></i></button>
                                    <button class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-info text-white p-2 me-3">SS</div>
                                    <div>
                                        <div class="fw-bold">Sarah Smith</div>
                                        <div class="small text-muted">sarah@support.com</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-info">Support Team</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>Just now</td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info" title="Login as User"><i class="fas fa-sign-in-alt"></i></button>
                                    <button class="btn btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-outline-warning" title="Freeze"><i class="fas fa-snowflake"></i></button>
                                    <button class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-purple text-white p-2 me-3">RA</div>
                                    <div>
                                        <div class="fw-bold">Global Resellers Ltd.</div>
                                        <div class="small text-muted">contact@globalresellers.com</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-purple">Reseller Agent</span></td>
                            <td><span class="badge bg-secondary">Frozen</span></td>
                            <td>3 days ago</td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info" title="Login as User"><i class="fas fa-sign-in-alt"></i></button>
                                    <button class="btn btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-outline-success" title="Unfreeze"><i class="fas fa-fire"></i></button>
                                    <button class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-warning text-dark p-2 me-3">ST</div>
                                    <div>
                                        <div class="fw-bold">Mike Johnson</div>
                                        <div class="small text-muted">mike@student.edu</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-warning text-dark">Student</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>1 day ago</td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info" title="Login as User"><i class="fas fa-sign-in-alt"></i></button>
                                    <button class="btn btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-outline-warning" title="Freeze"><i class="fas fa-snowflake"></i></button>
                                    <button class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
