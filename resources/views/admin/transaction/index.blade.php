@extends('layouts.master-2')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Grid Card -->
        <div class="row">
            <!-- Hoverable Table rows -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transactions</h5>
                    <a href="{{ route('admin.transaction.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i> Create Transaction
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Amount Paid</th>
                                {{-- <th>Status</th> --}}
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="transactionTable">
                            @if (count($transaction) != 0)
                                @foreach ($transaction as $transactions)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $transactions->user->name }}</td>
                                        <td>{{ $transactions->paid_date->format('d M Y h:i A') }}</td>
                                        <td>{{ $transactions->amount_paid }}</td>
                                        {{-- <td>
                                            @if ($transactions->status == 'pending')
                                                <span class="badge bg-label-danger">Pending</span>
                                            @elseif ($transactions->status == 'paid')
                                                <span class="badge bg-label-success">Paid</span>
                                            @elseif ($transactions->status == 'failed')
                                                <span class="badge bg-label-danger">Failed</span>
                                            @elseif ($transactions->status == 'cancelled')
                                                <span class="badge bg-label-secondary">Cancelled</span>
                                            @endif
                                        </td> --}}
                                        <td>
                                            <div class="d-flex">
                                                <a class="btn btn-primary me-2" href="{{ route('admin.transaction.edit', $transactions->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.transaction.delete', $transactions->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bx bx-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <!--/ Hoverable Table rows -->
            <div class="row pt-4">
                <div class="col-12 d-flex justify-content-end">
                    {!!  $transaction->withQueryString()->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>
@endsection

