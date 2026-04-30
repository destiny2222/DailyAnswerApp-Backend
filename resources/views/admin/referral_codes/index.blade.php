@extends('layouts.master-2')
@section('content')
    <div class="card w-100 position-relative overflow-hidden">
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Referral Codes</h4>
            <a href="{{ route('admin.referral-codes.create') }}" class="btn btn-primary">Create Code</a>
        </div>
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Code</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Uses Count</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Max Uses</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Expires At</h6>
                            </th>
                            <th>
                                <h6 class="fs-4 fw-semibold mb-0">Action</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($referralCodes) != 0)
                            @foreach ($referralCodes as $code)
                                <tr>
                                    <td>
                                        <p class="mb-0 fw-bold">{{ $code->code }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-normal">{{ $code->uses_count }} / {{ $code->users_count }} (registered)</p>
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-normal">{{ $code->max_uses ?? 'Unlimited' }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0 fw-normal">{{ $code->expires_at ? $code->expires_at->format('M d, Y H:i') : 'Never' }}</p>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.referral-codes.show', $code->id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('admin.referral-codes.edit', $code->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('admin.referral-codes.destroy', $code->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this code?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">
                                    No referral codes found.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $referralCodes->links() }}
            </div>
        </div>
    </div>
@endsection
