@extends('layouts.master-2')
@section('content')
    <div class="card w-100 position-relative overflow-hidden">
        <div class="px-4 py-3 border-bottom">
            <h4 class="card-title mb-0">Edit Referral Code: {{ $referralCode->code }}</h4>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.referral-codes.update', $referralCode->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $referralCode->code) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="max_uses" class="form-label">Max Uses (Optional)</label>
                        <input type="number" class="form-control" id="max_uses" name="max_uses" value="{{ old('max_uses', $referralCode->max_uses) }}" min="1">
                        <small class="text-muted">Currently used {{ $referralCode->uses_count }} times.</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="expires_at" class="form-label">Expires At (Optional)</label>
                        <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at', $referralCode->expires_at ? $referralCode->expires_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Referral Code</button>
                <a href="{{ route('admin.referral-codes.index') }}" class="btn btn-outline-secondary mt-3 ms-2">Cancel</a>
            </form>
        </div>
    </div>
@endsection
