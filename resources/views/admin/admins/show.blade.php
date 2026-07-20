@extends('layouts.master-2')
@section('content')
    <div class="card w-100 position-relative overflow-hidden">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Admin User Details</h4>
            <div class="d-flex gap-2">
                @can('edit admins', 'admin')
                    <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-primary">
                        <i class="ti ti-edit me-1"></i> Edit Admin
                    </a>
                @endcan
                <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    <img src="{{ asset('uploads/admins/'. $admin->image) }}" 
                         class="rounded-circle mb-3" width="150" height="150"
                         onerror="this.src='/assets/images/profile/user-1.jpg'">
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Full Name</label>
                            <p class="fs-4 fw-semibold">{{ $admin->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Email Address</label>
                            <p class="fs-4">{{ $admin->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Phone Number</label>
                            <p class="fs-4">{{ $admin->phone }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Account Created</label>
                            <p class="fs-4">{{ $admin->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="mt-4">
                <h5 class="fw-semibold mb-3">Assigned Roles</h5>
                @if ($admin->roles->count() > 0)
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach ($admin->roles as $role)
                            <span class="badge bg-primary fs-3 text-capitalize">{{ $role->name }}</span>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        No roles assigned to this admin user.
                    </div>
                @endif
            </div>

            <div class="mt-4">
                <h5 class="fw-semibold mb-3">Permissions ({{ $admin->getAllPermissions()->count() }})</h5>
                @if ($admin->getAllPermissions()->count() > 0)
                    <div class="row">
                        @foreach ($admin->getAllPermissions() as $permission)
                            <div class="col-md-6 col-lg-4 mb-2">
                                <div class="d-flex align-items-center p-2 border rounded">
                                    <i class="ti ti-check text-success me-2"></i>
                                    <span>{{ $permission->name }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        No permissions assigned to this admin user.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
