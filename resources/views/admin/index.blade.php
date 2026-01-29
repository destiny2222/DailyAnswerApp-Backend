@extends('layouts.master-2')
@section('content')
    <div class="container-fluid">
        <!--  Owl carousel -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 zoom-in bg-primary-subtle shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="/assets/images/svgs/icon-user-male.svg" width="50" height="50" class="mb-3"
                                alt="modernize-img">
                            <p class="fw-semibold fs-3 text-primary mb-1">
                                Total Users
                            </p>
                            <h5 class="fw-semibold text-primary mb-0">{{ $totalUsers }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 zoom-in bg-warning-subtle shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="/assets/images/svgs/icon-briefcase.svg" width="50" height="50" class="mb-3"
                                alt="modernize-img">
                            <p class="fw-semibold fs-3 text-warning mb-1">Subscribed Users</p>
                            <h5 class="fw-semibold text-warning mb-0">{{ $totalSubscribedUsers }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 zoom-in bg-info-subtle shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="/assets/images/svgs/icon-mailbox.svg" width="50" height="50" class="mb-3"
                                alt="modernize-img">
                            <p class="fw-semibold fs-3 text-info mb-1">Unsubscribed Users</p>
                            <h5 class="fw-semibold text-info mb-0">{{ $totalUnsubscribedUsers }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 zoom-in bg-danger-subtle shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="/assets/images/svgs/icon-favorites.svg" width="50" height="50" class="mb-3"
                                alt="modernize-img">
                            <p class="fw-semibold fs-3 text-danger mb-1">Memory Verses</p>
                            <h5 class="fw-semibold text-danger mb-0">{{ $totalMemoryVerse }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 zoom-in bg-info-subtle shadow-none">
                    <div class="card-body">
                        <div class="text-center">
                            <img src="/assets/images/svgs/icon-connect.svg" width="50" height="50" class="mb-3"
                                alt="modernize-img">
                            <p class="fw-semibold fs-3 text-info mb-1">Total Devotionals</p>
                            <h5 class="fw-semibold text-info mb-0">{{ $totalDevotional }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--  Row 1 -->
        <div class="row">
            <div class="col-lg-12 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-body">
                        <div class="d-sm-flex d-block align-items-center justify-content-between mb-7">
                            <div class="mb-3 mb-sm-0">
                                <h4 class="card-title fw-semibold">User Table</h4>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle text-nowrap mb-0">
                                <thead>
                                    <tr class="text-muted fw-semibold">
                                        <th scope="col">S/N</th>
                                        <th scope="col" class="ps-0">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Subscription Plan</th>
                                        <th scope="col">Payment Expires</th>
                                    </tr>
                                </thead>
                                <tbody class="border-top">
                                    @foreach ($users as $user)
                                        <tr>
                                        <td class="">
                                            {{ $loop->index + 1 }}
                                        </td>
                                        <td class="ps-0">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2 pe-1">
                                                    <img src="{{ asset('profile/'.$user->profile_photo_url ) }}" class="rounded-circle"  width="40" height="40" alt="modernize-img">
                                                </div>
                                                <div>
                                                    <h6 class="fw-semibold mb-1">{{ $user->name }}</h6>
                                                    <p class="fs-2 mb-0 text-muted">
                                                        {{ $user->username }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $user->email }}
                                        </td>
                                        <td>
                                            @if ($user->has_paid)
                                                <span class="badge bg-primary">Paid</span>
                                            @else
                                                <span class="badge bg-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $user->payment_expires_at ? $user->payment_expires_at->format('M d, Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
