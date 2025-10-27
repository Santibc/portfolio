<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Edit Coupon: {{ $coupon->code }}</h1>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to List
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="alert alert-info">
                                        <strong>Usage Stats:</strong> This coupon has been used {{ $coupon->usage_count }} times
                                        @if($coupon->usage_limit)
                                            out of {{ $coupon->usage_limit }} total uses allowed
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">Coupon Code *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $coupon->code) }}" required style="text-transform: uppercase;">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $coupon->description) }}">
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="discount_type" class="form-label">Discount Type *</label>
                                    <select class="form-select @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type" required>
                                        <option value="percentage" {{ old('discount_type', $coupon->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                        <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                                    </select>
                                    @error('discount_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="discount_value" class="form-label">Discount Value *</label>
                                    <input type="number" step="0.01" class="form-control @error('discount_value') is-invalid @enderror" id="discount_value" name="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}" required>
                                    @error('discount_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="min_order_amount" class="form-label">Min. Order Amount</label>
                                    <input type="number" step="0.01" class="form-control @error('min_order_amount') is-invalid @enderror" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}">
                                    @error('min_order_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="usage_limit" class="form-label">Usage Limit</label>
                                    <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1">
                                    @error('usage_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Current usage: {{ $coupon->usage_count }}</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $coupon->start_date?->format('Y-m-d')) }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $coupon->expiry_date?->format('Y-m-d')) }}">
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active (coupon can be used)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Coupon</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
