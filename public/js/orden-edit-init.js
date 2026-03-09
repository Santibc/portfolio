/**
 * SINDEN - Orden Edit Init
 * Precarga el wizard con datos existentes de la orden para modo edicion.
 */

$(function() {
    if (typeof ORDEN_DATA === 'undefined' || !ORDEN_DATA) return;

    // Establecer ordenId en el estado global
    wizardState.ordenId = ORDEN_DATA.id;

    // Delay to ensure wizard is fully initialized
    setTimeout(function() {
        cargarCliente();
        cargarBosquejos();
        cargarPiezas();
        cargarItems();
        cargarPagos();
        cargarFechas();
        cargarFirma();
        cargarOperario();

        // Set initial saved hash to prevent immediate auto-save
        wizardState.lastSavedHash = JSON.stringify(recopilarDatosFormulario());
    }, 200);
});

// ==========================================
// Cargar Cliente
// ==========================================
function cargarCliente() {
    if (!ORDEN_DATA.cliente) return;
    var c = ORDEN_DATA.cliente;
    seleccionarCliente(c.id, c.nombre, c.celular_1 || '', c.correo || '');
}

// ==========================================
// Cargar Items
// ==========================================
function cargarItems() {
    if (!ORDEN_DATA.items || ORDEN_DATA.items.length === 0) return;

    ORDEN_DATA.items.forEach(function(item) {
        agregarFilaItem();
        var idx = wizardState.itemCounter;
        var $row = $('#itemRow_' + idx);

        $row.find('.item-catalogo-id').val(item.catalogo_item_id || '');
        $row.find('.item-codigo').val(item.codigo || '');
        $row.find('.item-descripcion').val(item.descripcion || '');
        $row.find('.item-cantidad').val(item.cantidad || 1);
        $row.find('.item-precio').val(item.precio_unitario || 0);
        $row.find('.item-iva-check').prop('checked', parseFloat(item.porcentaje_iva) > 0);
        $row.find('.item-categoria').val(item.categoria || 'servicio');

        if (item.catalogo_item_id) {
            $row.find('.item-codigo').prop('readonly', true).addClass('item-readonly');
            $row.find('.item-descripcion').prop('readonly', true).addClass('item-readonly');
            $row.find('.btn-desvincular-item').show();
        }

        calcularTotalFila(idx);
    });

    recalcularTotales();
}

// ==========================================
// Cargar Bosquejos
// ==========================================
function cargarBosquejos() {
    if (!ORDEN_DATA.bosquejos || ORDEN_DATA.bosquejos.length === 0) return;

    ORDEN_DATA.bosquejos.forEach(function(b) {
        wizardState.bosquejos.push({
            id: b.id,
            nombre: b.nombre,
            tipo_origen: b.tipo_origen,
            ruta_archivo: b.ruta_archivo,
            ruta_miniatura: b.ruta_miniatura,
            plantilla_bosquejo_id: b.plantilla_bosquejo_id
        });
    });

    renderizarGrillaBosquejos();
    actualizarSelectBosquejosPiezas();
}

// ==========================================
// Cargar Piezas
// ==========================================
function cargarPiezas() {
    if (!ORDEN_DATA.piezas || ORDEN_DATA.piezas.length === 0) return;

    ORDEN_DATA.piezas.forEach(function(pieza) {
        agregarFilaPieza();
        var idx = wizardState.piezaCounter;
        var $row = $('#piezaRow_' + idx);

        $row.find('.pieza-nombre').val(pieza.nombre || '');
        $row.find('.pieza-cantidad').val(pieza.cantidad || 1);

        if (pieza.material) {
            $row.find('.pieza-material').val(pieza.material);
        }
        if (pieza.calibre) {
            $row.find('.pieza-calibre').val(pieza.calibre);
        }
        if (pieza.notas) {
            $row.find('.pieza-notas').val(pieza.notas);
        }

        // Match bosquejo by DB ID in wizardState.bosquejos array
        if (pieza.orden_bosquejo_id) {
            var bosquejoIdx = -1;
            for (var i = 0; i < wizardState.bosquejos.length; i++) {
                if (wizardState.bosquejos[i].id == pieza.orden_bosquejo_id) {
                    bosquejoIdx = i;
                    break;
                }
            }
            if (bosquejoIdx >= 0) {
                vincularBosquejoAPieza(idx, bosquejoIdx);
            }
        }

        // Restore requiere_operario checkbox state
        if (pieza.requiere_operario === false || pieza.requiere_operario === 0) {
            $row.find('.pieza-requiere-operario').prop('checked', false);
        }

        generarEspecificacion(idx);
    });

    actualizarVisibilidadOperario();
}

// ==========================================
// Cargar Pagos
// ==========================================
function cargarPagos() {
    if (!ORDEN_DATA.pagos || ORDEN_DATA.pagos.length === 0) return;

    var bloquearPagos = (typeof IS_GENERATED !== 'undefined' && IS_GENERATED)
                     && (typeof IS_ADMIN !== 'undefined' && !IS_ADMIN);

    ORDEN_DATA.pagos.forEach(function(pago) {
        agregarFilaPago();
        var idx = wizardState.pagoCounter;
        var $row = $('#pagoRow_' + idx);

        $row.find('.pago-monto').val(pago.monto || 0);
        $row.find('.pago-metodo').val(pago.metodo_pago || 'efectivo');
        $row.find('.pago-referencia').val(pago.referencia_pago || '');

        if (bloquearPagos) {
            $row.find('.pago-monto').prop('disabled', true);
            $row.find('.pago-metodo').prop('disabled', true);
            $row.find('.pago-referencia').prop('disabled', true);
            $row.find('.btn-outline-danger').remove();
        }
    });

    // Si no es admin y la orden ya fue generada, ocultar boton de agregar pago
    if (bloquearPagos) {
        $('#seccionPagos .card-header .btn-primary').hide();
    }

    recalcularSaldo();
}

// ==========================================
// Cargar Fechas y Notas
// ==========================================
function cargarFechas() {
    if (ORDEN_DATA.fecha_entrega) {
        $('#fecha_entrega').val(ORDEN_DATA.fecha_entrega);
        marcarStepCompletado(7);
    }
    if (ORDEN_DATA.hora_entrega) {
        $('#hora_entrega').val(ORDEN_DATA.hora_entrega);
    }
    if (ORDEN_DATA.notas) {
        $('#notas').val(ORDEN_DATA.notas);
    }
}

// ==========================================
// Cargar Firma existente
// ==========================================
function cargarFirma() {
    if (!ORDEN_DATA.ruta_firma_cliente) return;

    var src = ORDEN_DATA.ruta_firma_cliente;
    if (src && !src.startsWith('http') && !src.startsWith('/') && !src.startsWith('data:')) {
        src = '/' + src;
    }

    // Show existing signature image above the canvas
    var $container = $('#firmaCanvasContainer');
    if ($container.length) {
        $container.before(
            '<div id="firmaExistente" class="text-center mb-2">'
            + '<p class="small text-muted mb-1">Firma actual (dibuje sobre el canvas para reemplazar)</p>'
            + '<img src="' + src + '" class="firma-existente" alt="Firma existente">'
            + '</div>'
        );
    }
    marcarStepCompletado(4);
}

// ==========================================
// Cargar Operario
// ==========================================
function cargarOperario() {
    if (!ORDEN_DATA.operario_id) return;

    var $select = $('#operario_id');
    if ($select.length) {
        $select.val(ORDEN_DATA.operario_id);
        if ($select.val()) {
            marcarStepCompletado(5);
        }
    }
}
