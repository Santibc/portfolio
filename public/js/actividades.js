/**
 * Actividades - DataTables initialization
 * Usado para vista personal y global de registro de actividades.
 */
function initActividadesTable(config) {
    var isPersonal = config.personal;

    var columns = isPersonal
        ? [
            { data: 'fecha_formatted', name: 'created_at', width: '150px' },
            { data: 'accion_badge', name: 'accion', width: '200px' },
            { data: 'orden_link', name: 'orden_id', orderable: false, searchable: false, width: '120px' },
            { data: 'descripcion', name: 'descripcion' },
            { data: 'detalle_btn', name: 'detalle_btn', orderable: false, searchable: false, className: 'text-center', width: '80px' }
        ]
        : [
            { data: 'fecha_formatted', name: 'created_at', width: '150px' },
            { data: 'usuario_nombre', name: 'usuario.name', width: '140px' },
            { data: 'usuario_rol', name: 'usuario_rol', orderable: false, searchable: false, className: 'text-center', width: '110px' },
            { data: 'accion_badge', name: 'accion', width: '200px' },
            { data: 'orden_link', name: 'orden_id', orderable: false, searchable: false, width: '120px' },
            { data: 'descripcion', name: 'descripcion' },
            { data: 'detalle_btn', name: 'detalle_btn', orderable: false, searchable: false, className: 'text-center', width: '80px' }
        ];

    var table = $('#actividadesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: config.ajaxUrl,
            data: function(d) {
                d.fecha_desde = $('#filtroFechaDesde').val();
                d.fecha_hasta = $('#filtroFechaHasta').val();
                d.accion = $('#filtroAccion').val();
                if (!isPersonal) {
                    d.usuario_id = $('#filtroUsuario').val();
                }
            }
        },
        columns: columns,
        dom: '<"d-flex flex-wrap align-items-center justify-content-between mb-2"<"d-flex align-items-center gap-2"lB>f>rt<"d-flex justify-content-between"ip>',
        buttons: [
            { extend: 'colvis', text: '<i class="bi bi-layout-three-columns"></i> Columnas', className: 'btn btn-sm btn-outline-secondary' }
        ],
        order: [[0, 'desc']],
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        drawCallback: function(settings) {
            var total = settings._iRecordsTotal || 0;
            $('#totalRegistros').text(total + ' registro' + (total !== 1 ? 's' : ''));
        }
    });

    // Filtrar
    $('#btnFiltrar').on('click', function() {
        table.draw();
    });

    // Limpiar filtros
    $('#btnLimpiar').on('click', function() {
        $('#filtroFechaDesde, #filtroFechaHasta').val('');
        $('#filtroAccion').val('');
        if (!isPersonal) {
            $('#filtroUsuario').val('');
        }
        table.draw();
    });

    // Enter en inputs para filtrar
    $('#filtroFechaDesde, #filtroFechaHasta').on('keypress', function(e) {
        if (e.which === 13) table.draw();
    });

    // Cambio en selects para filtrar automaticamente
    $('#filtroAccion').on('change', function() {
        table.draw();
    });

    if (!isPersonal) {
        $('#filtroUsuario').on('change', function() {
            table.draw();
        });
    }

    // Modal de detalle - delegado en document para sobrevivir redraws
    $(document).off('click.actividadesDetalle').on('click.actividadesDetalle', '.btn-ver-detalle', function() {
        var $btn = $(this);
        var raw = $btn.attr('data-detalle');
        var accion = $btn.attr('data-accion') || '';
        var fecha = $btn.attr('data-fecha') || '';
        var usuario = $btn.attr('data-usuario') || '';

        var datos;
        try {
            datos = raw ? JSON.parse(raw) : {};
        } catch (e) {
            datos = { _error: 'No se pudo parsear el detalle.', _raw: raw };
        }

        renderDetalleActividad(datos, { accion: accion, fecha: fecha, usuario: usuario });
        var modalEl = document.getElementById('modalDetalleActividad');
        if (modalEl && window.bootstrap) {
            var instance = bootstrap.Modal.getOrCreateInstance(modalEl);
            instance.show();
        }
    });
}

function renderDetalleActividad(datos, meta) {
    var $titulo = $('#modalDetalleActividadTitulo');
    var $subtitulo = $('#modalDetalleActividadSubtitulo');
    var $body = $('#modalDetalleActividadBody');

    $titulo.text(meta.accion || 'Detalle de actividad');

    var sub = [];
    if (meta.fecha) sub.push('<i class="bi bi-clock me-1"></i>' + escapeHtml(meta.fecha));
    if (meta.usuario && meta.usuario !== '-') sub.push('<i class="bi bi-person me-1"></i>' + escapeHtml(meta.usuario));
    $subtitulo.html(sub.join(' &nbsp;&middot;&nbsp; '));

    if (!datos || typeof datos !== 'object') {
        $body.html('<div class="alert alert-secondary mb-0">Sin datos de detalle disponibles.</div>');
        return;
    }

    var html = '';

    var tipoCambio = datos.tipo_cambio || null;
    var modelo = datos.modelo || null;
    var modeloId = datos.modelo_id || null;
    var cambios = datos.cambios || null;

    // Encabezado del registro afectado
    if (modelo || modeloId) {
        html += '<div class="mb-3">';
        if (modelo) {
            html += '<span class="badge bg-light text-dark border me-2"><i class="bi bi-database me-1"></i>' + escapeHtml(modelo) + '</span>';
        }
        if (modeloId) {
            html += '<span class="badge bg-light text-dark border">ID: ' + escapeHtml(String(modeloId)) + '</span>';
        }
        html += '</div>';
    }

    if (tipoCambio === 'create') {
        html += '<div class="alert alert-success py-2 mb-3"><i class="bi bi-plus-circle me-1"></i>Registro creado</div>';
        html += renderTablaSnapshot(cambios, 'despues');
    } else if (tipoCambio === 'delete') {
        html += '<div class="alert alert-danger py-2 mb-3"><i class="bi bi-trash me-1"></i>Registro eliminado</div>';
        html += renderTablaSnapshot(cambios, 'antes');
    } else if (tipoCambio === 'update') {
        if (cambios && Object.keys(cambios).length > 0) {
            html += '<div class="alert alert-info py-2 mb-3"><i class="bi bi-pencil-square me-1"></i>Campos modificados</div>';
            html += renderTablaDiff(cambios);
        } else {
            html += '<div class="alert alert-secondary py-2 mb-3">No se detectaron cambios en los campos del registro.</div>';
        }
    }

    // Datos adicionales (cualquier llave que no sea parte del envelope estandar)
    var extras = {};
    Object.keys(datos).forEach(function(k) {
        if (['tipo_cambio', 'modelo', 'modelo_id', 'cambios'].indexOf(k) === -1) {
            extras[k] = datos[k];
        }
    });

    if (Object.keys(extras).length > 0) {
        html += '<h6 class="mt-4 mb-2 text-muted"><i class="bi bi-info-circle me-1"></i>Datos adicionales</h6>';
        html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><tbody>';
        Object.keys(extras).forEach(function(k) {
            html += '<tr><th class="bg-light" style="width:35%">' + escapeHtml(k) + '</th><td>' + formatValor(extras[k]) + '</td></tr>';
        });
        html += '</tbody></table></div>';
    }

    if (!tipoCambio && Object.keys(extras).length === 0) {
        html += '<div class="alert alert-secondary mb-0">Sin detalle estructurado.</div>';
    }

    $body.html(html);
}

function renderTablaDiff(cambios) {
    var html = '<div class="table-responsive"><table class="table table-sm table-bordered align-middle mb-0">';
    html += '<thead class="table-light"><tr>';
    html += '<th style="width:30%">Campo</th>';
    html += '<th style="width:35%">Antes</th>';
    html += '<th style="width:35%">Despues</th>';
    html += '</tr></thead><tbody>';
    Object.keys(cambios).forEach(function(campo) {
        var c = cambios[campo] || {};
        html += '<tr>';
        html += '<td class="fw-semibold text-dark">' + escapeHtml(campo) + '</td>';
        html += '<td class="text-danger-emphasis bg-danger-subtle">' + formatValor(c.antes) + '</td>';
        html += '<td class="text-success-emphasis bg-success-subtle">' + formatValor(c.despues) + '</td>';
        html += '</tr>';
    });
    html += '</tbody></table></div>';
    return html;
}

function renderTablaSnapshot(cambios, lado) {
    if (!cambios || Object.keys(cambios).length === 0) {
        return '<div class="alert alert-secondary mb-0">Sin atributos disponibles.</div>';
    }
    var html = '<div class="table-responsive"><table class="table table-sm table-bordered align-middle mb-0">';
    html += '<thead class="table-light"><tr>';
    html += '<th style="width:35%">Campo</th>';
    html += '<th>Valor</th>';
    html += '</tr></thead><tbody>';
    Object.keys(cambios).forEach(function(campo) {
        var c = cambios[campo] || {};
        html += '<tr>';
        html += '<td class="fw-semibold text-dark">' + escapeHtml(campo) + '</td>';
        html += '<td>' + formatValor(c[lado]) + '</td>';
        html += '</tr>';
    });
    html += '</tbody></table></div>';
    return html;
}

function formatValor(v) {
    if (v === null || v === undefined || v === '') {
        return '<em class="text-muted">vacio</em>';
    }
    if (typeof v === 'boolean') {
        return v ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>';
    }
    if (typeof v === 'object') {
        return '<pre class="mb-0 small" style="max-height:200px;overflow:auto;">' + escapeHtml(JSON.stringify(v, null, 2)) + '</pre>';
    }
    return escapeHtml(String(v));
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
