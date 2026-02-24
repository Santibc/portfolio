/**
 * SINDEN - Orden Wizard
 * Orquestador principal del wizard de creacion de ordenes.
 */

// ==========================================
// Estado Global
// ==========================================
var wizardState = {
    ordenId: null,
    clienteId: null,
    bosquejos: [],
    firmaData: null,
    lastSavedHash: null,
    autoSaveTimer: null,
    itemCounter: 0,
    piezaCounter: 0,
    pagoCounter: 0
};

// ==========================================
// Inicializacion
// ==========================================
$(function() {
    initClienteAutocomplete();
    initFirmaCanvas();
    initDibujoCanvas();
    initAutoSave();
    initStepWatchers();

    // Cerrar dropdown de autocomplete al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#clienteSearch, #clienteResults').length) {
            $('#clienteResults').hide();
        }
        // Cerrar dropdowns de items
        if (!$(e.target).closest('.item-codigo, .item-autocomplete-results').length) {
            $('.item-autocomplete-results').hide();
        }
        // Cerrar dropdowns de material
        if (!$(e.target).closest('.pieza-material, .material-autocomplete-results').length) {
            $('.material-autocomplete-results').hide();
        }
    });

    // Focus en campo codigo de items -> mostrar todas las opciones
    $(document).on('focus click', '.item-codigo', function() {
        buscarItemCatalogo(this);
    });

    // Focus/click en campo material de piezas -> mostrar todas las opciones
    $(document).on('focus click', '.pieza-material', function() {
        buscarMaterialPieza(this);
    });
});

// ==========================================
// Step Watchers (completado de secciones)
// ==========================================
function initStepWatchers() {
    // Step 5: Operario - se completa al seleccionar
    $('#operario_id').on('change', function() {
        if ($(this).val()) {
            marcarStepCompletado(5);
        } else {
            desmarcarStep(5);
        }
    });

    // Step 7: Fechas - se completa al poner fecha de entrega
    $('#fecha_entrega').on('change', function() {
        if ($(this).val()) {
            marcarStepCompletado(7);
        } else {
            desmarcarStep(7);
        }
    });
}

// ==========================================
// Utilidades
// ==========================================
function formatCOP(valor) {
    if (isNaN(valor) || valor === null) valor = 0;
    return '$' + Math.round(valor).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function parseCOP(str) {
    if (!str) return 0;
    return parseFloat(String(str).replace(/[$.]/g, '').replace(',', '.')) || 0;
}

function handleAjaxError(xhr, contexto) {
    var msg = 'Error al ' + contexto + '.';
    if (xhr.responseJSON) {
        if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
        if (xhr.responseJSON.errores) {
            msg += '<br><ul>' + xhr.responseJSON.errores.map(function(e) { return '<li>' + e + '</li>'; }).join('') + '</ul>';
        }
    }
    Swal.fire({ icon: 'error', title: 'Error', html: msg });
}

// ==========================================
// SECCION 1: Cliente
// ==========================================
function initClienteAutocomplete() {
    var debounceTimer = null;
    var $input = $('#clienteSearch');
    var $results = $('#clienteResults');

    $input.on('keyup', function() {
        clearTimeout(debounceTimer);
        var q = $(this).val().trim();
        if (q.length < 2) { $results.hide(); return; }

        debounceTimer = setTimeout(function() {
            $.get(ROUTES.clienteAutocomplete, { q: q }, function(data) {
                $results.empty();
                if (data.length === 0) {
                    $results.append('<div class="list-group-item text-muted small">No se encontraron clientes</div>');
                } else {
                    data.forEach(function(c) {
                        $results.append(
                            '<a href="#" class="list-group-item list-group-item-action py-2" onclick="seleccionarCliente(' + c.id + ', \'' + escapeHtml(c.nombre) + '\', \'' + escapeHtml(c.celular_1 || '') + '\', \'' + escapeHtml(c.correo || '') + '\'); return false;">'
                            + '<strong>' + escapeHtml(c.nombre) + '</strong>'
                            + '<small class="text-muted d-block">' + (c.celular_1 || '-') + ' | ' + (c.correo || '-') + '</small>'
                            + '</a>'
                        );
                    });
                }
                $results.show();
            });
        }, 300);
    });

    $input.on('focus', function() {
        if ($results.children().length > 0 && $(this).val().length >= 2) {
            $results.show();
        }
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function seleccionarCliente(id, nombre, celular, correo) {
    wizardState.clienteId = id;
    $('#cliente_id').val(id);
    $('#clienteSearch').val(nombre).prop('disabled', true);
    $('#clienteResults').hide();
    $('#clienteStatus').text('Seleccionado').removeClass('bg-light text-muted').addClass('bg-success text-white');

    // Construir tarjeta de info del cliente via JS
    var infoHtml = '<div class="alert alert-light border d-flex align-items-center mb-0">'
        + '<i class="bi bi-person-check-fill text-success me-3 fs-4"></i>'
        + '<div class="flex-grow-1">'
        + '  <strong class="d-block">' + escapeHtml(nombre) + '</strong>'
        + '  <small class="text-muted">'
        + '    <i class="bi bi-phone me-1"></i>' + escapeHtml(celular || '-')
        + '    <span class="mx-2">|</span>'
        + '    <i class="bi bi-envelope me-1"></i>' + escapeHtml(correo || '-')
        + '  </small>'
        + '</div>'
        + '<button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="limpiarCliente()">'
        + '  <i class="bi bi-x-lg"></i>'
        + '</button>'
        + '</div>';

    $('#clienteSeleccionado').html(infoHtml).slideDown(200);
    marcarStepCompletado(1);
}

function limpiarCliente() {
    wizardState.clienteId = null;
    $('#cliente_id').val('');
    $('#clienteSeleccionado').slideUp(200, function() {
        $(this).empty();
        $('#clienteSearch').val('').prop('disabled', false).focus();
    });
    $('#clienteStatus').text('Sin seleccionar').removeClass('bg-success text-white').addClass('bg-light text-muted');
    desmarcarStep(1);
}

function crearClienteInline() {
    var nombre = $('#nuevoClienteNombre').val().trim();
    if (!nombre) {
        Swal.fire('Error', 'El nombre del cliente es obligatorio.', 'error');
        return;
    }

    $.ajax({
        url: ROUTES.crearCliente,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': WIZARD_CONFIG.csrfToken },
        contentType: 'application/json',
        data: JSON.stringify({
            nombre: nombre,
            celular_1: $('#nuevoClienteCelular1').val().trim(),
            celular_2: $('#nuevoClienteCelular2').val().trim(),
            correo: $('#nuevoClienteCorreo').val().trim(),
            direccion: $('#nuevoClienteDireccion').val().trim()
        }),
        success: function(response) {
            if (response.success) {
                var c = response.cliente;
                seleccionarCliente(c.id, c.nombre, c.celular_1, c.correo);
                $('#modalNuevoCliente').modal('hide');
                // Limpiar formulario del modal
                $('#nuevoClienteNombre, #nuevoClienteCelular1, #nuevoClienteCelular2, #nuevoClienteCorreo, #nuevoClienteDireccion').val('');
                Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                    title: 'Cliente creado y seleccionado', showConfirmButton: false, timer: 2000 });
            }
        },
        error: function(xhr) { handleAjaxError(xhr, 'crear el cliente'); }
    });
}

// ==========================================
// SECCION 2: Items
// ==========================================
function agregarFilaItem() {
    wizardState.itemCounter++;
    var idx = wizardState.itemCounter;

    var html = '<tr id="itemRow_' + idx + '" data-idx="' + idx + '">'
        + '<td class="text-center text-muted"><span class="item-num">' + contarFilasItems() + '</span></td>'
        + '<td class="position-relative">'
        + '  <input type="text" class="form-control form-control-sm item-codigo" data-idx="' + idx + '" placeholder="Buscar..." autocomplete="off" onkeyup="buscarItemCatalogo(this)">'
        + '  <div class="item-autocomplete-results list-group shadow-sm" id="itemResults_' + idx + '" style="display:none; position:absolute; z-index:1050; width:100%;"></div>'
        + '  <input type="hidden" class="item-catalogo-id" value="">'
        + '  <input type="hidden" class="item-categoria" value="servicio">'
        + '</td>'
        + '<td><input type="text" class="form-control form-control-sm item-descripcion" placeholder="Descripcion del item"></td>'
        + '<td><input type="number" class="form-control form-control-sm text-center item-cantidad" value="1" min="0.01" step="0.01" onchange="calcularTotalFila(' + idx + ')" onkeyup="calcularTotalFila(' + idx + ')"></td>'
        + '<td><input type="number" class="form-control form-control-sm text-end item-precio" value="0" min="0" step="0.01" onchange="calcularTotalFila(' + idx + ')" onkeyup="calcularTotalFila(' + idx + ')"></td>'
        + '<td class="text-center"><input type="checkbox" class="form-check-input item-iva-check" checked onchange="calcularTotalFila(' + idx + ')"></td>'
        + '<td class="text-end fw-semibold item-subtotal-display">$0</td>'
        + '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="eliminarFilaItem(' + idx + ')"><i class="bi bi-trash"></i></button></td>'
        + '</tr>';

    $('#tbodyItems').append(html);
    $('#itemsVacio').hide();
    $('#panelTotales').show();
    renumerarFilasItems();
    // Focus en el campo codigo de la nueva fila
    $('#itemRow_' + idx + ' .item-codigo').focus();
}

function eliminarFilaItem(idx) {
    $('#itemRow_' + idx).remove();
    renumerarFilasItems();
    recalcularTotales();
    if ($('#tbodyItems tr').length === 0) {
        $('#itemsVacio').show();
        $('#panelTotales').hide();
    }
}

function contarFilasItems() {
    return $('#tbodyItems tr').length + 1;
}

function renumerarFilasItems() {
    $('#tbodyItems tr').each(function(i) {
        $(this).find('.item-num').text(i + 1);
    });
}

var itemSearchTimers = {};
function buscarItemCatalogo(input) {
    var idx = $(input).data('idx');
    var q = $(input).val().trim();
    var $results = $('#itemResults_' + idx);

    clearTimeout(itemSearchTimers[idx]);

    var delay = q.length === 0 ? 0 : 300;

    itemSearchTimers[idx] = setTimeout(function() {
        $.get(ROUTES.itemAutocomplete, { q: q }, function(data) {
            $results.empty();
            if (data.length === 0) {
                $results.append('<div class="list-group-item text-muted small py-1">Sin resultados</div>');
            } else {
                data.forEach(function(item) {
                    $results.append(
                        '<a href="#" class="list-group-item list-group-item-action py-1 small" '
                        + 'onclick="seleccionarItemCatalogo(' + idx + ', ' + JSON.stringify(item).replace(/"/g, '&quot;') + '); return false;">'
                        + '<strong>' + escapeHtml(item.codigo) + '</strong> - ' + escapeHtml(item.descripcion)
                        + '<br><small class="text-muted">' + formatCOP(item.precio_unitario) + ' | IVA: ' + item.porcentaje_iva + '%</small>'
                        + '</a>'
                    );
                });
            }
            $results.show();
        });
    }, delay);
}

function seleccionarItemCatalogo(idx, item) {
    var $row = $('#itemRow_' + idx);
    $row.find('.item-codigo').val(item.codigo);
    $row.find('.item-catalogo-id').val(item.id);
    $row.find('.item-descripcion').val(item.descripcion);
    $row.find('.item-precio').val(item.precio_unitario);
    $row.find('.item-iva-check').prop('checked', parseFloat(item.porcentaje_iva) > 0);
    $row.find('.item-categoria').val(item.categoria);
    $('#itemResults_' + idx).hide();
    calcularTotalFila(idx);
}

function calcularTotalFila(idx) {
    var $row = $('#itemRow_' + idx);
    var cantidad = parseFloat($row.find('.item-cantidad').val()) || 0;
    var precio = parseFloat($row.find('.item-precio').val()) || 0;
    var subtotal = cantidad * precio;
    $row.find('.item-subtotal-display').text(formatCOP(subtotal));
    recalcularTotales();
}

function recalcularTotales() {
    var totalSubtotal = 0;
    var totalIva = 0;

    $('#tbodyItems tr').each(function() {
        var cantidad = parseFloat($(this).find('.item-cantidad').val()) || 0;
        var precio = parseFloat($(this).find('.item-precio').val()) || 0;
        var iva = $(this).find('.item-iva-check').is(':checked') ? WIZARD_CONFIG.ivaDefecto : 0;
        var sub = cantidad * precio;
        var ivaVal = sub * (iva / 100);
        totalSubtotal += sub;
        totalIva += ivaVal;
    });

    var totalGeneral = totalSubtotal + totalIva;
    $('#totalSubtotal').text(formatCOP(totalSubtotal));
    $('#totalIva').text(formatCOP(totalIva));
    $('#totalGeneral').text(formatCOP(totalGeneral));

    // Actualizar panel de pagos
    $('#pagoTotalOrden').text(formatCOP(totalGeneral));
    recalcularSaldo();

    if ($('#tbodyItems tr').length > 0) {
        marcarStepCompletado(3);
    } else {
        desmarcarStep(3);
    }
}

// ==========================================
// SECCION 3: Piezas (unificado con bosquejos)
// ==========================================

// --- Funciones de bosquejo por pieza ---

function piezaSubirArchivo(piezaIdx) {
    var input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/webp';
    input.style.display = 'none';
    input.onchange = function() { subirBosquejoParaPieza(input, 'archivo_local', piezaIdx); };
    document.body.appendChild(input);
    input.click();
    setTimeout(function() { if (input.parentNode) document.body.removeChild(input); }, 60000);
}

function piezaAbrirCamara(piezaIdx) {
    var input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.setAttribute('capture', 'environment');
    input.style.display = 'none';
    input.onchange = function() { subirBosquejoParaPieza(input, 'camara', piezaIdx); };
    document.body.appendChild(input);
    input.click();
    setTimeout(function() { if (input.parentNode) document.body.removeChild(input); }, 60000);
}

function piezaAbrirDibujo(piezaIdx) {
    window._targetPiezaForDibujo = piezaIdx;
    window._editandoBosquejoParaPieza = false;
    if (typeof initDibujoCanvas === 'function') initDibujoCanvas();
    if (typeof limpiarDibujo === 'function') limpiarDibujo();
    $('#modalDibujoTablet').modal('show');
}

function piezaEditarBosquejo(piezaIdx) {
    var $row = $('#piezaRow_' + piezaIdx);
    var bosquejoIndex = $row.attr('data-bosquejo-index');
    if (bosquejoIndex === '' || bosquejoIndex === undefined) return;
    bosquejoIndex = parseInt(bosquejoIndex);
    if (isNaN(bosquejoIndex) || !wizardState.bosquejos[bosquejoIndex]) return;
    window._targetPiezaForDibujo = piezaIdx;
    window._editandoBosquejoParaPieza = true;
    if (typeof abrirEditorBosquejo === 'function') {
        abrirEditorBosquejo(bosquejoIndex);
    }
}

function piezaRemoverBosquejo(piezaIdx) {
    var $row = $('#piezaRow_' + piezaIdx);
    $row.attr('data-bosquejo-index', '');
    $row.find('.bosquejo-thumb-container').hide();
    $row.find('.bosquejo-name-label').hide();
    $row.find('.bosquejo-empty-actions').show();
}

function piezaEditarNombreBosquejo(piezaIdx) {
    var $row = $('#piezaRow_' + piezaIdx);
    var bosquejoIndex = $row.attr('data-bosquejo-index');
    if (bosquejoIndex === '' || bosquejoIndex === undefined) return;
    bosquejoIndex = parseInt(bosquejoIndex);
    if (isNaN(bosquejoIndex) || !wizardState.bosquejos[bosquejoIndex]) return;

    var currentName = wizardState.bosquejos[bosquejoIndex].nombre || '';

    Swal.fire({
        title: 'Nombre del bosquejo',
        input: 'text',
        inputValue: currentName,
        inputPlaceholder: 'Nombre del bosquejo',
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#4A7C59',
        inputValidator: function(value) {
            if (!value || !value.trim()) {
                return 'El nombre no puede estar vacio.';
            }
        }
    }).then(function(result) {
        if (result.isConfirmed && result.value) {
            var newName = result.value.trim();
            wizardState.bosquejos[bosquejoIndex].nombre = newName;
            $row.find('.bosquejo-name-text').text(newName).attr('title', newName);
            $row.find('.pieza-bosquejo-thumb').attr('alt', newName).attr('title', newName);
            Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                title: 'Nombre actualizado', showConfirmButton: false, timer: 1500 });
        }
    });
}

function subirBosquejoParaPieza(fileInput, tipoOrigen, piezaIdx) {
    var files = fileInput.files;
    if (!files || files.length === 0) return;

    var formData = new FormData();
    formData.append('archivo', files[0]);
    formData.append('tipo_origen', tipoOrigen || 'archivo_local');
    formData.append('nombre', files[0].name.replace(/\.[^/.]+$/, ''));
    if (wizardState.ordenId) formData.append('orden_id', wizardState.ordenId);

    $.ajax({
        url: ROUTES.subirBosquejo,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': WIZARD_CONFIG.csrfToken },
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                var bosquejoIndex = wizardState.bosquejos.length;
                wizardState.bosquejos.push(response.bosquejo);
                vincularBosquejoAPieza(piezaIdx, bosquejoIndex);
                Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                    title: 'Bosquejo agregado', showConfirmButton: false, timer: 2000 });
            }
        },
        error: function(xhr) { handleAjaxError(xhr, 'subir el bosquejo'); }
    });
    fileInput.value = '';
}

function vincularBosquejoAPieza(piezaIdx, bosquejoIndex) {
    var $row = $('#piezaRow_' + piezaIdx);
    if (!$row.length) return;
    $row.attr('data-bosquejo-index', bosquejoIndex);

    var b = wizardState.bosquejos[bosquejoIndex];
    if (!b) return;
    var imgSrc = b.ruta_miniatura || b.ruta_archivo;
    if (imgSrc && !imgSrc.startsWith('http') && !imgSrc.startsWith('/') && !imgSrc.startsWith('data:')) {
        imgSrc = '/' + imgSrc;
    }

    $row.find('.bosquejo-empty-actions').hide();
    $row.find('.bosquejo-thumb-container').show();
    $row.find('.pieza-bosquejo-thumb').attr('src', imgSrc).attr('alt', b.nombre).attr('title', b.nombre);

    // Mostrar nombre del bosquejo debajo de la miniatura
    $row.find('.bosquejo-name-text').text(b.nombre).attr('title', b.nombre);
    $row.find('.bosquejo-name-label').show();
}

function agregarPiezaConBosquejo(bosquejoData) {
    var bosquejoIndex = wizardState.bosquejos.length;
    wizardState.bosquejos.push(bosquejoData);
    agregarFilaPieza();
    var piezaIdx = wizardState.piezaCounter;
    vincularBosquejoAPieza(piezaIdx, bosquejoIndex);
    generarEspecificacion(piezaIdx);
}

// --- Funciones de matriz (ahora crean piezas automaticamente) ---

function seleccionarPlantillaMatriz(plantillaId, nombre, rutaArchivo, rutaMiniatura) {
    agregarPiezaConBosquejo({
        nombre: nombre,
        tipo_origen: 'plantilla',
        ruta_archivo: rutaArchivo,
        ruta_miniatura: rutaMiniatura,
        plantilla_bosquejo_id: plantillaId
    });
    Swal.fire({ toast: true, position: 'top-end', icon: 'success',
        title: 'Pieza creada con bosquejo', showConfirmButton: false, timer: 2000 });
}

function insertarGrupoCompleto(grupoId) {
    $.get(ROUTES.subirBosquejo.replace('subir-bosquejo', 'grupos-bosquejos'), function(response) {
        if (response.success) {
            var grupo = response.grupos.find(function(g) { return g.id === grupoId; });
            if (grupo && grupo.plantillas && grupo.plantillas.length > 0) {
                grupo.plantillas.forEach(function(p) {
                    agregarPiezaConBosquejo({
                        nombre: p.nombre,
                        tipo_origen: 'grupo_plantillas',
                        ruta_archivo: p.ruta_archivo,
                        ruta_miniatura: p.ruta_miniatura || p.ruta_archivo,
                        plantilla_bosquejo_id: p.id
                    });
                });
                $('#modalBosquejoMatriz').modal('hide');
                Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                    title: grupo.plantillas.length + ' piezas creadas del grupo', showConfirmButton: false, timer: 2000 });
            }
        }
    });
}

// --- Funciones legacy (stubs para compatibilidad) ---

function renderizarGrillaBosquejos() { /* Deprecado: bosquejos ahora en celdas de piezas */ }
function actualizarSelectBosquejosPiezas() { /* Deprecado: sin select dropdown */ }
function generarOpcionesBosquejos() { return ''; }

function abrirSelectorArchivo() {
    // Funcion legacy - mantener para posible uso
    var input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/webp';
    input.style.display = 'none';
    input.onchange = function() {
        var files = input.files;
        if (!files || files.length === 0) return;
        var formData = new FormData();
        formData.append('archivo', files[0]);
        formData.append('tipo_origen', 'archivo_local');
        formData.append('nombre', files[0].name.replace(/\.[^/.]+$/, ''));
        if (wizardState.ordenId) formData.append('orden_id', wizardState.ordenId);
        $.ajax({
            url: ROUTES.subirBosquejo, method: 'POST',
            headers: { 'X-CSRF-TOKEN': WIZARD_CONFIG.csrfToken },
            data: formData, processData: false, contentType: false,
            success: function(r) { if (r.success) wizardState.bosquejos.push(r.bosquejo); },
            error: function(xhr) { handleAjaxError(xhr, 'subir el bosquejo'); }
        });
        input.value = '';
    };
    document.body.appendChild(input);
    input.click();
    setTimeout(function() { if (input.parentNode) document.body.removeChild(input); }, 60000);
}

function abrirCamara() {
    // Funcion legacy
    var input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.setAttribute('capture', 'environment');
    input.style.display = 'none';
    input.onchange = function() {
        var files = input.files;
        if (!files || files.length === 0) return;
        var formData = new FormData();
        formData.append('archivo', files[0]);
        formData.append('tipo_origen', 'camara');
        formData.append('nombre', files[0].name.replace(/\.[^/.]+$/, ''));
        if (wizardState.ordenId) formData.append('orden_id', wizardState.ordenId);
        $.ajax({
            url: ROUTES.subirBosquejo, method: 'POST',
            headers: { 'X-CSRF-TOKEN': WIZARD_CONFIG.csrfToken },
            data: formData, processData: false, contentType: false,
            success: function(r) { if (r.success) wizardState.bosquejos.push(r.bosquejo); },
            error: function(xhr) { handleAjaxError(xhr, 'subir el bosquejo'); }
        });
        input.value = '';
    };
    document.body.appendChild(input);
    input.click();
    setTimeout(function() { if (input.parentNode) document.body.removeChild(input); }, 60000);
}

// --- Funciones de pieza (tabla) ---

function agregarFilaPieza() {
    wizardState.piezaCounter++;
    var idx = wizardState.piezaCounter;
    var letra = obtenerLetraPieza($('#tbodyPiezas tr').length);
    var nombre = 'Pieza ' + letra;

    // Opciones de calibre
    var calOpts = '<option value="">--</option>';
    if (WIZARD_CONFIG.calibres && Array.isArray(WIZARD_CONFIG.calibres)) {
        WIZARD_CONFIG.calibres.forEach(function(c) {
            var label = typeof c === 'object' ? (c.calibre || c.label || c) : c;
            var value = typeof c === 'object' ? (c.calibre || c.value || c) : c;
            calOpts += '<option value="' + escapeHtml(String(value)) + '">' + escapeHtml(String(label)) + '</option>';
        });
    }

    var html = '<tr id="piezaRow_' + idx + '" data-idx="' + idx + '" data-bosquejo-index="">'
        + '<td class="pieza-bosquejo-cell text-center">'
        + '  <div class="bosquejo-empty-actions">'
        + '    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="piezaSubirArchivo(' + idx + ')" title="Subir archivo"><i class="bi bi-upload"></i></button>'
        + '    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="piezaAbrirCamara(' + idx + ')" title="Camara"><i class="bi bi-camera"></i></button>'
        + '    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="piezaAbrirDibujo(' + idx + ')" title="Dibujar"><i class="bi bi-pencil-square"></i></button>'
        + '  </div>'
        + '  <div class="bosquejo-thumb-container" style="display:none;">'
        + '    <img src="" class="pieza-bosquejo-thumb" alt="">'
        + '    <button type="button" class="bosquejo-edit-overlay" onclick="piezaEditarBosquejo(' + idx + ')" title="Editar"><i class="bi bi-pencil"></i></button>'
        + '    <button type="button" class="bosquejo-remove-overlay" onclick="piezaRemoverBosquejo(' + idx + ')" title="Quitar"><i class="bi bi-x-lg"></i></button>'
        + '  </div>'
        + '  <div class="bosquejo-name-label" style="display:none;">'
        + '    <span class="bosquejo-name-text" title="Click para editar nombre" onclick="piezaEditarNombreBosquejo(' + idx + ')"></span>'
        + '    <button type="button" class="bosquejo-name-edit-btn" onclick="piezaEditarNombreBosquejo(' + idx + ')" title="Editar nombre"><i class="bi bi-pencil-fill"></i></button>'
        + '  </div>'
        + '</td>'
        + '<td class="text-center text-muted"><span class="pieza-num">' + ($('#tbodyPiezas tr').length + 1) + '</span></td>'
        + '<td><input type="text" class="form-control form-control-sm pieza-nombre" value="' + nombre + '" onchange="generarEspecificacion(' + idx + ')"></td>'
        + '<td><input type="number" class="form-control form-control-sm text-center pieza-cantidad" value="1" min="1" onchange="generarEspecificacion(' + idx + ')"></td>'
        + '<td class="position-relative">'
        + '  <input type="text" class="form-control form-control-sm pieza-material" data-idx="' + idx + '" placeholder="Buscar..." autocomplete="off" onkeyup="buscarMaterialPieza(this)" onchange="generarEspecificacion(' + idx + ')">'
        + '  <div class="material-autocomplete-results list-group shadow-sm" id="materialResults_' + idx + '" style="display:none; position:absolute; z-index:1050; width:100%; max-height:200px; overflow-y:auto;"></div>'
        + '</td>'
        + '<td><select class="form-select form-select-sm pieza-calibre" onchange="generarEspecificacion(' + idx + ')">' + calOpts + '</select></td>'
        + '<td class="small text-muted pieza-especificacion">1 - ' + nombre + '</td>'
        + '<td><input type="text" class="form-control form-control-sm pieza-notas" placeholder="Notas..."></td>'
        + '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="eliminarFilaPieza(' + idx + ')"><i class="bi bi-trash"></i></button></td>'
        + '</tr>';

    $('#tbodyPiezas').append(html);
    $('#tablaPiezas').show();
    $('#piezasVacio').hide();
    actualizarVisibilidadOperario();
    renumerarFilasPiezas();
    marcarStepCompletado(2);
}

function eliminarFilaPieza(idx) {
    $('#piezaRow_' + idx).remove();
    renumerarFilasPiezas();
    actualizarVisibilidadOperario();
    if ($('#tbodyPiezas tr').length === 0) {
        $('#tablaPiezas').hide();
        $('#piezasVacio').show();
        desmarcarStep(2);
    }
}

function renumerarFilasPiezas() {
    $('#tbodyPiezas tr').each(function(i) {
        $(this).find('.pieza-num').text(i + 1);
    });
}

function obtenerLetraPieza(index) {
    var letra = '';
    var n = index + 1;
    while (n > 0) {
        n--;
        letra = String.fromCharCode(65 + (n % 26)) + letra;
        n = Math.floor(n / 26);
    }
    return letra;
}

function generarEspecificacion(idx) {
    var $row = $('#piezaRow_' + idx);
    var cantidad = $row.find('.pieza-cantidad').val() || '1';
    var nombre = $row.find('.pieza-nombre').val() || '';
    var calibre = $row.find('.pieza-calibre').val() || '';
    var material = $row.find('.pieza-material').val() || '';

    var partes = [cantidad];
    if (nombre) partes.push(nombre);
    if (calibre) partes.push(calibre);
    if (material) partes.push(material);

    $row.find('.pieza-especificacion').text(partes.join(' - '));
}

// --- Autocomplete de Material (client-side) ---
function buscarMaterialPieza(input) {
    var idx = $(input).data('idx');
    var q = $(input).val().trim().toLowerCase();
    var $results = $('#materialResults_' + idx);

    var materiales = WIZARD_CONFIG.materiales || [];
    var filtrados = materiales.filter(function(m) {
        return !q || m.toLowerCase().indexOf(q) !== -1;
    });

    $results.empty();
    if (filtrados.length === 0) {
        $results.append('<div class="list-group-item text-muted small py-1">Sin resultados</div>');
    } else {
        filtrados.forEach(function(m) {
            $results.append(
                '<a href="#" class="list-group-item list-group-item-action py-1 small" '
                + 'onclick="seleccionarMaterialPieza(' + idx + ', \'' + escapeHtml(m) + '\'); return false;">'
                + escapeHtml(m)
                + '</a>'
            );
        });
    }
    $results.show();
}

function seleccionarMaterialPieza(idx, material) {
    var $row = $('#piezaRow_' + idx);
    $row.find('.pieza-material').val(material);
    $('#materialResults_' + idx).hide();
    generarEspecificacion(idx);
}

function actualizarVisibilidadOperario() {
    var tienePiezas = $('#tbodyPiezas tr').length > 0;
    if (tienePiezas) {
        $('#operarioInfo').hide();
        $('#operarioSelector').show();
    } else {
        $('#operarioInfo').show();
        $('#operarioSelector').hide();
        $('#operario_id').val('');
    }
}

// ==========================================
// SECCION 6: Pagos
// ==========================================
function agregarFilaPago() {
    wizardState.pagoCounter++;
    var idx = wizardState.pagoCounter;

    var html = '<div class="pago-row" id="pagoRow_' + idx + '">'
        + '<div class="flex-grow-1 row g-2 align-items-center">'
        + '  <div class="col-sm-4">'
        + '    <div class="input-group input-group-sm">'
        + '      <span class="input-group-text">$</span>'
        + '      <input type="number" class="form-control pago-monto" placeholder="Monto" min="0.01" step="0.01" onchange="recalcularSaldo()" onkeyup="recalcularSaldo()">'
        + '    </div>'
        + '  </div>'
        + '  <div class="col-sm-4">'
        + '    <select class="form-select form-select-sm pago-metodo">'
        + '      <option value="efectivo">Efectivo</option>'
        + '      <option value="nequi">Nequi</option>'
        + '      <option value="transferencia">Transferencia</option>'
        + '      <option value="tarjeta">Tarjeta</option>'
        + '      <option value="otro">Otro</option>'
        + '    </select>'
        + '  </div>'
        + '  <div class="col-sm-3">'
        + '    <input type="text" class="form-control form-control-sm pago-referencia" placeholder="Referencia">'
        + '  </div>'
        + '  <div class="col-sm-1 text-center">'
        + '    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="eliminarFilaPago(' + idx + ')"><i class="bi bi-trash"></i></button>'
        + '  </div>'
        + '</div>'
        + '</div>';

    $('#pagosContainer').append(html);
    $('#pagosVacio').hide();
    $('#panelSaldo').show();
    recalcularSaldo();
    marcarStepCompletado(6);
}

function eliminarFilaPago(idx) {
    $('#pagoRow_' + idx).remove();
    recalcularSaldo();
    if ($('#pagosContainer .pago-row').length === 0) {
        $('#pagosVacio').show();
        $('#panelSaldo').hide();
        desmarcarStep(6);
    }
}

function recalcularSaldo() {
    var totalAbonado = 0;
    $('#pagosContainer .pago-monto').each(function() {
        totalAbonado += parseFloat($(this).val()) || 0;
    });

    // Obtener total general de items
    var totalGeneral = 0;
    $('#tbodyItems tr').each(function() {
        var cantidad = parseFloat($(this).find('.item-cantidad').val()) || 0;
        var precio = parseFloat($(this).find('.item-precio').val()) || 0;
        var iva = $(this).find('.item-iva-check').is(':checked') ? WIZARD_CONFIG.ivaDefecto : 0;
        var sub = cantidad * precio;
        totalGeneral += sub + (sub * iva / 100);
    });

    var saldo = totalGeneral - totalAbonado;

    $('#pagoTotalOrden').text(formatCOP(totalGeneral));
    $('#pagoTotalAbonado').text(formatCOP(totalAbonado));
    $('#pagoSaldo').text(formatCOP(saldo));

    // Color del saldo
    if (saldo <= 0) {
        $('#pagoSaldo').removeClass('text-danger').addClass('text-success');
    } else {
        $('#pagoSaldo').removeClass('text-success').addClass('text-danger');
    }
}

// ==========================================
// Step Navigation
// ==========================================
function irASeccion(num) {
    var sectionId = ['seccionCliente', 'seccionBosquejosPiezas', 'seccionItems', 'seccionFirma', 'seccionOperario', 'seccionPagos', 'seccionFechas'];
    var target = $('#' + sectionId[num - 1]);
    if (target.length) {
        $('html, body').animate({ scrollTop: target.offset().top - 140 }, 300);
    }
    // Actualizar step activo
    $('.wizard-step').removeClass('active');
    $('.wizard-step[data-step="' + num + '"]').addClass('active');
}

function marcarStepCompletado(num) {
    $('.wizard-step[data-step="' + num + '"]').addClass('completed');
}

function desmarcarStep(num) {
    $('.wizard-step[data-step="' + num + '"]').removeClass('completed');
}

// ==========================================
// Recopilacion de Datos
// ==========================================
function recopilarDatosFormulario() {
    var items = [];
    $('#tbodyItems tr').each(function() {
        items.push({
            catalogo_item_id: $(this).find('.item-catalogo-id').val() || null,
            codigo: $(this).find('.item-codigo').val(),
            descripcion: $(this).find('.item-descripcion').val(),
            cantidad: parseFloat($(this).find('.item-cantidad').val()) || 0,
            precio_unitario: parseFloat($(this).find('.item-precio').val()) || 0,
            porcentaje_iva: $(this).find('.item-iva-check').is(':checked') ? WIZARD_CONFIG.ivaDefecto : 0,
            categoria: $(this).find('.item-categoria').val() || 'servicio'
        });
    });

    var piezas = [];
    $('#tbodyPiezas tr').each(function() {
        var bosquejoIdx = $(this).attr('data-bosquejo-index');
        piezas.push({
            nombre: $(this).find('.pieza-nombre').val(),
            cantidad: parseInt($(this).find('.pieza-cantidad').val()) || 1,
            material: $(this).find('.pieza-material').val() || null,
            calibre: $(this).find('.pieza-calibre').val() || null,
            notas: $(this).find('.pieza-notas').val() || null,
            bosquejo_index: (bosquejoIdx !== '' && bosquejoIdx !== undefined) ? parseInt(bosquejoIdx) : null
        });
    });

    // Filtrar bosquejos: solo enviar los referenciados por piezas
    var referencedIndices = {};
    piezas.forEach(function(p) {
        if (p.bosquejo_index !== null) referencedIndices[p.bosquejo_index] = true;
    });
    var bosquejosToSend = [];
    var indexMap = {};
    Object.keys(referencedIndices).sort(function(a,b){ return a-b; }).forEach(function(oldIdx) {
        var newIdx = bosquejosToSend.length;
        indexMap[parseInt(oldIdx)] = newIdx;
        bosquejosToSend.push(wizardState.bosquejos[parseInt(oldIdx)]);
    });
    // Remapear indices de piezas
    piezas.forEach(function(p) {
        if (p.bosquejo_index !== null && indexMap[p.bosquejo_index] !== undefined) {
            p.bosquejo_index = indexMap[p.bosquejo_index];
        }
    });

    var pagos = [];
    $('#pagosContainer .pago-row').each(function() {
        var monto = parseFloat($(this).find('.pago-monto').val()) || 0;
        if (monto > 0) {
            pagos.push({
                monto: monto,
                metodo_pago: $(this).find('.pago-metodo').val(),
                referencia_pago: $(this).find('.pago-referencia').val() || null
            });
        }
    });

    return {
        orden_id: wizardState.ordenId,
        cliente_id: wizardState.clienteId,
        fecha_entrega: $('#fecha_entrega').val() || null,
        hora_entrega: $('#hora_entrega').val() || null,
        notas: $('#notas').val() || null,
        items: items,
        bosquejos: bosquejosToSend,
        piezas: piezas,
        operario_id: $('#operario_id').val() || null,
        pagos: pagos,
        firma_data: wizardState.firmaData || obtenerFirmaData()
    };
}

// ==========================================
// Guardar y Generar
// ==========================================
function guardarOrden(isAutoSave) {
    var data = recopilarDatosFormulario();

    if (!data.cliente_id && !isAutoSave) {
        Swal.fire('Error', 'Debe seleccionar un cliente para guardar la orden.', 'error');
        irASeccion(1);
        return;
    }

    // Si no tiene cliente y es autosave, no guardar
    if (!data.cliente_id && isAutoSave) return;

    // Deshabilitar botones
    if (!isAutoSave) {
        $('#btnGuardar').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Guardando...');
    }

    // Determinar metodo segun modo edicion
    var ajaxMethod = (typeof EDIT_MODE !== 'undefined' && EDIT_MODE) ? 'PUT' : 'POST';

    $.ajax({
        url: ROUTES.guardar,
        method: ajaxMethod,
        headers: { 'X-CSRF-TOKEN': WIZARD_CONFIG.csrfToken },
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
            if (response.success) {
                wizardState.ordenId = response.orden_id;
                $('#orden_id').val(response.orden_id);
                wizardState.lastSavedHash = JSON.stringify(recopilarDatosFormulario());

                if (isAutoSave) {
                    var ahora = new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
                    $('#autoguardadoTexto').text('Auto-guardado ' + ahora);
                    $('#autoguardadoIndicator').show();
                } else {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                        title: 'La orden ha sido guardada exitosamente.', showConfirmButton: false, timer: 3000 });
                }
            }
        },
        error: function(xhr) {
            if (!isAutoSave) handleAjaxError(xhr, 'guardar la orden');
        },
        complete: function() {
            if (!isAutoSave) {
                $('#btnGuardar').prop('disabled', false).html('<i class="bi bi-save me-1"></i> Guardar Orden');
            }
        }
    });
}

function generarOrden() {
    var data = recopilarDatosFormulario();

    // Validacion client-side
    var errores = validarParaGenerar(data);
    if (errores.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Falta diligenciar informacion para poder GENERAR ORDEN',
            html: '<ul class="text-start">' + errores.map(function(e) { return '<li>' + e + '</li>'; }).join('') + '</ul>'
        });
        return;
    }

    // Confirmacion con boton habilitado tras 1 segundo
    Swal.fire({
        title: 'Esta seguro de generar orden?',
        text: 'Se asignara un numero consecutivo. Esta accion no se puede revertir.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4A7C59',
        confirmButtonText: 'Generar Orden',
        cancelButtonText: 'Cancelar',
        didOpen: function() {
            var btn = Swal.getConfirmButton();
            btn.disabled = true;
            setTimeout(function() { btn.disabled = false; }, 1000);
        }
    }).then(function(result) {
        if (!result.isConfirmed) return;

        $('#btnGenerar').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Generando...');

        $.ajax({
            url: ROUTES.generar,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': WIZARD_CONFIG.csrfToken },
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Orden Generada',
                        text: response.message,
                        confirmButtonColor: '#4A7C59',
                        confirmButtonText: 'Aceptar'
                    }).then(function() {
                        window.location.href = ROUTES.panel;
                    });
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr, 'generar la orden');
            },
            complete: function() {
                $('#btnGenerar').prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Generar Orden');
            }
        });
    });
}

function validarParaGenerar(data) {
    var errores = [];
    if (!data.cliente_id) errores.push('Debe seleccionar un cliente.');
    if (!data.items || data.items.length === 0) {
        errores.push('Debe agregar al menos un item.');
    } else {
        data.items.forEach(function(item, i) {
            var num = i + 1;
            if (!item.descripcion) errores.push('Item ' + num + ': falta descripcion.');
            if (!item.cantidad || item.cantidad <= 0) errores.push('Item ' + num + ': cantidad debe ser mayor a 0.');
            if (item.precio_unitario < 0) errores.push('Item ' + num + ': precio no valido.');
        });
    }
    if (data.piezas && data.piezas.length > 0 && !data.operario_id) {
        errores.push('Debe seleccionar un operario cuando hay piezas.');
    }
    return errores;
}

// ==========================================
// Auto-guardado
// ==========================================
function initAutoSave() {
    var interval = WIZARD_CONFIG.autoSaveInterval || 300000; // 5 min default
    var idleTimer = null;

    function resetIdleTimer() {
        clearTimeout(idleTimer);
        idleTimer = setTimeout(function() {
            if (wizardState.clienteId) {
                var currentHash = JSON.stringify(recopilarDatosFormulario());
                if (currentHash !== wizardState.lastSavedHash) {
                    guardarOrden(true);
                }
            }
        }, interval);
    }

    $(document).on('keypress click change input', resetIdleTimer);
}
