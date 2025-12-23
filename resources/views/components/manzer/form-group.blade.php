@props([
    'label',
    'icon' => null,
    'name',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'options' => [],
    'rows' => 4,
    'step' => null,
    'min' => null,
    'max' => null,
    'help' => null,
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        @if($icon)
            <i class="{{ $icon }} me-1"></i>
        @endif
        {{ $label }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @if($type === 'password')
        <div class="input-group">
            <input
                type="password"
                id="{{ $name }}"
                name="{{ $name }}"
                class="form-control @error($name) is-invalid @enderror"
                placeholder="{{ $placeholder }}"
                value="{{ old($name, $value) }}"
                {{ $required ? 'required' : '' }}
            >
            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('{{ $name }}')">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    @elseif($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}" class="form-select @error($name) is-invalid @enderror" {{ $required ? 'required' : '' }}>
            @if(count($options) > 0)
                @foreach($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>
    @elseif($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            rows="{{ $rows }}"
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'file')
        <input
            type="file"
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror"
            {{ $required ? 'required' : '' }}
            @if($attributes->has('accept')) accept="{{ $attributes->get('accept') }}" @endif
        >
        {{ $slot }}
    @elseif($type === 'checkbox')
        <div class="form-check">
            <input
                type="checkbox"
                id="{{ $name }}"
                name="{{ $name }}"
                class="form-check-input @error($name) is-invalid @enderror"
                value="1"
                {{ old($name, $value) ? 'checked' : '' }}
            >
            <label class="form-check-label" for="{{ $name }}">{{ $placeholder }}</label>
        </div>
    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror"
            placeholder="{{ $placeholder }}"
            value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }}
            @if($step) step="{{ $step }}" @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
        >
    @endif

    @if($help)
        <small class="text-muted">{{ $help }}</small>
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if($type === 'password')
@push('scripts')
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endpush
@endif
