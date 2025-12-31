{{--
    Optimized Image Component with Lazy Loading

    Uso:
    <x-optimized-image
        src="url/imagen.jpg"
        alt="Descripción"
        class="img-fluid"
        width="300"
        height="200"
    />
--}}

@props([
    'src',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'lazy' => true,
    'placeholder' => 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 300 200\'%3E%3Crect fill=\'%23f8f9fa\' width=\'300\' height=\'200\'/%3E%3C/svg%3E'
])

<img
    @if($lazy)
        src="{{ $placeholder }}"
        data-src="{{ $src }}"
        loading="lazy"
    @else
        src="{{ $src }}"
    @endif
    alt="{{ $alt }}"
    @if($class) class="{{ $class }}" @endif
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    decoding="async"
    {{ $attributes }}
>

@once
@push('scripts')
<script>
// Simple lazy loading fallback for browsers without native support
document.addEventListener('DOMContentLoaded', function() {
    const lazyImages = document.querySelectorAll('img[data-src]');

    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for browsers without IntersectionObserver
        lazyImages.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    }
});
</script>
@endpush
@endonce
