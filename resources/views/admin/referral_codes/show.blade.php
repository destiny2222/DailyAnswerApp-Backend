@extends('layouts.master-2')
@section('content')
    <div class="card w-100 position-relative overflow-hidden">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Referral Code Details: {{ $referralCode->code }}</h4>
            <a href="{{ route('admin.referral-codes.index') }}" class="btn btn-outline-secondary">Back to List</a>
        </div>
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-3">
                    <strong>Code:</strong>
                    <p>{{ $referralCode->code }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Max Uses:</strong>
                    <p>{{ $referralCode->max_uses ?? 'Unlimited' }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Current Uses:</strong>
                    <p>{{ $referralCode->uses_count }}</p>
                </div>
                <div class="col-md-3">
                    <strong>Expires At:</strong>
                    <p>{{ $referralCode->expires_at ? $referralCode->expires_at->format('M d, Y H:i') : 'Never' }}</p>
                </div>
            </div>

            <h5 class="mb-3">Users Registered with this Code</h5>
            <div class="table-responsive border rounded-1">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($referralCode->users->count() > 0)
                            @foreach ($referralCode->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('profile/'.$user->profile_photo_url) }}" class="rounded-circle" width="40" height="40">
                                            <div class="ms-3">
                                                <h6 class="fs-4 fw-semibold mb-0">{{ $user->name }}</h6>
                                                <span class="fw-normal">{{ $user->username }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.customer.edit', $user->id) }}" class="btn btn-sm btn-primary">View User</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center">No users have registered using this code yet.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
