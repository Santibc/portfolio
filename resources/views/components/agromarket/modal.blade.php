@props([
    'id',
    'title',
    'size' => 'md'
])

<div class="modal" id="{{ $id }}" style="display: none;">
    <div class="modal-overlay" onclick="closeModal('{{ $id }}')"></div>
    <div class="modal-content modal-{{ $size }}">
        <div class="modal-header">
            <h2>{{ $title }}</h2>
            <button class="modal-close" onclick="closeModal('{{ $id }}')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="modal-footer">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>

@once
    @push('scripts')
    <script>
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal[style*="display: flex"]');
            openModals.forEach(modal => {
                modal.style.display = 'none';
            });
            document.body.style.overflow = 'auto';
        }
    });
    </script>
    @endpush
@endonce
