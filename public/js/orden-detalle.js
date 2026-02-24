/**
 * SINDEN - Orden Detalle
 * JavaScript para la vista show de ordenes.
 */

// ==========================================
// Copiar Orden
// ==========================================
function copiarOrden(ordenId) {
    Swal.fire({
        title: 'Copiar Orden?',
        text: 'Se creara un nuevo borrador con los datos de esta orden.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4A7C59',
        confirmButtonText: 'Si, Copiar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Copiando...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });

        $.ajax({
            url: ROUTES_DETALLE.copiar,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Orden Copiada',
                        text: response.message || 'Se ha creado un nuevo borrador.',
                        confirmButtonColor: '#4A7C59'
                    }).then(function() {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                }
            },
            error: function(xhr) {
                var msg = 'Error al copiar la orden.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    });
}

// ==========================================
// Anular Orden
// ==========================================
function abrirModalAnular() {
    $('#motivoAnulacion').val('');
    $('#modalAnularOrden').modal('show');
}

function confirmarAnulacion() {
    var motivo = $('#motivoAnulacion').val().trim();
    if (!motivo) {
        Swal.fire('Error', 'Debe ingresar un motivo para anular la orden.', 'error');
        return;
    }

    $('#btnConfirmarAnular').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Anulando...');

    $.ajax({
        url: ROUTES_DETALLE.anular,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        contentType: 'application/json',
        data: JSON.stringify({ motivo: motivo }),
        success: function(response) {
            if (response.success) {
                $('#modalAnularOrden').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Orden Anulada',
                    text: response.message || 'La orden ha sido anulada.',
                    confirmButtonColor: '#4A7C59'
                }).then(function() {
                    window.location.reload();
                });
            }
        },
        error: function(xhr) {
            var msg = 'Error al anular la orden.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            Swal.fire({ icon: 'error', title: 'Error', text: msg });
        },
        complete: function() {
            $('#btnConfirmarAnular').prop('disabled', false).html('<i class="bi bi-x-circle me-1"></i> Confirmar Anulacion');
        }
    });
}

// ==========================================
// Registrar Pago
// ==========================================
function registrarPago() {
    var monto = parseFloat($('#pagoMonto').val()) || 0;
    var metodo = $('#pagoMetodo').val();
    var referencia = $('#pagoReferencia').val().trim();

    if (monto <= 0) {
        Swal.fire('Error', 'El monto debe ser mayor a 0.', 'error');
        return;
    }

    $('#btnRegistrarPago').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Registrando...');

    $.ajax({
        url: ROUTES_DETALLE.pagos,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        contentType: 'application/json',
        data: JSON.stringify({
            monto: monto,
            metodo_pago: metodo,
            referencia_pago: referencia || null
        }),
        success: function(response) {
            if (response.success) {
                $('#modalAgregarPago').modal('hide');
                $('#pagoMonto').val('');
                $('#pagoReferencia').val('');
                $('#pagoMetodo').val('efectivo');

                // Append pago to list
                var pago = response.pago;
                var badgeAprobado = pago.aprobado
                    ? '<span class="badge bg-success ms-1 small">Aprobado</span>'
                    : '<span class="badge bg-warning text-dark ms-1 small">Pendiente</span>';

                var pagoHtml = '<div class="d-flex justify-content-between align-items-start py-2 border-bottom">'
                    + '<div>'
                    + '  <span class="fw-semibold">' + pago.monto + '</span>'
                    + '  <span class="badge bg-light text-dark border ms-1 small">' + ucfirst(pago.metodo_pago) + '</span>'
                    + '  ' + badgeAprobado
                    + '  <div class="text-muted small">' + (pago.registrado_por || '-') + ' - Ahora</div>'
                    + (pago.referencia_pago ? '  <div class="text-muted small">Ref: ' + pago.referencia_pago + '</div>' : '')
                    + '</div>'
                    + '</div>';

                $('#sinPagos').hide();
                $('#listaPagos').prepend(pagoHtml);

                // Update totals
                if (response.nuevo_total_pagado !== undefined) {
                    $('#totalPagadoDisplay').text(response.nuevo_total_pagado);
                    $('#saldoDisplay').text(response.nuevo_saldo || '$0');
                    var saldoClass = (response.estado_pago === 'saldo_pendiente') ? 'text-danger' : 'text-success';
                    $('#saldoDisplay').removeClass('text-danger text-success').addClass(saldoClass);
                }

                Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                    title: 'Pago registrado', showConfirmButton: false, timer: 2000 });
            }
        },
        error: function(xhr) {
            var msg = 'Error al registrar el pago.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            Swal.fire({ icon: 'error', title: 'Error', text: msg });
        },
        complete: function() {
            $('#btnRegistrarPago').prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i> Registrar');
        }
    });
}

// ==========================================
// Agregar Comentario
// ==========================================
$(function() {
    $('#btnAgregarComentario').on('click', function() {
        agregarComentario();
    });

    $('#nuevoComentario').on('keypress', function(e) {
        if (e.which === 13) agregarComentario();
    });
});

function agregarComentario() {
    var contenido = $('#nuevoComentario').val().trim();
    if (!contenido) return;

    $('#btnAgregarComentario').prop('disabled', true);

    $.ajax({
        url: ROUTES_DETALLE.comentarios,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
        contentType: 'application/json',
        data: JSON.stringify({ contenido: contenido }),
        success: function(response) {
            if (response.success) {
                var c = response.comentario;
                var html = '<div class="comment-item">'
                    + '<div class="d-flex justify-content-between">'
                    + '  <span class="comment-author">' + (c.usuario || '-') + '</span>'
                    + '  <span class="comment-date">Ahora</span>'
                    + '</div>'
                    + '<div class="comment-content">' + escapeHtmlDetalle(c.contenido) + '</div>'
                    + '</div>';

                $('#sinComentarios').hide();
                $('#listaComentarios').prepend(html);
                $('#nuevoComentario').val('');
            }
        },
        error: function(xhr) {
            var msg = 'Error al agregar comentario.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            Swal.fire({ icon: 'error', title: 'Error', text: msg });
        },
        complete: function() {
            $('#btnAgregarComentario').prop('disabled', false);
        }
    });
}

// ==========================================
// Lightbox
// ==========================================
function abrirLightbox(rutaArchivo, titulo) {
    var src = rutaArchivo;
    if (src && !src.startsWith('http') && !src.startsWith('/')) {
        src = '/' + src;
    }
    $('#lightboxImagen').attr('src', src);
    $('#lightboxTitulo').text(titulo || 'Imagen');
    $('#modalLightbox').modal('show');
}

// ==========================================
// Helpers
// ==========================================
function formatCOPDetalle(valor) {
    if (isNaN(valor) || valor === null) valor = 0;
    return Math.round(valor).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function ucfirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function escapeHtmlDetalle(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
