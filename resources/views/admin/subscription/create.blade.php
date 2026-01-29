@extends('layouts.master-2')
@section('content')
    <div class="card w-100 position-relative overflow-hidden">
        <div class="px-4 py-3 border-bottom">
            <h4 class="card-title mb-0">Create New Subscription Plan</h4>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.subscription.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="name" class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="price" class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price') }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="interval" class="form-label fw-semibold">Billing Interval <span class="text-danger">*</span></label>
                        <select class="form-select @error('interval') is-invalid @enderror" id="interval" name="interval" required>
                            <option value="">Select billing interval</option>
                            <option value="quarterly" {{ old('interval') == 'quarterly' ? 'selected' : '' }}>Quarterly (Every 3 Months)</option>
                            <option value="monthly" {{ old('interval') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('interval') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            
                        </select>
                        @error('interval')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="plan_id" class="form-label fw-semibold">Plan ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('plan_id') is-invalid @enderror" 
                               id="plan_id" name="plan_id" value="{{ old('plan_id') }}" required 
                               placeholder="e.g., plan_premium_monthly">
                        @error('plan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Unique identifier for the plan (used for payment processing)</small>
                    </div>

                    {{-- <div class="col-md-12 mb-4">
                        <label for="features" class="form-label fw-semibold">Features</label>
                        <div id="features-container">
                            @if (old('features'))
                                @foreach (old('features') as $index => $feature)
                                    <div class="input-group mb-2 feature-item">
                                        <input type="text" class="form-control" name="features[]" 
                                               value="{{ $feature }}" placeholder="Enter a feature">
                                        <button type="button" class="btn btn-outline-danger remove-feature">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 feature-item">
                                    <input type="text" class="form-control" name="features[]" 
                                           placeholder="Enter a feature">
                                    <button type="button" class="btn btn-outline-danger remove-feature">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-feature">
                            <i class="ti ti-plus"></i> Add Feature
                        </button>
                        @error('features')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div> --}}
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i> Create Plan
                    </button>
                    <a href="{{ route('admin.subscription.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-x me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const featuresContainer = document.getElementById('features-container');
        const addFeatureBtn = document.getElementById('add-feature');

        addFeatureBtn.addEventListener('click', function() {
            const newFeature = document.createElement('div');
            newFeature.className = 'input-group mb-2 feature-item';
            newFeature.innerHTML = `
                <input type="text" class="form-control" name="features[]" placeholder="Enter a feature">
                <button type="button" class="btn btn-outline-danger remove-feature">
                    <i class="ti ti-trash"></i>
                </button>
            `;
            featuresContainer.appendChild(newFeature);
        });

        featuresContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-feature')) {
                const featureItem = e.target.closest('.feature-item');
                if (featuresContainer.querySelectorAll('.feature-item').length > 1) {
                    featureItem.remove();
                } else {
                    featureItem.querySelector('input').value = '';
                }
            }
        });

        // Auto-generate slug from name
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        nameInput.addEventListener('input', function() {
            if (!slugInput.value || slugInput.dataset.auto !== 'false') {
                slugInput.value = this.value.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
            }
        });

        slugInput.addEventListener('input', function() {
            this.dataset.auto = 'false';
        });
    });
</script>
@endpush
