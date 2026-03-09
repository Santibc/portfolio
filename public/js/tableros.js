/**
 * Manzer - Tablero Kanban JavaScript
 * Drag & drop, AJAX, card modal, filters
 */
document.addEventListener('DOMContentLoaded', function () {

    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const TABLERO_ID = window.TABLERO_ID;
    const PUEDE_EDITAR = window.PUEDE_EDITAR;
    let currentTarjetaId = null;
    let currentTarjetaData = null;

    // =====================================================
    // SweetAlert helper (handles Bootstrap modal focus trap)
    // =====================================================
    function swalInModal(options) {
        document.getElementById('modalTitulo')?.blur();
        return Swal.fire({
            ...options,
            didOpen: (popup) => {
                // Deactivate Bootstrap focus trap so SweetAlert input works
                const modal = bootstrap.Modal.getInstance(document.getElementById('tarjetaModal'));
                if (modal?._focustrap) modal._focustrap.deactivate();
                setTimeout(() => popup.querySelector('.swal2-input, .swal2-select')?.focus(), 50);
                if (options.didOpen) options.didOpen(popup);
            },
            didClose: () => {
                // Reactivate Bootstrap focus trap
                const modal = bootstrap.Modal.getInstance(document.getElementById('tarjetaModal'));
                if (modal?._focustrap) modal._focustrap.activate();
                if (options.didClose) options.didClose();
            },
        });
    }

    // =====================================================
    // AJAX helper
    // =====================================================
    function ajax(url, method, data) {
        const opts = {
            method: method,
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            }
        };

        if (data instanceof FormData) {
            opts.body = data;
        } else if (data) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(data);
        }

        return fetch(url, opts).then(r => {
            if (!r.ok) throw r;
            return r.json();
        });
    }

    // =====================================================
    // DRAG & DROP: Columns
    // =====================================================
    if (PUEDE_EDITAR) {
        const columnasContainer = document.getElementById('columnasContainer');
        if (columnasContainer) {
            new Sortable(columnasContainer, {
                animation: 200,
                handle: '.columna-header',
                draggable: '.tablero-columna',
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                filter: '.tablero-columna-nueva',
                preventOnFilter: false,
                onEnd: function () {
                    const columnas = columnasContainer.querySelectorAll('.tablero-columna');
                    const posiciones = {};
                    columnas.forEach((col, i) => {
                        posiciones[col.dataset.columnaId] = i;
                    });

                    ajax(`/tableros/${TABLERO_ID}/columnas/reordenar`, 'POST', { posiciones })
                        .catch(() => showToast('Error al reordenar columnas', 'error'));
                }
            });
        }

        // DRAG & DROP: Cards
        document.querySelectorAll('.columna-tarjetas').forEach(initSortableCards);
    }

    function initSortableCards(container) {
        if (!PUEDE_EDITAR) return;
        new Sortable(container, {
            group: 'tarjetas',
            animation: 200,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: function (evt) {
                const tarjetaId = evt.item.dataset.tarjetaId;
                const nuevaColumnaId = evt.to.dataset.columnaId;
                const nuevaPosicion = evt.newIndex;

                ajax(`/tarjetas/${tarjetaId}/mover`, 'POST', {
                    columna_id: parseInt(nuevaColumnaId),
                    posicion: nuevaPosicion
                }).catch(() => {
                    showToast('Error al mover tarjeta', 'error');
                    location.reload();
                });
            }
        });
    }

    // =====================================================
    // CARD MODAL
    // =====================================================
    window.abrirTarjetaModal = function (tarjetaId) {
        currentTarjetaId = tarjetaId;

        ajax(`/tarjetas/${tarjetaId}`, 'GET').then(data => {
            currentTarjetaData = data.tarjeta;
            poblarModal(data);
            new bootstrap.Modal(document.getElementById('tarjetaModal')).show();
        }).catch(() => showToast('Error al cargar tarjeta', 'error'));
    };

    function poblarModal(data) {
        const t = data.tarjeta;

        // Title
        document.getElementById('modalTitulo').value = t.titulo;
        document.getElementById('modalColumnaNombre').textContent = data.columna_nombre;

        // Labels
        const labelsHtml = (t.etiquetas || []).map(e =>
            `<span class="etiqueta-chip" style="background:${e.color}">${e.nombre}</span>`
        ).join('');
        document.getElementById('modalEtiquetas').innerHTML = labelsHtml || '<span class="text-muted small">Sin etiquetas</span>';

        // Members
        const membersHtml = (t.usuarios || []).map(u => {
            const initials = u.name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
            return `<div class="avatar-mini" title="${u.name}">${initials}</div>`;
        }).join('');
        document.getElementById('modalMiembros').innerHTML = membersHtml || '<span class="text-muted small">Sin miembros</span>';

        // Due date
        const fechaDisplay = document.getElementById('modalFechaDisplay');
        if (t.fecha_vencimiento) {
            const fecha = new Date(t.fecha_vencimiento);
            const badgeClass = {
                'vencida': 'badge-vencida',
                'urgente': 'badge-urgente',
                'pronto': 'badge-pronto',
                'ok': 'badge-ok',
                'completada': 'badge-completada'
            }[data.estado_vencimiento] || '';
            fechaDisplay.innerHTML = `<span class="tarjeta-badge ${badgeClass}"><i class="bi bi-clock"></i> ${fecha.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>`;
        } else {
            fechaDisplay.innerHTML = '<span class="text-muted">Sin fecha</span>';
        }

        // Priority
        const prioridadMap = {
            'alta': '<span class="badge bg-danger">Alta</span>',
            'media': '<span class="badge bg-warning text-dark">Media</span>',
            'baja': '<span class="badge bg-secondary">Baja</span>'
        };
        document.getElementById('modalPrioridadDisplay').innerHTML = prioridadMap[t.prioridad] || prioridadMap['media'];

        // Description
        document.getElementById('modalDescripcion').value = t.descripcion || '';

        // Date picker
        if (t.fecha_vencimiento) {
            const d = new Date(t.fecha_vencimiento);
            const pad = n => String(n).padStart(2, '0');
            const val = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            const picker = document.getElementById('fechaVencimientoInput');
            if (picker) picker.value = val;
        }

        // Checklists
        renderChecklists(t.checklists || []);

        // Attachments
        renderAdjuntos(t.adjuntos || []);

        // Comments
        renderComentarios(t.comentarios || []);
    }

    // Title save helper (used by Enter key and modal close)
    function guardarTituloTarjeta() {
        if (!currentTarjetaId || !PUEDE_EDITAR) return;
        const nuevoTitulo = document.getElementById('modalTitulo')?.value.trim();
        if (nuevoTitulo && nuevoTitulo !== currentTarjetaData?.titulo) {
            ajax(`/tarjetas/${currentTarjetaId}`, 'PUT', { titulo: nuevoTitulo }).then(data => {
                currentTarjetaData.titulo = nuevoTitulo;
                updateCardInBoard(data);
            });
        }
    }

    // Save title on Enter key
    document.getElementById('modalTitulo')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.blur();
            guardarTituloTarjeta();
        }
    });

    // Save title when modal closes
    document.getElementById('tarjetaModal')?.addEventListener('hide.bs.modal', function () {
        guardarTituloTarjeta();
    });

    // Description
    document.getElementById('modalDescripcion')?.addEventListener('focus', function () {
        const btn = document.getElementById('btnGuardarDescripcion');
        if (btn) btn.style.display = 'inline-block';
    });

    window.guardarDescripcion = function () {
        if (!currentTarjetaId) return;
        const desc = document.getElementById('modalDescripcion').value;
        ajax(`/tarjetas/${currentTarjetaId}`, 'PUT', { descripcion: desc }).then(data => {
            document.getElementById('btnGuardarDescripcion').style.display = 'none';
            updateCardInBoard(data);
            showToast('Descripcion guardada');
        });
    };

    // =====================================================
    // CHECKLISTS
    // =====================================================
    function renderChecklists(checklists) {
        const container = document.getElementById('checklistsContainer');
        if (!checklists.length) {
            container.innerHTML = '';
            return;
        }

        let html = '';
        checklists.forEach(cl => {
            const items = cl.items || [];
            const total = items.length;
            const done = items.filter(i => i.completado).length;
            const pct = total > 0 ? Math.round((done / total) * 100) : 0;

            html += `<div class="tarjeta-seccion checklist-container" data-checklist-id="${cl.id}">
                <div class="checklist-header">
                    <div class="tarjeta-seccion-titulo">
                        <i class="bi bi-check2-square"></i> ${escapeHtml(cl.titulo)}
                    </div>
                    ${PUEDE_EDITAR ? `<button class="btn btn-sm btn-outline-danger" onclick="eliminarChecklist(${cl.id})"><i class="bi bi-trash"></i></button>` : ''}
                </div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="small text-muted">${pct}%</span>
                    <div class="checklist-progress flex-grow-1">
                        <div class="checklist-progress-bar ${pct === 100 ? 'completado' : ''}" style="width:${pct}%"></div>
                    </div>
                </div>`;

            items.forEach(item => {
                html += `<div class="checklist-item">
                    <input type="checkbox" ${item.completado ? 'checked' : ''}
                           onchange="toggleChecklistItem(${item.id})" ${!PUEDE_EDITAR ? 'disabled' : ''}>
                    <span class="checklist-item-texto ${item.completado ? 'completado' : ''}">${escapeHtml(item.texto)}</span>
                    ${PUEDE_EDITAR ? `<button class="btn-eliminar-item" onclick="eliminarChecklistItem(${item.id})"><i class="bi bi-x"></i></button>` : ''}
                </div>`;
            });

            if (PUEDE_EDITAR) {
                html += `<div class="mt-2">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Agregar item..." id="nuevoItem_${cl.id}"
                               onkeypress="if(event.key==='Enter'){agregarChecklistItem(${cl.id});event.preventDefault();}">
                        <button class="btn btn-outline-primary" onclick="agregarChecklistItem(${cl.id})">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>`;
            }

            html += '</div>';
        });

        container.innerHTML = html;
    }

    window.agregarChecklist = function () {
        swalInModal({
            title: 'Nueva checklist',
            input: 'text',
            inputPlaceholder: 'Nombre de la checklist...',
            showCancelButton: true,
            confirmButtonText: 'Crear',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (result.isConfirmed && result.value) {
                ajax(`/tarjetas/${currentTarjetaId}/checklists`, 'POST', { titulo: result.value })
                    .then(() => recargarTarjeta());
            }
        });
    };

    window.eliminarChecklist = function (id) {
        swalInModal({
            title: 'Eliminar checklist?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (result.isConfirmed) {
                ajax(`/tarjeta-checklists/${id}`, 'DELETE').then(() => recargarTarjeta());
            }
        });
    };

    window.toggleChecklistItem = function (itemId) {
        ajax(`/tarjeta-checklist-items/${itemId}/toggle`, 'POST').then(() => recargarTarjeta());
    };

    window.agregarChecklistItem = function (checklistId) {
        const input = document.getElementById(`nuevoItem_${checklistId}`);
        const texto = input.value.trim();
        if (!texto) return;

        ajax(`/tarjeta-checklists/${checklistId}/items`, 'POST', { texto })
            .then(() => {
                input.value = '';
                recargarTarjeta();
            });
    };

    window.eliminarChecklistItem = function (itemId) {
        ajax(`/tarjeta-checklist-items/${itemId}`, 'DELETE').then(() => recargarTarjeta());
    };

    // =====================================================
    // ATTACHMENTS
    // =====================================================
    function esImagen(mimeType) {
        return mimeType && mimeType.startsWith('image/');
    }

    function renderAdjuntos(adjuntos) {
        const container = document.getElementById('modalAdjuntos');
        const section = document.getElementById('seccionAdjuntos');

        if (!adjuntos.length) {
            section.style.display = 'none';
            return;
        }

        section.style.display = 'block';
        container.innerHTML = adjuntos.map(a => {
            const size = a.tamano > 1048576 ? (a.tamano / 1048576).toFixed(1) + ' MB' : (a.tamano / 1024).toFixed(0) + ' KB';
            const fecha = new Date(a.created_at).toLocaleDateString('es-ES');
            const deleteBtn = PUEDE_EDITAR ? `<button class="btn btn-sm btn-outline-danger" onclick="eliminarAdjunto(${a.id})"><i class="bi bi-trash"></i></button>` : '';

            if (esImagen(a.mime_type) && a.url) {
                return `<div class="adjunto-imagen-card">
                    <a href="${a.url}" target="_blank" class="adjunto-imagen-preview">
                        <img src="${a.url}" alt="${escapeHtml(a.nombre_original)}" loading="lazy">
                    </a>
                    <div class="adjunto-imagen-footer">
                        <div class="adjunto-info">
                            <a href="${a.url}" target="_blank" class="adjunto-nombre">${escapeHtml(a.nombre_original)}</a>
                            <div class="adjunto-meta">${size} - ${fecha}</div>
                        </div>
                        ${deleteBtn}
                    </div>
                </div>`;
            }

            return `<div class="adjunto-item">
                <div class="adjunto-icono"><i class="bi bi-file-earmark"></i></div>
                <div class="adjunto-info">
                    <a href="/tarjeta-adjuntos/${a.id}/descargar" class="adjunto-nombre">${escapeHtml(a.nombre_original)}</a>
                    <div class="adjunto-meta">${size} - ${fecha}</div>
                </div>
                ${deleteBtn}
            </div>`;
        }).join('');
    }

    window.subirAdjunto = function (input) {
        if (!input.files.length || !currentTarjetaId) return;

        const formData = new FormData();
        formData.append('archivo', input.files[0]);

        ajax(`/tarjetas/${currentTarjetaId}/adjuntos`, 'POST', formData)
            .then(() => {
                input.value = '';
                recargarTarjeta();
                showToast('Adjunto subido');
            })
            .catch(() => showToast('Error al subir adjunto', 'error'));
    };

    window.eliminarAdjunto = function (id) {
        swalInModal({
            title: 'Eliminar adjunto?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (result.isConfirmed) {
                ajax(`/tarjeta-adjuntos/${id}`, 'DELETE').then(() => recargarTarjeta());
            }
        });
    };

    // =====================================================
    // COMMENTS
    // =====================================================
    function renderComentarios(comentarios) {
        const container = document.getElementById('modalComentarios');
        container.innerHTML = comentarios.map(c => {
            const initials = c.user ? c.user.name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase() : '?';
            const fecha = new Date(c.created_at).toLocaleDateString('es-ES', {
                day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'
            });

            if (c.tipo === 'actividad') {
                return `<div class="comentario-item">
                    <div class="comentario-avatar" style="background:#6b7280;width:28px;height:28px;font-size:0.6rem;">${initials}</div>
                    <div class="comentario-contenido">
                        <span class="comentario-autor">${escapeHtml(c.user?.name || 'Sistema')}</span>
                        <span class="comentario-fecha">${fecha}</span>
                        <div class="actividad-texto">${escapeHtml(c.contenido)}</div>
                    </div>
                </div>`;
            }

            const canDelete = c.user_id === window.CURRENT_USER_ID;
            return `<div class="comentario-item">
                <div class="comentario-avatar">${initials}</div>
                <div class="comentario-contenido">
                    <span class="comentario-autor">${escapeHtml(c.user?.name || 'Usuario')}</span>
                    <span class="comentario-fecha">${fecha}</span>
                    ${canDelete ? `<button class="btn btn-sm text-danger p-0 ms-2" onclick="eliminarComentario(${c.id})" style="font-size:0.7rem;"><i class="bi bi-trash"></i></button>` : ''}
                    <div class="comentario-texto">${escapeHtml(c.contenido)}</div>
                </div>
            </div>`;
        }).join('');
    }

    window.agregarComentario = function () {
        const textarea = document.getElementById('nuevoComentario');
        const contenido = textarea.value.trim();
        if (!contenido || !currentTarjetaId) return;

        ajax(`/tarjetas/${currentTarjetaId}/comentarios`, 'POST', { contenido })
            .then(() => {
                textarea.value = '';
                recargarTarjeta();
            });
    };

    window.eliminarComentario = function (id) {
        ajax(`/tarjeta-comentarios/${id}`, 'DELETE').then(() => recargarTarjeta());
    };

    // =====================================================
    // MEMBERS PICKER
    // =====================================================
    window.toggleMiembrosPicker = function () {
        const picker = document.getElementById('miembrosPicker');
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
        if (picker.style.display === 'block') renderMiembrosPicker();
    };

    function renderMiembrosPicker() {
        const list = document.getElementById('miembrosPickerList');
        const assigned = (currentTarjetaData?.usuarios || []).map(u => u.id);

        list.innerHTML = (window.TABLERO_MIEMBROS || []).map(m => {
            const isAssigned = assigned.includes(m.id);
            return `<div class="miembro-item d-flex align-items-center gap-2 p-1"
                         onclick="${isAssigned ? `desasignarUsuario(${m.id})` : `asignarUsuario(${m.id})`}"
                         style="cursor:pointer;">
                <div class="avatar-mini" style="width:24px;height:24px;font-size:0.55rem;">${m.initials}</div>
                <span class="small flex-grow-1">${escapeHtml(m.name)}</span>
                ${isAssigned ? '<i class="bi bi-check-lg text-success"></i>' : ''}
            </div>`;
        }).join('');
    }

    window.asignarUsuario = function (userId) {
        ajax(`/tarjetas/${currentTarjetaId}/usuarios`, 'POST', { user_id: userId })
            .then(() => recargarTarjeta());
    };

    window.desasignarUsuario = function (userId) {
        ajax(`/tarjetas/${currentTarjetaId}/usuarios/${userId}`, 'DELETE')
            .then(() => recargarTarjeta());
    };

    // =====================================================
    // LABELS PICKER
    // =====================================================
    window.toggleEtiquetasPicker = function () {
        const picker = document.getElementById('etiquetasPicker');
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
        if (picker.style.display === 'block') renderEtiquetasPicker();
    };

    function renderEtiquetasPicker() {
        const list = document.getElementById('etiquetasPickerList');
        const attached = (currentTarjetaData?.etiquetas || []).map(e => e.id);

        list.innerHTML = (window.TABLERO_ETIQUETAS || []).map(e => {
            const isAttached = attached.includes(e.id);
            return `<div class="d-flex align-items-center gap-2 p-1 rounded" onclick="toggleEtiqueta(${e.id})"
                         style="cursor:pointer;${isAttached ? 'outline:2px solid #172b4d;' : ''}">
                <div style="width:100%;height:28px;background:${e.color};border-radius:4px;display:flex;align-items:center;padding:0 8px;">
                    <span style="color:white;font-size:0.75rem;font-weight:600;">${escapeHtml(e.nombre)}</span>
                    ${isAttached ? '<i class="bi bi-check-lg ms-auto" style="color:white;"></i>' : ''}
                </div>
            </div>`;
        }).join('');
    }

    window.toggleEtiqueta = function (etiquetaId) {
        ajax(`/tarjetas/${currentTarjetaId}/etiquetas`, 'POST', { etiqueta_id: etiquetaId })
            .then(() => recargarTarjeta());
    };

    // =====================================================
    // DATE PICKER
    // =====================================================
    window.toggleFechaPicker = function () {
        const picker = document.getElementById('fechaPicker');
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    };

    window.guardarFecha = function () {
        const val = document.getElementById('fechaVencimientoInput').value;
        if (!val) return;
        ajax(`/tarjetas/${currentTarjetaId}`, 'PUT', { fecha_vencimiento: val })
            .then(data => {
                document.getElementById('fechaPicker').style.display = 'none';
                recargarTarjeta();
                updateCardInBoard(data);
            });
    };

    window.eliminarFecha = function () {
        ajax(`/tarjetas/${currentTarjetaId}`, 'PUT', { fecha_vencimiento: '' })
            .then(data => {
                document.getElementById('fechaPicker').style.display = 'none';
                recargarTarjeta();
                updateCardInBoard(data);
            });
    };

    // =====================================================
    // COLOR PICKER
    // =====================================================
    window.toggleColorPicker = function () {
        const picker = document.getElementById('colorPicker');
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    };

    window.guardarColorPortada = function (color, el) {
        ajax(`/tarjetas/${currentTarjetaId}`, 'PUT', { color_portada: color || '' })
            .then(data => {
                document.getElementById('colorPicker').style.display = 'none';
                if (el) {
                    document.querySelectorAll('#colorPicker .color-option').forEach(c => c.classList.remove('selected'));
                    el.classList.add('selected');
                }
                updateCardInBoard(data);
            });
    };

    // =====================================================
    // PRIORITY
    // =====================================================
    window.cambiarPrioridad = function () {
        swalInModal({
            title: 'Prioridad',
            input: 'select',
            inputOptions: { 'alta': 'Alta', 'media': 'Media', 'baja': 'Baja' },
            inputValue: currentTarjetaData?.prioridad || 'media',
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (result.isConfirmed) {
                ajax(`/tarjetas/${currentTarjetaId}`, 'PUT', { prioridad: result.value })
                    .then(data => {
                        recargarTarjeta();
                        updateCardInBoard(data);
                    });
            }
        });
    };

    // =====================================================
    // ARCHIVE / DELETE
    // =====================================================
    window.archivarTarjeta = function () {
        ajax(`/tarjetas/${currentTarjetaId}/archivar`, 'POST').then(() => {
            bootstrap.Modal.getInstance(document.getElementById('tarjetaModal')).hide();
            const card = document.querySelector(`.tarjeta-card[data-tarjeta-id="${currentTarjetaId}"]`);
            if (card) card.remove();
            showToast('Tarjeta archivada');
        });
    };

    window.eliminarTarjeta = function () {
        swalInModal({
            title: 'Eliminar tarjeta?',
            text: 'Esta accion no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (result.isConfirmed) {
                ajax(`/tarjetas/${currentTarjetaId}`, 'DELETE').then(() => {
                    bootstrap.Modal.getInstance(document.getElementById('tarjetaModal')).hide();
                    const card = document.querySelector(`.tarjeta-card[data-tarjeta-id="${currentTarjetaId}"]`);
                    if (card) card.remove();
                    showToast('Tarjeta eliminada');
                });
            }
        });
    };

    // =====================================================
    // COLUMN OPERATIONS
    // =====================================================
    window.crearColumna = function (e) {
        e.preventDefault();
        const form = e.target;
        const nombre = form.querySelector('input[name="nombre"]').value.trim();
        if (!nombre) return;

        ajax(`/tableros/${TABLERO_ID}/columnas`, 'POST', { nombre }).then(data => {
            form.querySelector('input[name="nombre"]').value = '';
            const container = document.getElementById('columnasContainer');
            const nuevaCol = document.querySelector('.tablero-columna-nueva');
            const temp = document.createElement('div');
            temp.innerHTML = data.html;
            const colEl = temp.firstElementChild;
            container.insertBefore(colEl, nuevaCol);
            // Init sortable on new column
            initSortableCards(colEl.querySelector('.columna-tarjetas'));
        }).catch(() => showToast('Error al crear lista', 'error'));
    };

    window.eliminarColumna = function (columnaId, event) {
        event.preventDefault();
        Swal.fire({
            title: 'Eliminar lista?',
            text: 'Se eliminaran todas las tarjetas de esta lista',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Eliminar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (result.isConfirmed) {
                ajax(`/tablero-columnas/${columnaId}`, 'DELETE').then(() => {
                    const col = document.querySelector(`.tablero-columna[data-columna-id="${columnaId}"]`);
                    if (col) col.remove();
                    showToast('Lista eliminada');
                });
            }
        });
    };

    // Column rename on blur
    document.addEventListener('blur', function (e) {
        if (!e.target.classList.contains('columna-titulo') || !PUEDE_EDITAR) return;

        const nuevoNombre = e.target.textContent.trim();
        const original = e.target.dataset.original;
        const columnaId = e.target.dataset.columnaId;

        if (nuevoNombre && nuevoNombre !== original) {
            ajax(`/tablero-columnas/${columnaId}`, 'PUT', { nombre: nuevoNombre }).then(() => {
                e.target.dataset.original = nuevoNombre;
            });
        } else if (!nuevoNombre) {
            e.target.textContent = original;
        }
    }, true);

    // Column title Enter key
    document.addEventListener('keydown', function (e) {
        if (e.target.classList.contains('columna-titulo') && e.key === 'Enter') {
            e.preventDefault();
            e.target.blur();
        }
    });

    // =====================================================
    // ADD CARD
    // =====================================================
    window.mostrarFormNuevaTarjeta = function (btn) {
        const columnaId = btn.dataset.columnaId;
        const container = btn.closest('.tablero-columna').querySelector('.columna-tarjetas');

        // Remove existing forms
        document.querySelectorAll('.nueva-tarjeta-form').forEach(f => f.remove());

        const form = document.createElement('div');
        form.className = 'nueva-tarjeta-form';
        form.innerHTML = `
            <textarea class="form-control form-control-sm" placeholder="Titulo de la tarjeta..." rows="2" autofocus></textarea>
            <div class="d-flex gap-2 mt-2">
                <button class="btn btn-primary btn-sm btn-guardar-tarjeta" data-columna-id="${columnaId}">Agregar</button>
                <button class="btn btn-sm btn-cancelar-tarjeta"><i class="bi bi-x-lg"></i></button>
            </div>`;
        container.appendChild(form);

        const textarea = form.querySelector('textarea');
        textarea.focus();

        textarea.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                guardarNuevaTarjeta(columnaId, textarea.value.trim(), form);
            }
            if (e.key === 'Escape') form.remove();
        });

        form.querySelector('.btn-guardar-tarjeta').addEventListener('click', function () {
            guardarNuevaTarjeta(columnaId, textarea.value.trim(), form);
        });

        form.querySelector('.btn-cancelar-tarjeta').addEventListener('click', function () {
            form.remove();
        });

        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
    };

    function guardarNuevaTarjeta(columnaId, titulo, form) {
        if (!titulo) return;

        ajax('/tarjetas', 'POST', { columna_id: parseInt(columnaId), titulo })
            .then(data => {
                const container = form.closest('.columna-tarjetas');
                form.remove();
                const temp = document.createElement('div');
                temp.innerHTML = data.html;
                container.appendChild(temp.firstElementChild);
            })
            .catch(() => showToast('Error al crear tarjeta', 'error'));
    }

    // =====================================================
    // LABELS CRUD
    // =====================================================
    window.crearEtiqueta = function () {
        const nombre = document.getElementById('nuevaEtiquetaNombre').value.trim();
        const color = document.getElementById('nuevaEtiquetaColor').value;
        if (!nombre) return;

        ajax(`/tableros/${TABLERO_ID}/etiquetas`, 'POST', { nombre, color })
            .then(() => location.reload());
    };

    window.eliminarEtiqueta = function (id) {
        ajax(`/tablero-etiquetas/${id}`, 'DELETE').then(() => location.reload());
    };

    // =====================================================
    // FILTERS (client-side)
    // =====================================================
    window.toggleFiltros = function () {
        const panel = document.getElementById('filtrosPanel');
        panel.style.display = panel.style.display === 'none' ? 'flex' : 'none';
    };

    window.aplicarFiltros = function () {
        const miembro = document.getElementById('filtroMiembro').value;
        const etiqueta = document.getElementById('filtroEtiqueta').value;
        const prioridad = document.getElementById('filtroPrioridad').value;
        const fecha = document.getElementById('filtroFecha').value;
        const busqueda = document.getElementById('filtroBusqueda').value.toLowerCase();

        document.querySelectorAll('.tarjeta-card').forEach(card => {
            let show = true;

            if (miembro && !(card.dataset.usuarios || '').split(',').filter(Boolean).includes(miembro)) show = false;
            if (etiqueta && !(card.dataset.etiquetas || '').split(',').filter(Boolean).includes(etiqueta)) show = false;
            if (prioridad && card.dataset.prioridad !== prioridad) show = false;
            if (fecha) {
                const estado = card.dataset.estadoFecha || 'sin_fecha';
                if (fecha === 'vencida' && estado !== 'vencida' && estado !== 'urgente') show = false;
                if (fecha === 'pronto' && estado !== 'pronto') show = false;
                if (fecha === 'sin_fecha' && estado !== 'sin_fecha') show = false;
            }
            if (busqueda && !(card.dataset.titulo || '').includes(busqueda)) show = false;

            card.style.display = show ? '' : 'none';
        });
    };

    window.limpiarFiltros = function () {
        document.getElementById('filtroMiembro').value = '';
        document.getElementById('filtroEtiqueta').value = '';
        document.getElementById('filtroPrioridad').value = '';
        document.getElementById('filtroFecha').value = '';
        document.getElementById('filtroBusqueda').value = '';
        document.querySelectorAll('.tarjeta-card').forEach(card => card.style.display = '');
    };

    // =====================================================
    // HELPERS
    // =====================================================
    function recargarTarjeta() {
        if (!currentTarjetaId) return;
        ajax(`/tarjetas/${currentTarjetaId}`, 'GET').then(data => {
            currentTarjetaData = data.tarjeta;
            poblarModal(data);
            updateCardInBoard(data);
        });
    }

    function updateCardInBoard(data) {
        if (data.html) {
            const card = document.querySelector(`.tarjeta-card[data-tarjeta-id="${currentTarjetaId}"]`);
            if (card) {
                const temp = document.createElement('div');
                temp.innerHTML = data.html;
                card.replaceWith(temp.firstElementChild);
            }
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function showToast(msg, type) {
        if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type || 'success',
                title: msg,
                showConfirmButton: false,
                timer: 2500,
            });
        }
    }
});
