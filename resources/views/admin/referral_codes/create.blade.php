@extends('layouts.master-2')
@section('content')
    <div class="card w-100 position-relative overflow-hidden">
        <div class="px-4 py-3 border-bottom">
            <h4 class="card-title mb-0">Create Referral Code</h4>
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

            <form action="{{ route('admin.referral-codes.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Code</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" required>
                            <button class="btn btn-outline-secondary" type="button" id="generateCodeBtn">Auto Generate</button>
                        </div>
                        <small class="text-muted">The code users will enter during registration.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="max_uses" class="form-label">Max Uses (Optional)</label>
                        <input type="number" class="form-control" id="max_uses" name="max_uses" value="{{ old('max_uses') }}" min="1">
                        <small class="text-muted">Leave empty for unlimited uses.</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="expires_at" class="form-label">Expires At (Optional)</label>
                        <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                        <small class="text-muted">Leave empty if the code never expires.</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Create Referral Code</button>
                <a href="{{ route('admin.referral-codes.index') }}" class="btn btn-outline-secondary mt-3 ms-2">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('generateCodeBtn').addEventListener('click', function() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = '';
            for (let i = 0; i < 8; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('code').value = 'REF-' + code;
        });
    });
</script>
@endpush
