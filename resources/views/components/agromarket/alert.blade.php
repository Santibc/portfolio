@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => true
])

@php
    $typeConfig = [
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-400',
            'text' => 'text-green-800',
            'icon' => 'fas fa-check-circle',
            'iconColor' => 'text-green-400',
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-400',
            'text' => 'text-red-800',
            'icon' => 'fas fa-exclamation-circle',
            'iconColor' => 'text-red-400',
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-400',
            'text' => 'text-yellow-800',
            'icon' => 'fas fa-exclamation-triangle',
            'iconColor' => 'text-yellow-400',
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-400',
            'text' => 'text-blue-800',
            'icon' => 'fas fa-info-circle',
            'iconColor' => 'text-blue-400',
        ],
    ];
    $config = $typeConfig[$type] ?? $typeConfig['info'];
@endphp

<div class="alert-component {{ $config['bg'] }} {{ $config['border'] }} {{ $config['text'] }}" role="alert">
    <div class="alert-content">
        <i class="{{ $config['icon'] }} {{ $config['iconColor'] }}"></i>
        <span class="alert-message">{{ $message }}</span>
    </div>
    @if($dismissible)
        <button type="button" class="alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    @endif
</div>

<style>
    .alert-component {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        border-left: 4px solid;
        margin-bottom: 1rem;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-component.bg-green-50 { background-color: #f0fdf4; }
    .alert-component.bg-red-50 { background-color: #fef2f2; }
    .alert-component.bg-yellow-50 { background-color: #fffbeb; }
    .alert-component.bg-blue-50 { background-color: #eff6ff; }

    .alert-component.border-green-400 { border-left-color: #4ade80; }
    .alert-component.border-red-400 { border-left-color: #f87171; }
    .alert-component.border-yellow-400 { border-left-color: #facc15; }
    .alert-component.border-blue-400 { border-left-color: #60a5fa; }

    .alert-component.text-green-800 { color: #166534; }
    .alert-component.text-red-800 { color: #991b1b; }
    .alert-component.text-yellow-800 { color: #854d0e; }
    .alert-component.text-blue-800 { color: #1e40af; }

    .alert-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-content i {
        font-size: 1.25rem;
    }

    .alert-content i.text-green-400 { color: #4ade80; }
    .alert-content i.text-red-400 { color: #f87171; }
    .alert-content i.text-yellow-400 { color: #facc15; }
    .alert-content i.text-blue-400 { color: #60a5fa; }

    .alert-message {
        font-weight: 500;
    }

    .alert-close {
        background: none;
        border: none;
        cursor: pointer;
        opacity: 0.6;
        padding: 0.25rem;
        transition: opacity 0.2s;
        color: inherit;
    }

    .alert-close:hover {
        opacity: 1;
    }
</style>
