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
            { data: 'descripcion', name: 'descripcion' }
        ]
        : [
            { data: 'fecha_formatted', name: 'created_at', width: '150px' },
            { data: 'usuario_nombre', name: 'usuario.name', width: '140px' },
            { data: 'usuario_rol', name: 'usuario_rol', orderable: false, searchable: false, className: 'text-center', width: '110px' },
            { data: 'accion_badge', name: 'accion', width: '200px' },
            { data: 'orden_link', name: 'orden_id', orderable: false, searchable: false, width: '120px' },
            { data: 'descripcion', name: 'descripcion' }
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
}
