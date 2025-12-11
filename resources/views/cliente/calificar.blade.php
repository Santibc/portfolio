@extends('cliente.layout')

@section('title', 'Calificar Producto')

@section('header')
    <a href="{{ route('cliente.compras.show', $compra->id) }}" class="text-decoration-none text-muted mb-2 d-inline-block">
        <i class="bi bi-arrow-left"></i> Volver al Pedido
    </a>
    <h1 class="mb-0"><i class="bi bi-star"></i> Calificar Producto</h1>
    <p class="text-muted mb-0">Comparte tu experiencia con este producto</p>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="content-card">
                <!-- Producto a calificar -->
                <div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom">
                    <img src="{{ $itemCompra->producto->url_imagen_principal ?? asset('images/no-image.png') }}"
                         alt="{{ $itemCompra->nombre_producto }}"
                         class="rounded"
                         style="width: 100px; height: 100px; object-fit: cover;">

                    <div>
                        <h5 class="mb-1">{{ $itemCompra->nombre_producto }}</h5>
                        @if($itemCompra->info_variante)
                            <small class="text-muted">{{ $itemCompra->info_variante }}</small>
                        @endif
                        <div class="mt-1">
                            <span class="badge bg-success">
                                <i class="bi bi-patch-check"></i> Compra verificada
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Formulario de calificación -->
                <form action="{{ route('cliente.calificar.guardar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="item_compra_id" value="{{ $itemCompra->id }}">

                    <!-- Estrellas -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">¿Cómo calificarías este producto? *</label>
                        <div class="star-rating" id="starRating">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star fs-2" data-rating="{{ $i }}" style="cursor: pointer; color: #e5e7eb;"></i>
                            @endfor
                        </div>
                        <input type="hidden" name="estrellas" id="estrellas" value="{{ old('estrellas', '') }}" required>
                        @error('estrellas')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Título -->
                    <div class="mb-4">
                        <label for="titulo" class="form-label fw-bold">Título de tu reseña (opcional)</label>
                        <input type="text"
                               class="form-control @error('titulo') is-invalid @enderror"
                               id="titulo"
                               name="titulo"
                               value="{{ old('titulo') }}"
                               placeholder="Ej: Excelente producto, muy recomendado"
                               maxlength="255">
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Comentario -->
                    <div class="mb-4">
                        <label for="comentario" class="form-label fw-bold">Tu opinión (opcional)</label>
                        <textarea class="form-control @error('comentario') is-invalid @enderror"
                                  id="comentario"
                                  name="comentario"
                                  rows="4"
                                  placeholder="Cuéntanos qué te pareció el producto, su calidad, si cumplió tus expectativas..."
                                  maxlength="1000">{{ old('comentario') }}</textarea>
                        <div class="form-text">Máximo 1000 caracteres</div>
                        @error('comentario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="bi bi-check-lg"></i> Enviar Calificación
                        </button>
                        <a href="{{ route('cliente.compras.show', $compra->id) }}" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const stars = $('#starRating .bi');
        const inputEstrellas = $('#estrellas');
        const submitBtn = $('#submitBtn');

        // Inicializar con valor previo si existe
        const valorPrevio = inputEstrellas.val();
        if (valorPrevio) {
            updateStars(parseInt(valorPrevio));
            submitBtn.prop('disabled', false);
        }

        // Hover effect
        stars.on('mouseenter', function() {
            const rating = $(this).data('rating');
            highlightStars(rating);
        });

        stars.on('mouseleave', function() {
            const currentRating = inputEstrellas.val();
            if (currentRating) {
                highlightStars(parseInt(currentRating));
            } else {
                clearStars();
            }
        });

        // Click to select
        stars.on('click', function() {
            const rating = $(this).data('rating');
            inputEstrellas.val(rating);
            updateStars(rating);
            submitBtn.prop('disabled', false);
        });

        function highlightStars(rating) {
            stars.each(function(index) {
                if (index < rating) {
                    $(this).removeClass('bi-star').addClass('bi-star-fill').css('color', '#ffc107');
                } else {
                    $(this).removeClass('bi-star-fill').addClass('bi-star').css('color', '#e5e7eb');
                }
            });
        }

        function updateStars(rating) {
            highlightStars(rating);
        }

        function clearStars() {
            stars.removeClass('bi-star-fill').addClass('bi-star').css('color', '#e5e7eb');
        }
    });
</script>
@endpush
