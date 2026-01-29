@extends('layouts.master-2')
@section('content')
    <div class="card w-100 position-relative overflow-hidden">
        <div class="px-4 py-3 border-bottom">
            <h4 class="card-title mb-0">User Table</h4>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Name</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Email Address</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Subscription Plan</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Payment Expires</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Action</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($users) != 0)
                            @forelse ($users as $usering)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('profile/'.$usering->profile_photo_url) }}" class="rounded-circle"
                                                width="40" height="40">
                                            <div class="ms-3">
                                                <h6 class="fs-4 fw-semibold mb-0">{{ $usering->name }}</h6>
                                                <span class="fw-normal">{{ $usering->username }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-normal">{{ $usering->email }}</p>
                                    </td>
                                    <td>
                                        @if ($usering->has_paid)
                                            <span class="badge bg-primary">Paid</span>
                                        @else
                                            <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>{{ $usering->payment_expires_at ? $usering->payment_expires_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.customer.edit', $usering->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('admin.customer.delete', $usering->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
<script>
    ClassicEditor
        .create(document.getElementById('summary-body'))
        .catch(error => {
            console.error(error);
        });
</script>
