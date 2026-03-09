/**
 * SINDEN - Dibujo Canvas Profesional (Fabric.js)
 * Canvas de dibujo profesional para bosquejos industriales.
 * Herramientas: lapiz, linea, rectangulo, elipse, flecha, texto, borrador.
 * Zoom: scroll, pinch-to-zoom (tablets), botones.
 * Undo/Redo con serializacion JSON.
 * Compatible con la API publica existente de orden-wizard.js
 */
(function() {
    'use strict';

    // =============================================
    // ESTADO PRIVADO
    // =============================================
    var fabricCanvas = null;
    var currentTool = 'pencil';
    var currentColor = '#000000';
    var currentWidth = 3;

    // Undo/Redo
    var undoStack = [];
    var redoStack = [];
    var maxUndo = 50;
    var isSavingState = false; // evita recursion en saveState

    // Shape drawing
    var isShapeDrawing = false;
    var shapeOrigin = null;
    var activeShape = null;

    // Edit mode
    var editandoBosquejoIndex = null;
    var imagenFondoOriginal = null;

    // Pan state
    var isPanning = false;
    var lastPanPoint = null;

    // Pinch state
    var pinchActive = false;
    var pinchStartDist = 0;
    var pinchStartZoom = 1;
    var pinchLastCenter = null;

    // =============================================
    // INICIALIZACION
    // =============================================
    window.initDibujoCanvas = function() {
        var canvasEl = document.getElementById('dibujoCanvas');
        if (!canvasEl) return;

        // Dispose instancia previa si existe
        if (fabricCanvas) {
            try { fabricCanvas.dispose(); } catch(e) {}
            fabricCanvas = null;
        }

        // Reset estado
        undoStack = [];
        redoStack = [];
        currentTool = 'pencil';
        isShapeDrawing = false;
        activeShape = null;
        editandoBosquejoIndex = null;
        imagenFondoOriginal = null;

        // Tamano inicial del canvas
        var wrapper = document.getElementById('dibujoCanvasWrapper');
        var w = wrapper ? wrapper.clientWidth : 800;
        var h = wrapper ? wrapper.clientHeight : 600;
        if (w < 100) w = 800;
        if (h < 100) h = 600;

        fabricCanvas = new fabric.Canvas('dibujoCanvas', {
            isDrawingMode: true,
            width: w,
            height: h,
            backgroundColor: '#ffffff',
            enableRetinaScaling: true,
            allowTouchScrolling: false
        });

        // Brush por defecto
        fabricCanvas.freeDrawingBrush = new fabric.PencilBrush(fabricCanvas);
        fabricCanvas.freeDrawingBrush.color = currentColor;
        fabricCanvas.freeDrawingBrush.width = currentWidth;

        // Guardar estado inicial
        saveState();

        // Bind eventos
        bindCanvasEvents();
        bindPinchZoom();
        bindKeyboardShortcuts();

        // Actualizar toolbar
        setTool('pencil');
        updateColorButtons();
        updateWidthButtons();
        updateZoomLabel();
    };

    // =============================================
    // RESIZE DEL CANVAS
    // =============================================
    function resizeCanvasToWrapper() {
        if (!fabricCanvas) return;
        var wrapper = document.getElementById('dibujoCanvasWrapper');
        if (!wrapper) return;
        var w = wrapper.clientWidth;
        var h = wrapper.clientHeight;
        if (w < 100 || h < 100) return;

        fabricCanvas.setDimensions({ width: w, height: h });
        fabricCanvas.renderAll();
    }

    // =============================================
    // GESTION DE HERRAMIENTAS
    // =============================================
    function setTool(tool) {
        currentTool = tool;

        // Cancelar forma en progreso
        if (isShapeDrawing && activeShape) {
            fabricCanvas.remove(activeShape);
            activeShape = null;
            isShapeDrawing = false;
        }

        fabricCanvas.isDrawingMode = (tool === 'pencil' || tool === 'white-brush');
        fabricCanvas.selection = (tool === 'select');

        // Configurar brush blanco para borrador de fondo
        if (tool === 'white-brush') {
            fabricCanvas.freeDrawingBrush = new fabric.PencilBrush(fabricCanvas);
            fabricCanvas.freeDrawingBrush.color = '#ffffff';
            fabricCanvas.freeDrawingBrush.width = Math.max(currentWidth * 3, 10);
        } else if (tool === 'pencil') {
            fabricCanvas.freeDrawingBrush = new fabric.PencilBrush(fabricCanvas);
            fabricCanvas.freeDrawingBrush.color = currentColor;
            fabricCanvas.freeDrawingBrush.width = currentWidth;
        }

        // Cursor
        if (tool === 'text') {
            fabricCanvas.defaultCursor = 'text';
            fabricCanvas.hoverCursor = 'text';
        } else if (tool === 'select') {
            fabricCanvas.defaultCursor = 'default';
            fabricCanvas.hoverCursor = 'move';
        } else if (tool === 'eraser') {
            fabricCanvas.defaultCursor = 'pointer';
            fabricCanvas.hoverCursor = 'pointer';
        } else if (tool === 'white-brush') {
            fabricCanvas.defaultCursor = 'crosshair';
            fabricCanvas.hoverCursor = 'crosshair';
        } else if (tool === 'pan') {
            fabricCanvas.defaultCursor = 'grab';
            fabricCanvas.hoverCursor = 'grab';
        } else {
            fabricCanvas.defaultCursor = 'crosshair';
            fabricCanvas.hoverCursor = 'crosshair';
        }

        // Deseleccionar objetos
        fabricCanvas.discardActiveObject();
        fabricCanvas.renderAll();

        // Hacer objetos no-seleccionables en modos de dibujo
        var selectable = (tool === 'select' || tool === 'eraser' || tool === 'text');
        fabricCanvas.forEachObject(function(obj) {
            obj.selectable = selectable;
            obj.evented = selectable;
        });

        // Actualizar botones activos
        document.querySelectorAll('.dibujo-tool').forEach(function(btn) {
            btn.classList.toggle('active', btn.dataset.tool === tool);
        });
    }

    // =============================================
    // BINDEO DE EVENTOS DEL CANVAS
    // =============================================
    function bindCanvasEvents() {
        // --- MOUSE DOWN ---
        fabricCanvas.on('mouse:down', function(opt) {
            var e = opt.e;

            // Pan con Alt+click o boton medio
            if (e.altKey || e.button === 1 || currentTool === 'pan') {
                isPanning = true;
                lastPanPoint = { x: e.clientX, y: e.clientY };
                fabricCanvas.selection = false;
                fabricCanvas.defaultCursor = 'grabbing';
                return;
            }

            // Borrador: eliminar objeto
            if (currentTool === 'eraser') {
                var target = fabricCanvas.findTarget(e);
                if (target) {
                    saveState();
                    fabricCanvas.remove(target);
                    fabricCanvas.renderAll();
                }
                return;
            }

            // Texto: crear IText al hacer click
            if (currentTool === 'text') {
                // Si hacemos click sobre un IText existente, dejar que Fabric lo maneje
                var clickTarget = fabricCanvas.findTarget(e);
                if (clickTarget && clickTarget.type === 'i-text') return;

                saveState();
                var ptr = fabricCanvas.getPointer(e);
                var itext = new fabric.IText('', {
                    left: ptr.x,
                    top: ptr.y,
                    fontSize: Math.max(currentWidth * 5, 18),
                    fill: currentColor,
                    fontFamily: 'Arial, sans-serif',
                    editable: true,
                    selectable: true,
                    evented: true
                });
                fabricCanvas.add(itext);
                fabricCanvas.setActiveObject(itext);
                // Delay enterEditing para que Fabric termine de procesar el mouse event
                setTimeout(function() {
                    if (itext) {
                        itext.enterEditing();
                        // Fabric.js crea el hidden textarea en <body>, pero Bootstrap modal
                        // atrapa el focus dentro del modal. Moverlo dentro del modal.
                        if (itext.hiddenTextarea) {
                            var wrapper = document.getElementById('dibujoCanvasWrapper');
                            if (wrapper && itext.hiddenTextarea.parentElement !== wrapper) {
                                wrapper.appendChild(itext.hiddenTextarea);
                            }
                            itext.hiddenTextarea.focus();
                        }
                    }
                }, 100);
                return;
            }

            // Formas: linea, rectangulo, elipse, flecha
            if (['line', 'rect', 'ellipse', 'arrow'].indexOf(currentTool) !== -1) {
                isShapeDrawing = true;
                var ptr = fabricCanvas.getPointer(e);
                shapeOrigin = { x: ptr.x, y: ptr.y };
                saveState();

                if (currentTool === 'line' || currentTool === 'arrow') {
                    activeShape = new fabric.Line([ptr.x, ptr.y, ptr.x, ptr.y], {
                        stroke: currentColor,
                        strokeWidth: currentWidth,
                        selectable: false,
                        evented: false
                    });
                    fabricCanvas.add(activeShape);
                } else if (currentTool === 'rect') {
                    activeShape = new fabric.Rect({
                        left: ptr.x,
                        top: ptr.y,
                        width: 0,
                        height: 0,
                        fill: 'transparent',
                        stroke: currentColor,
                        strokeWidth: currentWidth,
                        selectable: false,
                        evented: false
                    });
                    fabricCanvas.add(activeShape);
                } else if (currentTool === 'ellipse') {
                    activeShape = new fabric.Ellipse({
                        left: ptr.x,
                        top: ptr.y,
                        rx: 0,
                        ry: 0,
                        fill: 'transparent',
                        stroke: currentColor,
                        strokeWidth: currentWidth,
                        originX: 'center',
                        originY: 'center',
                        selectable: false,
                        evented: false
                    });
                    fabricCanvas.add(activeShape);
                }
                return;
            }
        });

        // --- MOUSE MOVE ---
        fabricCanvas.on('mouse:move', function(opt) {
            var e = opt.e;

            // Pan
            if (isPanning && lastPanPoint) {
                var dx = e.clientX - lastPanPoint.x;
                var dy = e.clientY - lastPanPoint.y;
                lastPanPoint = { x: e.clientX, y: e.clientY };
                var vpt = fabricCanvas.viewportTransform;
                vpt[4] += dx;
                vpt[5] += dy;
                fabricCanvas.requestRenderAll();
                return;
            }

            // Shape drawing
            if (!isShapeDrawing || !activeShape) return;
            var pointer = fabricCanvas.getPointer(e);

            if (currentTool === 'line' || currentTool === 'arrow') {
                activeShape.set({ x2: pointer.x, y2: pointer.y });
            } else if (currentTool === 'rect') {
                var left = Math.min(shapeOrigin.x, pointer.x);
                var top = Math.min(shapeOrigin.y, pointer.y);
                activeShape.set({
                    left: left,
                    top: top,
                    width: Math.abs(pointer.x - shapeOrigin.x),
                    height: Math.abs(pointer.y - shapeOrigin.y)
                });
            } else if (currentTool === 'ellipse') {
                activeShape.set({
                    rx: Math.abs(pointer.x - shapeOrigin.x) / 2,
                    ry: Math.abs(pointer.y - shapeOrigin.y) / 2,
                    left: (shapeOrigin.x + pointer.x) / 2,
                    top: (shapeOrigin.y + pointer.y) / 2
                });
            }
            fabricCanvas.renderAll();
        });

        // --- MOUSE UP ---
        fabricCanvas.on('mouse:up', function(opt) {
            // End pan
            if (isPanning) {
                isPanning = false;
                lastPanPoint = null;
                if (currentTool === 'pan') {
                    fabricCanvas.defaultCursor = 'grab';
                } else {
                    fabricCanvas.selection = (currentTool === 'select' || currentTool === 'text');
                }
                return;
            }

            // Texto: no hacer nada en mouse:up (se crea en mouse:down)
            if (currentTool === 'text') {
                return;
            }

            // End shape drawing
            if (!isShapeDrawing || !activeShape) return;
            isShapeDrawing = false;

            // Para flecha: agregar punta
            if (currentTool === 'arrow') {
                var arrowHead = createArrowHead(activeShape);
                if (arrowHead) {
                    fabricCanvas.add(arrowHead);
                }
            }

            // Hacer la forma seleccionable ahora
            activeShape.set({ selectable: true, evented: true });
            activeShape = null;
            shapeOrigin = null;

            // Guardar estado despues de la forma
            saveState();
        });

        // --- PATH CREATED (free draw) ---
        fabricCanvas.on('path:created', function() {
            saveState();
        });

        // --- OBJECT MODIFIED ---
        fabricCanvas.on('object:modified', function() {
            saveState();
        });

        // --- TEXT EDITING ENTERED ---
        // Fabric.js crea el hidden textarea en <body>, pero Bootstrap modal
        // atrapa el focus dentro del modal. Mover textarea al wrapper del canvas.
        fabricCanvas.on('text:editing:entered', function(opt) {
            var itext = opt.target;
            if (itext && itext.hiddenTextarea) {
                var wrapper = document.getElementById('dibujoCanvasWrapper');
                if (wrapper && itext.hiddenTextarea.parentElement !== wrapper) {
                    wrapper.appendChild(itext.hiddenTextarea);
                }
                itext.hiddenTextarea.focus();
            }
        });

        // --- TEXT EDITING EXITED ---
        fabricCanvas.on('text:editing:exited', function(opt) {
            var itext = opt.target;
            // Eliminar textos vacios
            if (itext && itext.text.trim() === '') {
                fabricCanvas.remove(itext);
            }
            saveState();
        });

        // --- MOUSE WHEEL ZOOM ---
        fabricCanvas.on('mouse:wheel', function(opt) {
            var delta = opt.e.deltaY;
            var zoom = fabricCanvas.getZoom();
            zoom *= 0.999 ** delta;
            zoom = Math.min(Math.max(zoom, 0.25), 10);
            var point = new fabric.Point(opt.e.offsetX, opt.e.offsetY);
            fabricCanvas.zoomToPoint(point, zoom);
            updateZoomLabel();
            opt.e.preventDefault();
            opt.e.stopPropagation();
        });
    }

    // =============================================
    // FLECHA (arrowhead)
    // =============================================
    function createArrowHead(line) {
        if (!line) return null;
        var x1 = line.x1, y1 = line.y1, x2 = line.x2, y2 = line.y2;
        var lineLen = Math.hypot(x2 - x1, y2 - y1);
        // No crear punta si la linea es muy corta
        if (lineLen < 5) return null;

        var angle = Math.atan2(y2 - y1, x2 - x1) * 180 / Math.PI;
        // Tamano proporcional a la linea, con limites razonables
        var headW = Math.min(Math.max(currentWidth * 2.5, 10), 30);
        var headH = Math.min(Math.max(currentWidth * 3.5, 14), 40);
        return new fabric.Triangle({
            left: x2,
            top: y2,
            originX: 'center',
            originY: 'bottom',
            width: headW,
            height: headH,
            fill: currentColor,
            angle: angle + 90,
            selectable: false,
            evented: false
        });
    }

    // =============================================
    // UNDO / REDO (JSON serialization)
    // =============================================
    function saveState() {
        if (!fabricCanvas || isSavingState) return;
        isSavingState = true;
        try {
            redoStack = [];
            var json = JSON.stringify(fabricCanvas.toJSON());
            undoStack.push(json);
            if (undoStack.length > maxUndo) undoStack.shift();
        } finally {
            isSavingState = false;
        }
    }

    window.deshacerDibujo = function() {
        if (!fabricCanvas || undoStack.length <= 1) return;
        isSavingState = true;
        redoStack.push(undoStack.pop());
        var prevState = undoStack[undoStack.length - 1];
        fabricCanvas.loadFromJSON(prevState, function() {
            fabricCanvas.renderAll();
            isSavingState = false;
        });
    };

    window.rehacerDibujo = function() {
        if (!fabricCanvas || redoStack.length === 0) return;
        isSavingState = true;
        var nextState = redoStack.pop();
        undoStack.push(nextState);
        fabricCanvas.loadFromJSON(nextState, function() {
            fabricCanvas.renderAll();
            isSavingState = false;
        });
    };

    // =============================================
    // ZOOM CONTROLS
    // =============================================
    window.zoomDibujo = function(direction) {
        if (!fabricCanvas) return;
        var zoom = fabricCanvas.getZoom();
        if (direction === 1) zoom *= 1.3;
        else if (direction === -1) zoom /= 1.3;
        else {
            // Reset: zoom 1, centrar viewport
            fabricCanvas.setViewportTransform([1, 0, 0, 1, 0, 0]);
            updateZoomLabel();
            fabricCanvas.renderAll();
            return;
        }
        zoom = Math.min(Math.max(zoom, 0.25), 10);
        var center = { x: fabricCanvas.width / 2, y: fabricCanvas.height / 2 };
        fabricCanvas.zoomToPoint(new fabric.Point(center.x, center.y), zoom);
        updateZoomLabel();
    };

    function updateZoomLabel() {
        var label = document.getElementById('dibujoZoomLabel');
        if (label && fabricCanvas) {
            label.textContent = Math.round(fabricCanvas.getZoom() * 100) + '%';
        }
    }

    // =============================================
    // PINCH-TO-ZOOM (Touch)
    // =============================================
    function bindPinchZoom() {
        if (!fabricCanvas) return;
        var upperCanvas = fabricCanvas.upperCanvasEl;
        if (!upperCanvas) return;

        upperCanvas.addEventListener('touchstart', function(e) {
            if (e.touches.length === 2) {
                pinchActive = true;
                // Deshabilitar dibujo durante pinch
                if (fabricCanvas.isDrawingMode) {
                    fabricCanvas.isDrawingMode = false;
                }
                pinchStartDist = getTouchDistance(e.touches[0], e.touches[1]);
                pinchStartZoom = fabricCanvas.getZoom();
                pinchLastCenter = getTouchCenter(e.touches[0], e.touches[1]);
                e.preventDefault();
            }
        }, { passive: false });

        upperCanvas.addEventListener('touchmove', function(e) {
            if (e.touches.length === 2 && pinchActive) {
                e.preventDefault();
                var dist = getTouchDistance(e.touches[0], e.touches[1]);
                var center = getTouchCenter(e.touches[0], e.touches[1]);

                // Zoom
                var scale = dist / pinchStartDist;
                var zoom = pinchStartZoom * scale;
                zoom = Math.min(Math.max(zoom, 0.25), 10);

                var rect = upperCanvas.getBoundingClientRect();
                var point = new fabric.Point(
                    center.x - rect.left,
                    center.y - rect.top
                );
                fabricCanvas.zoomToPoint(point, zoom);

                // Pan
                if (pinchLastCenter) {
                    var dx = center.x - pinchLastCenter.x;
                    var dy = center.y - pinchLastCenter.y;
                    var vpt = fabricCanvas.viewportTransform;
                    vpt[4] += dx;
                    vpt[5] += dy;
                }

                pinchLastCenter = center;
                fabricCanvas.requestRenderAll();
                updateZoomLabel();
            }
        }, { passive: false });

        upperCanvas.addEventListener('touchend', function(e) {
            if (pinchActive && e.touches.length < 2) {
                pinchActive = false;
                // Restaurar modo dibujo si estamos en pencil
                if (currentTool === 'pencil') {
                    fabricCanvas.isDrawingMode = true;
                }
            }
        });
    }

    function getTouchDistance(t1, t2) {
        return Math.hypot(t2.clientX - t1.clientX, t2.clientY - t1.clientY);
    }

    function getTouchCenter(t1, t2) {
        return {
            x: (t1.clientX + t2.clientX) / 2,
            y: (t1.clientY + t2.clientY) / 2
        };
    }

    // =============================================
    // KEYBOARD SHORTCUTS
    // =============================================
    function bindKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            var modal = document.getElementById('modalDibujoTablet');
            if (!modal || !modal.classList.contains('show')) return;

            // No interceptar teclas si hay un IText en modo edicion
            if (fabricCanvas) {
                var activeObj = fabricCanvas.getActiveObject();
                if (activeObj && activeObj.type === 'i-text' && activeObj.isEditing) {
                    // Solo interceptar Escape para salir de edicion
                    if (e.key === 'Escape') {
                        activeObj.exitEditing();
                        fabricCanvas.discardActiveObject();
                        fabricCanvas.renderAll();
                        saveState();
                        e.preventDefault();
                    }
                    return;
                }
            }

            // Ctrl+Z / Cmd+Z = Deshacer
            if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                e.preventDefault();
                deshacerDibujo();
            }
            // Ctrl+Y / Cmd+Shift+Z = Rehacer
            else if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) {
                e.preventDefault();
                rehacerDibujo();
            }
            // Delete/Backspace = Eliminar objeto seleccionado
            else if ((e.key === 'Delete' || e.key === 'Backspace') && currentTool === 'select') {
                var activeObj = fabricCanvas.getActiveObject();
                if (activeObj && !activeObj.isEditing) {
                    e.preventDefault();
                    saveState();
                    fabricCanvas.remove(activeObj);
                    fabricCanvas.renderAll();
                }
            }
        });
    }

    // =============================================
    // FUNCIONES PUBLICAS (API compatible)
    // =============================================

    window.cambiarColorDibujo = function(color) {
        currentColor = color;
        if (fabricCanvas && fabricCanvas.freeDrawingBrush && currentTool !== 'white-brush') {
            fabricCanvas.freeDrawingBrush.color = color;
        }
        updateColorButtons();
    };

    window.cambiarGrosorDibujo = function(width) {
        currentWidth = width;
        if (fabricCanvas && fabricCanvas.freeDrawingBrush) {
            // El borrador blanco usa grosor multiplicado
            if (currentTool === 'white-brush') {
                fabricCanvas.freeDrawingBrush.width = Math.max(width * 3, 10);
            } else {
                fabricCanvas.freeDrawingBrush.width = width;
            }
        }
        updateWidthButtons();
    };

    window.limpiarDibujo = function() {
        if (!fabricCanvas) return;
        saveState();
        // Limpiar todos los objetos pero mantener background si estamos editando
        fabricCanvas.getObjects().slice().forEach(function(obj) {
            fabricCanvas.remove(obj);
        });
        fabricCanvas.renderAll();
    };

    window.abrirEditorBosquejo = function(index) {
        if (typeof wizardState === 'undefined' || !wizardState.bosquejos[index]) return;

        var bosquejo = wizardState.bosquejos[index];
        editandoBosquejoIndex = index;
        undoStack = [];
        redoStack = [];

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
            resizeCanvasToWrapper();

            var imgSrc = bosquejo.ruta_archivo || bosquejo.ruta_miniatura;
            if (imgSrc && !imgSrc.startsWith('http') && !imgSrc.startsWith('/') && !imgSrc.startsWith('data:')) {
                imgSrc = '/' + imgSrc;
            }

            fabric.Image.fromURL(imgSrc, function(img) {
                if (!img) {
                    Swal.fire('Error', 'No se pudo cargar la imagen del bosquejo.', 'error');
                    resetearEstadoEdicion();
                    modal.hide();
                    return;
                }
                // Escalar para que quepa en el canvas
                var scaleX = fabricCanvas.width / img.width;
                var scaleY = fabricCanvas.height / img.height;
                var scale = Math.min(scaleX, scaleY);

                fabricCanvas.setBackgroundImage(img, fabricCanvas.renderAll.bind(fabricCanvas), {
                    scaleX: scale,
                    scaleY: scale,
                    left: (fabricCanvas.width - img.width * scale) / 2,
                    top: (fabricCanvas.height - img.height * scale) / 2
                });

                imagenFondoOriginal = imgSrc;
                saveState();
            }, { crossOrigin: 'anonymous' });
        });
    };

    window.guardarDibujoComoImagen = function() {
        if (!fabricCanvas) return;

        // Verificar que no este vacio (solo para dibujo nuevo)
        if (editandoBosquejoIndex === null) {
            if (fabricCanvas.getObjects().length === 0 && !fabricCanvas.backgroundImage) {
                Swal.fire('Aviso', 'El lienzo esta vacio. Dibuje algo antes de guardar.', 'warning');
                return;
            }
        }

        // Reset viewport para exportar a tamano completo
        var savedTransform = fabricCanvas.viewportTransform.slice();
        fabricCanvas.setViewportTransform([1, 0, 0, 1, 0, 0]);

        // Exportar con multiplier para alta resolucion
        var dataUrl = fabricCanvas.toDataURL({
            format: 'png',
            multiplier: 2,
            quality: 1
        });

        // Restaurar viewport
        fabricCanvas.setViewportTransform(savedTransform);

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
                        var bosquejoEditado = response.bosquejo;
                        bosquejoEditado.nombre = nombreBosquejo;
                        wizardState.bosquejos[editandoBosquejoIndex] = bosquejoEditado;
                        bosquejoIdx = editandoBosquejoIndex;
                    } else {
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

    // =============================================
    // RESET ESTADO AL CERRAR MODAL
    // =============================================
    function resetearEstadoEdicion() {
        editandoBosquejoIndex = null;
        imagenFondoOriginal = null;
        undoStack = [];
        redoStack = [];
        isShapeDrawing = false;
        activeShape = null;

        // Restaurar texto del boton
        var btn = document.getElementById('btnGuardarDibujo');
        if (btn) {
            btn.innerHTML = '<i class="bi bi-save me-1"></i> Guardar Dibujo';
        }

        // Dispose canvas para liberar memoria
        if (fabricCanvas) {
            try { fabricCanvas.dispose(); } catch(e) {}
            fabricCanvas = null;
        }
    }

    // =============================================
    // TOOLBAR UI UPDATES
    // =============================================
    function updateColorButtons() {
        document.querySelectorAll('.dibujo-color').forEach(function(btn) {
            btn.classList.toggle('active', btn.dataset.color === currentColor);
        });
    }

    function updateWidthButtons() {
        document.querySelectorAll('.dibujo-width').forEach(function(btn) {
            btn.classList.toggle('active', parseInt(btn.dataset.width) === currentWidth);
        });
    }

    // =============================================
    // EVENT DELEGATION PARA TOOLBAR
    // =============================================
    $(document).on('click', '.dibujo-tool', function() {
        setTool($(this).data('tool'));
    });

    $(document).on('click', '.dibujo-color', function() {
        cambiarColorDibujo($(this).data('color'));
    });

    $(document).on('click', '.dibujo-width', function() {
        cambiarGrosorDibujo(parseInt($(this).data('width')));
    });

    // =============================================
    // MODAL LIFECYCLE
    // =============================================
    $(document).on('shown.bs.modal', '#modalDibujoTablet', function() {
        if (!fabricCanvas) {
            initDibujoCanvas();
        }
        // Siempre resize al mostrar
        setTimeout(function() {
            resizeCanvasToWrapper();
        }, 50);
    });

    $(document).on('hidden.bs.modal', '#modalDibujoTablet', function() {
        resetearEstadoEdicion();
    });

    // Handle resize / orientation change
    var resizeTimer = null;
    window.addEventListener('resize', function() {
        var modal = document.getElementById('modalDibujoTablet');
        if (modal && modal.classList.contains('show') && fabricCanvas) {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(resizeCanvasToWrapper, 150);
        }
    });

})();
