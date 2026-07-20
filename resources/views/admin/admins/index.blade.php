@extends('layouts.master-2')
@section('content')
    <div class="card w-100 position-relative overflow-hidden">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Admin Users Management</h4>
            @can('create admins', 'admin')
                <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Create Admin
                </a>
            @endcan
        </div>
        <div class="card-body p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Admin</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Email</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Phone</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Role</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Actions</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($admins as $admin)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('uploads/admins/'.$admin->image) }}" 
                                             class="rounded-circle" width="40" height="40" 
                                             onerror="this.src='/assets/images/profile/user-1.jpg'">
                                        <div class="ms-3">
                                            <h6 class="fs-4 fw-semibold mb-0">{{ $admin->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0 fw-normal">{{ $admin->email }}</p>
                                </td>
                                <td>
                                    <p class="mb-0 fw-normal">{{ $admin->phone }}</p>
                                </td>
                                <td>
                                    @if ($admin->roles->count() > 0)
                                        @foreach ($admin->roles as $role)
                                            <span class="badge bg-primary text-capitalize">{{ $role->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary">No Role</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @can('view admins', 'admin')
                                            <a href="{{ route('admin.admins.show', $admin) }}" class="btn btn-sm btn-info">
                                                <i class="ti ti-eye"></i> View
                                            </a>
                                        @endcan
                                        @can('edit admins', 'admin')
                                            <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-edit"></i> Edit
                                            </a>
                                        @endcan
                                        @can('delete admins', 'admin')
                                            <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this admin?')">
                                                    <i class="ti ti-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="text-muted mb-0">No admin users found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $admins->links() }}
            </div>
        </div>
    </div>
@endsection
