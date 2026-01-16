@extends('layouts.master-2')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Transaction</h5>
                        <a href="{{ route('admin.transaction.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.transaction.update', $transaction->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label" for="user_id">User</label>
                                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" id="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $transaction->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
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
                                <input type="number" step="0.01" value="{{ old('amount_paid', $transaction->amount_paid) }}" name="amount_paid" 
                                    class="form-control @error('amount_paid') is-invalid @enderror" id="amount" 
                                    placeholder="Amount" required />
                                @error('amount_paid')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            

                            {{-- <div class="mb-3">
                                <label class="form-label" for="status">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" id="status" required>
                                    <option value="">Select Status</option>
                                    <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ old('status', $transaction->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ old('status', $transaction->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div> --}}
                            <div class="mb-3">
                                <label class="form-label" for="status">Date/Time</label>
                                <input type="datetime-local" name="paid_date" class="form-control @error('paid_date') is-invalid @enderror" value="{{ $transaction->paid_date }}" id="">
                                @error('paid_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.transaction.index') }}" class="btn btn-label-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection