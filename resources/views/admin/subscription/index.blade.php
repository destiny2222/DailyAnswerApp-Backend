@extends('layouts.master-2')
@section('content')

<div class="card w-100 position-relative overflow-hidden">
        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
            <h4 class="card-title mb-0">Subscription Plans Management</h4>
            @can('create subscription', 'admin')
                <a href="{{ route('admin.subscription.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Create Subscription Plan
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
                                <h6 class="fs-4 fw-semibold mb-0">Plan Name</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Price</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Interval</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Plan ID</h6>
                            </th>
                            {{-- <th>
                                <h6 class="fs-4 fw-semibold mb-0">Features</h6>
                            </th> --}}
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Actions</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscriptions as $subscription)
                            <tr>
                                <td>
                                    <h6 class="fs-4 fw-semibold mb-0">{{ $subscription->name }}</h6>
                                </td>
                                <td>
                                    <p class="mb-0 fw-normal">${{ number_format($subscription->price, 2) }}</p>
                                </td>
                                <td>
                                    <span class="badge bg-info text-capitalize">{{ $subscription->interval }}</span>
                                </td>
                                <td>
                                    <code>{{ $subscription->plan_id }}</code>
                                </td>
                                {{-- <td>
                                    @if ($subscription->features)
                                        <ul class="mb-0 ps-3">
                                            @foreach (array_slice($subscription->features, 0, 3) as $feature)
                                                <li>{{ $feature }}</li>
                                            @endforeach
                                            @if (count($subscription->features) > 3)
                                                <li><small class="text-muted">+{{ count($subscription->features) - 3 }} more</small></li>
                                            @endif
                                        </ul>
                                    @else
                                        <span class="text-muted">No features</span>
                                    @endif
                                </td> --}}
                                <td>
                                    <div class="d-flex gap-2">
                                        @can('edit subscription', 'admin')
                                            <a href="{{ route('admin.subscription.edit', $subscription->id) }}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-edit"></i> Edit
                                            </a>
                                        @endcan
                                        @can('delete subscription', 'admin')
                                            <form action="{{ route('admin.subscription.delete', $subscription->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this subscription plan?')">
                                                    <i class="ti ti-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-muted mb-0">No subscription plans found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection