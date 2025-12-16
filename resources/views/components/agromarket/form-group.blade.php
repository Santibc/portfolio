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
])

<div class="form-group">
    <label for="{{ $name }}" class="form-label">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $label }}
    </label>

    @if($type === 'password')
        <div class="password-input-container">
            <input
                type="password"
                id="{{ $name }}"
                name="{{ $name }}"
                class="form-input"
                placeholder="{{ $placeholder }}"
                value="{{ old($name, $value) }}"
                {{ $required ? 'required' : '' }}
            >
            <button type="button" class="password-toggle" onclick="togglePassword('{{ $name }}')">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    @elseif($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}" class="form-input" {{ $required ? 'required' : '' }}>
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
            class="form-input"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            rows="{{ $rows }}"
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            class="form-input"
            placeholder="{{ $placeholder }}"
            value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }}
            @if($step) step="{{ $step }}" @endif
            @if($min !== null) min="{{ $min }}" @endif
            @if($max !== null) max="{{ $max }}" @endif
        >
    @endif

    @error($name)
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>

@push('scripts')
@if($type === 'password')
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const icon = input.nextElementSibling.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endif
@endpush
