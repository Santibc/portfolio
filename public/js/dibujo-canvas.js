/**
 * SINDEN - Dibujo Canvas
 * Canvas de dibujo para bosquejos con herramientas basicas.
 * Soporta dibujo nuevo (fondo blanco) y edicion de bosquejos existentes (imagen de fondo).
 */
(function() {
    var canvas, ctx, isDrawing = false;
    var lastX = 0, lastY = 0;
    var currentColor = '#000000';
    var currentWidth = 3;
    var undoStack = [];
    var maxUndo = 20;

    // Estado de edicion
    var editandoBosquejoIndex = null;
    var imagenFondoOriginal = null; // Image object para restaurar con "Limpiar"

    window.initDibujoCanvas = function() {
        canvas = document.getElementById('dibujoCanvas');
        if (!canvas) return;
        ctx = canvas.getContext('2d');

        // Fondo blanco
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Mouse
        canvas.addEventListener('mousedown', function(e) {
            saveState();
            startDraw(getMousePos(e));
        });
        canvas.addEventListener('mousemove', function(e) { if (isDrawing) draw(getMousePos(e)); });
        canvas.addEventListener('mouseup', endDraw);
        canvas.addEventListener('mouseleave', endDraw);

        // Touch
        canvas.addEventListener('touchstart', function(e) {
            e.preventDefault();
            saveState();
            startDraw(getTouchPos(e));
        }, { passive: false });
        canvas.addEventListener('touchmove', function(e) {
            e.preventDefault();
            if (isDrawing) draw(getTouchPos(e));
        }, { passive: false });
        canvas.addEventListener('touchend', function(e) {
            e.preventDefault();
            endDraw();
        });

        // Listener para resetear estado al cerrar el modal
        var modal = document.getElementById('modalDibujoTablet');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                resetearEstadoEdicion();
            });
        }
    };

    function getMousePos(e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top) * scaleY
        };
    }

    function getTouchPos(e) {
        var rect = canvas.getBoundingClientRect();
        var touch = e.touches[0];
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        return {
            x: (touch.clientX - rect.left) * scaleX,
            y: (touch.clientY - rect.top) * scaleY
        };
    }

    function startDraw(pos) {
        isDrawing = true;
        lastX = pos.x;
        lastY = pos.y;
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.strokeStyle = currentColor;
        ctx.lineWidth = currentWidth;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
    }

    function draw(pos) {
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        lastX = pos.x;
        lastY = pos.y;
    }

    function endDraw() {
        if (!isDrawing) return;
        isDrawing = false;
        ctx.closePath();
    }

    function saveState() {
        if (!canvas || !ctx) return;
        if (undoStack.length >= maxUndo) {
            undoStack.shift();
        }
        undoStack.push(ctx.getImageData(0, 0, canvas.width, canvas.height));
    }

    function dibujarImagenEnCanvas(img) {
        if (!canvas || !ctx) return;
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Escalar imagen manteniendo aspect ratio
        var scale = Math.min(canvas.width / img.width, canvas.height / img.height);
        var w = img.width * scale;
        var h = img.height * scale;
        var x = (canvas.width - w) / 2;
        var y = (canvas.height - h) / 2;
        ctx.drawImage(img, x, y, w, h);
    }

    function resetearEstadoEdicion() {
        editandoBosquejoIndex = null;
        imagenFondoOriginal = null;
        undoStack = [];
        // Restaurar texto del boton
        var btn = document.getElementById('btnGuardarDibujo');
        if (btn) {
            btn.innerHTML = '<i class="bi bi-save me-1"></i> Guardar Dibujo';
        }
        // Limpiar canvas a blanco
        if (canvas && ctx) {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }
    }

    // ==========================================
    // Funciones publicas
    // ==========================================

    window.cambiarColorDibujo = function(color) {
        currentColor = color;
    };

    window.cambiarGrosorDibujo = function(width) {
        currentWidth = width;
    };

    window.limpiarDibujo = function() {
        if (!canvas || !ctx) return;
        saveState();
        if (editandoBosquejoIndex !== null && imagenFondoOriginal) {
            // En modo edicion: restaurar imagen original
            dibujarImagenEnCanvas(imagenFondoOriginal);
        } else {
            // Dibujo nuevo: fondo blanco
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }
    };

    window.deshacerDibujo = function() {
        if (!ctx || undoStack.length === 0) return;
        ctx.putImageData(undoStack.pop(), 0, 0);
    };

    window.abrirEditorBosquejo = function(index) {
        if (typeof wizardState === 'undefined' || !wizardState.bosquejos[index]) return;

        var bosquejo = wizardState.bosquejos[index];
        editandoBosquejoIndex = index;
        undoStack = [];

        // Cambiar texto del boton
        var btn = document.getElementById('btnGuardarDibujo');
        if (btn) {
            btn.innerHTML = '<i class="bi bi-save me-1"></i> Guardar Edicion';
        }

        // Abrir modal
        var modalEl = document.getElementById('modalDibujoTablet');
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        // Esperar a que el modal se muestre para cargar la imagen
        modalEl.addEventListener('shown.bs.modal', function handler() {
            modalEl.removeEventListener('shown.bs.modal', handler);

            var imgSrc = bosquejo.ruta_archivo || bosquejo.ruta_miniatura;
            if (imgSrc && !imgSrc.startsWith('http') && !imgSrc.startsWith('/') && !imgSrc.startsWith('data:')) {
                imgSrc = '/' + imgSrc;
            }

            var img = new Image();
            if (!imgSrc.startsWith('data:')) {
                img.crossOrigin = 'anonymous';
            }
            img.onload = function() {
                imagenFondoOriginal = img;
                dibujarImagenEnCanvas(img);
                // Guardar estado base para que "deshacer" vuelva a la imagen limpia
                saveState();
            };
            img.onerror = function() {
                Swal.fire('Error', 'No se pudo cargar la imagen del bosquejo.', 'error');
                resetearEstadoEdicion();
                modal.hide();
            };
            img.src = imgSrc;
        });
    };

    window.guardarDibujoComoImagen = function() {
        if (!canvas) return;

        var dataUrl = canvas.toDataURL('image/png');

        // Verificar que no este en blanco (solo para dibujo nuevo)
        if (editandoBosquejoIndex === null) {
            var blank = document.createElement('canvas');
            blank.width = canvas.width;
            blank.height = canvas.height;
            var blankCtx = blank.getContext('2d');
            blankCtx.fillStyle = '#ffffff';
            blankCtx.fillRect(0, 0, blank.width, blank.height);

            if (canvas.toDataURL() === blank.toDataURL()) {
                Swal.fire('Aviso', 'El lienzo esta vacio. Dibuje algo antes de guardar.', 'warning');
                return;
            }
        }

        var esEdicion = editandoBosquejoIndex !== null;
        var nombreBosquejo = esEdicion
            ? wizardState.bosquejos[editandoBosquejoIndex].nombre
            : 'Dibujo ' + (wizardState.bosquejos.length + 1);

        var formData = new FormData();
        formData.append('imagen_base64', dataUrl);
        formData.append('tipo_origen', esEdicion ? 'edicion_bosquejo' : 'dibujo_tablet');
        formData.append('nombre', nombreBosquejo);
        if (wizardState.ordenId) {
            formData.append('orden_id', wizardState.ordenId);
        }

        $.ajax({
            url: ROUTES.subirBosquejo,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': WIZARD_CONFIG.csrfToken },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    var bosquejoIdx;
                    if (esEdicion) {
                        // Reemplazar bosquejo existente con la version editada
                        var bosquejoEditado = response.bosquejo;
                        bosquejoEditado.nombre = nombreBosquejo;
                        wizardState.bosquejos[editandoBosquejoIndex] = bosquejoEditado;
                        bosquejoIdx = editandoBosquejoIndex;
                    } else {
                        // Agregar nuevo bosquejo
                        wizardState.bosquejos.push(response.bosquejo);
                        bosquejoIdx = wizardState.bosquejos.length - 1;
                    }

                    // Vincular a pieza si hay target definido
                    var targetPieza = window._targetPiezaForDibujo;
                    if (targetPieza !== undefined && targetPieza !== null) {
                        if (typeof vincularBosquejoAPieza === 'function') {
                            vincularBosquejoAPieza(targetPieza, bosquejoIdx);
                        }
                        window._targetPiezaForDibujo = null;
                        window._editandoBosquejoParaPieza = false;
                    }

                    // Llamar stubs para compatibilidad
                    if (typeof renderizarGrillaBosquejos === 'function') renderizarGrillaBosquejos();
                    if (typeof actualizarSelectBosquejosPiezas === 'function') actualizarSelectBosquejosPiezas();

                    $('#modalDibujoTablet').modal('hide');
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success',
                        title: esEdicion ? 'Bosquejo editado' : 'Dibujo guardado',
                        showConfirmButton: false, timer: 2000 });
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr, 'guardar el dibujo');
            }
        });
    };
})();
