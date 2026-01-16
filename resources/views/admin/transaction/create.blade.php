@extends('layouts.master-2')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add Transaction</h5>
                        <a href="{{ route('admin.transaction.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.transaction.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="user_id">User</label>
                                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" id="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="amount">Amount</label>
                                <input type="number" step="0.01" name="amount_paid"
                                    class="form-control @error('amount_paid') is-invalid @enderror" id="amount"
                                    placeholder="Amount" value="{{ old('amount_paid') }}" required />
                                @error('amount_paid')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="status">Date/Time</label>
                                <input type="datetime-local" name="paid_date" class="form-control @error('paid_date') is-invalid @enderror" value="{{ old('paid_date') }}" id="paid_date" required>
                                @error('paid_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.transaction.index') }}" class="btn btn-label-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bx bx-save me-1"></i> Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection