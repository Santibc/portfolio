<x-app-layout>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Add New District</h1>
                    <a href="{{ route('admin.districts.index') }}" class="btn btn-secondary">
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
                        <form action="{{ route('admin.districts.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">District Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <select class="form-select @error('state') is-invalid @enderror" id="state" name="state">
                                        <option value="">Select state...</option>
                                        <option value="NSW" {{ old('state') == 'NSW' ? 'selected' : '' }}>NSW</option>
                                        <option value="VIC" {{ old('state') == 'VIC' ? 'selected' : '' }}>VIC</option>
                                        <option value="QLD" {{ old('state') == 'QLD' ? 'selected' : '' }}>QLD</option>
                                        <option value="WA" {{ old('state') == 'WA' ? 'selected' : '' }}>WA</option>
                                        <option value="SA" {{ old('state') == 'SA' ? 'selected' : '' }}>SA</option>
                                        <option value="TAS" {{ old('state') == 'TAS' ? 'selected' : '' }}>TAS</option>
                                        <option value="ACT" {{ old('state') == 'ACT' ? 'selected' : '' }}>ACT</option>
                                        <option value="NT" {{ old('state') == 'NT' ? 'selected' : '' }}>NT</option>
                                    </select>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="postcode" class="form-label">Postcode</label>
                                    <input type="text" class="form-control @error('postcode') is-invalid @enderror" id="postcode" name="postcode" value="{{ old('postcode') }}">
                                    @error('postcode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0">
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active (visible in calculator)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.districts.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create District</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
