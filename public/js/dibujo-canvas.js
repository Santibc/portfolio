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
    var currentFontSize = 28;
    var canvasSize = 0; // Fixed square size

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
    var spaceHeld = false; // Spacebar held for temporary pan
    var tempPanTimer = null; // Long-press timer for temporary pan
    var tempPanActive = false; // Long-press pan active
    var tempPanStartPoint = null;

    // Pinch state
    var pinchActive = false;
    var pinchStartDist = 0;
    var pinchStartZoom = 1;
    var pinchLastCenter = null;
    var ignoreNextPathCreated = 0; // descartar path:created generado durante pinch
    var lastPinchEndedAt = 0;

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

        // Tamano inicial del canvas - SIEMPRE cuadrado
        var wrapper = document.getElementById('dibujoCanvasWrapper');
        var wW = wrapper ? wrapper.clientWidth : 800;
        var wH = wrapper ? wrapper.clientHeight : 600;
        if (wW < 100) wW = 800;
        if (wH < 100) wH = 600;
        canvasSize = Math.min(wW, wH);

        fabricCanvas = new fabric.Canvas('dibujoCanvas', {
            isDrawingMode: true,
            width: canvasSize,
            height: canvasSize,
            backgroundColor: '#ffffff',
            enableRetinaScaling: true,
            allowTouchScrolling: false
        });

        // ClipPath para restringir dibujo al area cuadrada
        fabricCanvas.clipPath = new fabric.Rect({
            left: 0,
            top: 0,
            width: canvasSize,
            height: canvasSize,
            originX: 'left',
            originY: 'top',
            absolutePositioned: true
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
        bindStylusHoverRejection();
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
        var wW = wrapper.clientWidth;
        var wH = wrapper.clientHeight;
        if (wW < 100 || wH < 100) return;

        // Siempre cuadrado
        canvasSize = Math.min(wW, wH);
        fabricCanvas.setDimensions({ width: canvasSize, height: canvasSize });

        // Actualizar clipPath
        if (fabricCanvas.clipPath) {
            fabricCanvas.clipPath.set({ width: canvasSize, height: canvasSize });
        }

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

        // Mostrar/ocultar selector de tamano de texto
        document.querySelectorAll('.dibujo-text-size-group').forEach(function(el) {
            el.classList.toggle('d-none', tool !== 'text');
        });
    }

    // =============================================
    // HELPER: obtener coordenadas del evento (mouse o touch)
    // =============================================
    function getEventPoint(e) {
        if (e.touches && e.touches.length > 0) {
            return { x: e.touches[0].clientX, y: e.touches[0].clientY };
        }
        return { x: e.clientX, y: e.clientY };
    }

    // =============================================
    // BINDEO DE EVENTOS DEL CANVAS
    // =============================================
    function bindCanvasEvents() {
        // --- MOUSE DOWN ---
        fabricCanvas.on('mouse:down', function(opt) {
            // Si hay pinch activo, ignorar cualquier mouse:down sintetizado por touch
            if (pinchActive) return;
            var e = opt.e;
            // Si es un touch event con mas de 1 dedo, tampoco iniciar nada
            if (e && e.touches && e.touches.length > 1) return;
            var pt = getEventPoint(e);

            // Pan con Alt+click, boton medio, herramienta pan, o spacebar
            if (e.altKey || e.button === 1 || currentTool === 'pan' || spaceHeld) {
                isPanning = true;
                lastPanPoint = pt;
                fabricCanvas.selection = false;
                fabricCanvas.defaultCursor = 'grabbing';
                if (fabricCanvas.upperCanvasEl) fabricCanvas.upperCanvasEl.style.cursor = 'grabbing';
                return;
            }

            // Long-press para pan temporal: si zoom > 1 y no es pan tool
            if (fabricCanvas.getZoom() > 1.01 && currentTool !== 'pan') {
                tempPanStartPoint = pt;
                clearTimeout(tempPanTimer);
                tempPanTimer = setTimeout(function() {
                    tempPanActive = true;
                    isPanning = true;
                    lastPanPoint = tempPanStartPoint;
                    fabricCanvas.isDrawingMode = false;
                    fabricCanvas.defaultCursor = 'grabbing';
                    if (fabricCanvas.upperCanvasEl) fabricCanvas.upperCanvasEl.style.cursor = 'grabbing';
                }, 300);
            }

            // Borrador: eliminar objeto
            // Usamos deteccion manual (containsPoint + distancia a segmento para lineas)
            // porque findTarget falla con shapes creadas con fill transparente o evented:false,
            // y con lineas/flechas cuyo bounding box es demasiado delgado para acertar el click.
            if (currentTool === 'eraser') {
                var ptrErase = fabricCanvas.getPointer(e, false);
                var hit = findEraseTarget(ptrErase);
                if (hit) {
                    saveState();
                    // Si el objeto es parte de una flecha (linea + punta), borrar ambos
                    if (hit.arrowPairId) {
                        var pairId = hit.arrowPairId;
                        fabricCanvas.getObjects().slice().forEach(function(o) {
                            if (o.arrowPairId === pairId) fabricCanvas.remove(o);
                        });
                    } else {
                        fabricCanvas.remove(hit);
                    }
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
                    fontSize: currentFontSize,
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
                // Defensa: si Fabric.js detecto un target arrastrable (p.ej. tras
                // undo/redo que restauro selectable:true por default), abortar el
                // drag y dejar ese objeto inert para que el nuevo trazo no lo mueva.
                if (opt.target) {
                    opt.target.selectable = false;
                    opt.target.evented = false;
                    fabricCanvas.discardActiveObject();
                    if (fabricCanvas._currentTransform) {
                        fabricCanvas._currentTransform = null;
                    }
                }
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
            var pt = getEventPoint(e);

            // Cancelar long-press si el usuario se movio mas de 5px
            if (tempPanTimer && tempPanStartPoint) {
                var dist = Math.hypot(pt.x - tempPanStartPoint.x, pt.y - tempPanStartPoint.y);
                if (dist > 5) {
                    clearTimeout(tempPanTimer);
                    tempPanTimer = null;
                    tempPanStartPoint = null;
                }
            }

            // Pan
            if (isPanning && lastPanPoint) {
                var dx = pt.x - lastPanPoint.x;
                var dy = pt.y - lastPanPoint.y;
                lastPanPoint = pt;
                var vpt = fabricCanvas.viewportTransform;
                vpt[4] += dx;
                vpt[5] += dy;
                clampViewport();
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
            // Cancelar long-press timer
            if (tempPanTimer) {
                clearTimeout(tempPanTimer);
                tempPanTimer = null;
                tempPanStartPoint = null;
            }

            // End pan
            if (isPanning) {
                isPanning = false;
                lastPanPoint = null;

                // Si fue pan temporal (long-press o spacebar), restaurar herramienta
                if (tempPanActive) {
                    tempPanActive = false;
                    setTool(currentTool); // restaura cursor y drawing mode
                } else if (currentTool === 'pan') {
                    fabricCanvas.defaultCursor = 'grab';
                    if (fabricCanvas.upperCanvasEl) fabricCanvas.upperCanvasEl.style.cursor = 'grab';
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

            // La forma reciba selectable/evented solo si la herramienta actual es
            // interactiva (select/eraser/text). Con herramientas de dibujo activas
            // (line/rect/ellipse/arrow/pencil), debe quedar inerte para que un nuevo
            // trazo iniciado sobre ella no la arrastre.
            var nowInteractive = (currentTool === 'select' || currentTool === 'eraser' || currentTool === 'text');

            // Para flecha: agregar punta y vincularla a la linea para que el borrador
            // elimine ambas piezas como una unidad.
            if (currentTool === 'arrow') {
                var arrowHead = createArrowHead(activeShape);
                if (arrowHead) {
                    arrowHead.set({ selectable: nowInteractive, evented: nowInteractive });
                    var pairId = 'arrow_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8);
                    activeShape.arrowPairId = pairId;
                    arrowHead.arrowPairId = pairId;
                    fabricCanvas.add(arrowHead);
                }
            }

            // setCoords() es necesario para que containsPoint() funcione con las
            // dimensiones finales (sin esto el borrador no detecta rects/elipses).
            activeShape.set({ selectable: nowInteractive, evented: nowInteractive });
            activeShape.setCoords();
            activeShape = null;
            shapeOrigin = null;

            // Guardar estado despues de la forma
            saveState();
        });

        // --- PATH CREATED (free draw) ---
        fabricCanvas.on('path:created', function(opt) {
            // Si veniamos de un pinch, descartar el path generado por el dedo inicial
            if (ignoreNextPathCreated > 0) {
                ignoreNextPathCreated--;
                if (opt && opt.path) {
                    fabricCanvas.remove(opt.path);
                    fabricCanvas.requestRenderAll();
                }
                return;
            }
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
            clampViewport();
            updateZoomLabel();
            opt.e.preventDefault();
            opt.e.stopPropagation();
        });
    }

    // =============================================
    // FLECHA (arrowhead)
    // =============================================
    // Distancia de un punto a un segmento de linea (en coordenadas de canvas)
    function pointToSegmentDistance(px, py, x1, y1, x2, y2) {
        var dx = x2 - x1;
        var dy = y2 - y1;
        var lenSq = dx * dx + dy * dy;
        if (lenSq === 0) return Math.hypot(px - x1, py - y1);
        var t = ((px - x1) * dx + (py - y1) * dy) / lenSq;
        t = Math.max(0, Math.min(1, t));
        var projX = x1 + t * dx;
        var projY = y1 + t * dy;
        return Math.hypot(px - projX, py - projY);
    }

    // Busca un objeto del canvas bajo el puntero para el borrador.
    // Itera de arriba hacia abajo (los ultimos dibujados primero) y prueba:
    //  - Lineas: distancia al segmento (para que sean facil de tocar)
    //  - Resto: containsPoint (bounding box)
    function findEraseTarget(pointer) {
        var objects = fabricCanvas.getObjects();
        // Tolerancia adaptada al zoom para que las lineas sean clickeables
        var zoom = fabricCanvas.getZoom() || 1;
        var lineTolerance = Math.max(8 / zoom, 6);
        for (var i = objects.length - 1; i >= 0; i--) {
            var obj = objects[i];
            if (!obj || obj.excludeFromExport === true) continue;
            if (obj.type === 'line') {
                // Coordenadas absolutas del segmento
                var pts = obj.calcLinePoints();
                var center = obj.getCenterPoint();
                var ax = center.x + pts.x1;
                var ay = center.y + pts.y1;
                var bx = center.x + pts.x2;
                var by = center.y + pts.y2;
                var dist = pointToSegmentDistance(pointer.x, pointer.y, ax, ay, bx, by);
                if (dist <= lineTolerance + (obj.strokeWidth || 0) / 2) {
                    return obj;
                }
            } else {
                try {
                    if (obj.containsPoint(pointer)) {
                        return obj;
                    }
                } catch (err) { /* ignore */ }
            }
        }
        return null;
    }

    function createArrowHead(line) {
        if (!line) return null;
        // Obtener coordenadas absolutas del canvas (no relativas al objeto)
        var points = line.calcLinePoints();
        var center = line.getCenterPoint();
        var x1 = center.x + points.x1;
        var y1 = center.y + points.y1;
        var x2 = center.x + points.x2;
        var y2 = center.y + points.y2;
        var lineLen = Math.hypot(x2 - x1, y2 - y1);
        // No crear punta si la linea es muy corta
        if (lineLen < 5) return null;

        var angle = Math.atan2(y2 - y1, x2 - x1) * 180 / Math.PI;
        // Tamano proporcional al grosor de la linea
        var headW = Math.max(currentWidth * 3, 10);
        var headH = Math.max(currentWidth * 4, 14);
        return new fabric.Triangle({
            left: x2,
            top: y2,
            originX: 'center',
            originY: 'center',
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
            var json = JSON.stringify(fabricCanvas.toJSON(['arrowPairId']));
            undoStack.push(json);
            if (undoStack.length > maxUndo) undoStack.shift();
        } finally {
            isSavingState = false;
        }
    }

    // Reaplica selectable/evented a todos los objetos segun la herramienta actual.
    // Fabric.js NO serializa estas propiedades en toJSON por defecto, asi que tras
    // un loadFromJSON los objetos vuelven a su default (selectable:true, evented:true)
    // y podrian ser arrastrados al iniciar un nuevo trazo sobre ellos.
    function reaplicarInteractividad() {
        if (!fabricCanvas) return;
        var interactive = (currentTool === 'select' || currentTool === 'eraser' || currentTool === 'text');
        fabricCanvas.forEachObject(function(obj) {
            obj.selectable = interactive;
            obj.evented = interactive;
        });
    }

    window.deshacerDibujo = function() {
        if (!fabricCanvas || undoStack.length <= 1) return;
        isSavingState = true;
        redoStack.push(undoStack.pop());
        var prevState = undoStack[undoStack.length - 1];
        fabricCanvas.loadFromJSON(prevState, function() {
            reaplicarInteractividad();
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
            reaplicarInteractividad();
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
            clampViewport();
            updateZoomLabel();
            fabricCanvas.renderAll();
            return;
        }
        zoom = Math.min(Math.max(zoom, 0.25), 10);
        var center = { x: fabricCanvas.width / 2, y: fabricCanvas.height / 2 };
        fabricCanvas.zoomToPoint(new fabric.Point(center.x, center.y), zoom);
        clampViewport();
        updateZoomLabel();
    };

    function updateZoomLabel() {
        var label = document.getElementById('dibujoZoomLabel');
        if (label && fabricCanvas) {
            label.textContent = Math.round(fabricCanvas.getZoom() * 100) + '%';
        }
    }

    // Restringir viewport para no salir del area cuadrada
    function clampViewport() {
        if (!fabricCanvas || !canvasSize) return;
        var zoom = fabricCanvas.getZoom();
        var vpt = fabricCanvas.viewportTransform;

        if (zoom < 1) {
            // Centrar cuando esta en zoom out
            vpt[4] = (canvasSize - canvasSize * zoom) / 2;
            vpt[5] = (canvasSize - canvasSize * zoom) / 2;
        } else {
            // Limitar pan cuando esta en zoom in
            var maxPanX = 0;
            var minPanX = canvasSize * (1 - zoom);
            var maxPanY = 0;
            var minPanY = canvasSize * (1 - zoom);

            if (vpt[4] > maxPanX) vpt[4] = maxPanX;
            if (vpt[4] < minPanX) vpt[4] = minPanX;
            if (vpt[5] > maxPanY) vpt[5] = maxPanY;
            if (vpt[5] < minPanY) vpt[5] = minPanY;
        }

        fabricCanvas.setViewportTransform(vpt);
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
                var wasDrawing = fabricCanvas.isDrawingMode;
                if (wasDrawing) {
                    fabricCanvas.isDrawingMode = false;
                }
                // Abortar cualquier trazo en curso del primer dedo:
                // - vaciar puntos del brush
                // - limpiar la capa contextTop (tinta provisional)
                // - marcar para descartar el proximo path:created
                var brush = fabricCanvas.freeDrawingBrush;
                if (brush) {
                    brush._points = [];
                    if (typeof brush._reset === 'function') {
                        try { brush._reset(); } catch(_) {}
                    }
                }
                if (fabricCanvas.contextTop) {
                    fabricCanvas.clearContext(fabricCanvas.contextTop);
                }
                if (wasDrawing) {
                    ignoreNextPathCreated = 1;
                }
                // Cancelar shape drawing en curso (linea/rect/elipse/flecha)
                if (isShapeDrawing && activeShape) {
                    fabricCanvas.remove(activeShape);
                    isShapeDrawing = false;
                    activeShape = null;
                    fabricCanvas.requestRenderAll();
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
                clampViewport();
                fabricCanvas.requestRenderAll();
                updateZoomLabel();
            }
        }, { passive: false });

        upperCanvas.addEventListener('touchend', function(e) {
            if (pinchActive && e.touches.length < 2) {
                pinchActive = false;
                lastPinchEndedAt = Date.now();
                // Restaurar modo dibujo si estamos en pencil o white-brush
                if (currentTool === 'pencil' || currentTool === 'white-brush') {
                    fabricCanvas.isDrawingMode = true;
                }
                // Limpieza de seguridad: si quedo un flag huerfano, resetearlo
                // tras un breve delay para no descartar trazos legitimos posteriores
                setTimeout(function() {
                    if (!pinchActive && Date.now() - lastPinchEndedAt >= 800) {
                        ignoreNextPathCreated = 0;
                    }
                }, 1000);
            }
        });
    }

    // =============================================
    // RECHAZO DE HOVER DE STYLUS (S-Pen, Apple Pencil, Wacom)
    // =============================================
    // Los lapices modernos emiten eventos pointer cuando se acercan a la pantalla
    // sin tocar (hover detection). Sin este filtro, Fabric.js los procesa como
    // dibujo activo y traza lineas con el lapiz "en el aire".
    //
    // Solucion: en fase de captura interceptamos pointerdown/pointermove de tipo
    // 'pen' que llegan sin contacto fisico (buttons === 0 y pressure === 0).
    // pointerup se deja pasar siempre para que Fabric.js pueda cerrar trazos.
    function bindStylusHoverRejection() {
        if (!fabricCanvas) return;
        if (typeof window.PointerEvent === 'undefined') return;
        var upperCanvas = fabricCanvas.upperCanvasEl;
        if (!upperCanvas) return;

        function rejectStylusHover(e) {
            if (e.pointerType !== 'pen') return;
            // pointerup siempre pasa: si quedo un trazo abierto, debe cerrarse.
            if (e.type === 'pointerup' || e.type === 'pointercancel') return;
            // Contacto real: algun boton presionado o presion fisica > 0.
            var hasContact = e.buttons > 0 || (typeof e.pressure === 'number' && e.pressure > 0);
            if (!hasContact) {
                e.stopImmediatePropagation();
                e.preventDefault();
            }
        }

        upperCanvas.addEventListener('pointerdown', rejectStylusHover, true);
        upperCanvas.addEventListener('pointermove', rejectStylusHover, true);
        // Tambien filtramos pointerover/pointerenter por si algun navegador
        // los convierte en eventos de dibujo (caso raro pero seguro).
        upperCanvas.addEventListener('pointerover', rejectStylusHover, true);
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
            // Spacebar = Pan temporal (como Photoshop/Figma)
            else if (e.key === ' ' || e.code === 'Space') {
                e.preventDefault();
                if (!spaceHeld) {
                    spaceHeld = true;
                    fabricCanvas.isDrawingMode = false;
                    fabricCanvas.defaultCursor = 'grab';
                    if (fabricCanvas.upperCanvasEl) fabricCanvas.upperCanvasEl.style.cursor = 'grab';
                }
            }
        });

        document.addEventListener('keyup', function(e) {
            var modal = document.getElementById('modalDibujoTablet');
            if (!modal || !modal.classList.contains('show')) return;

            if (e.key === ' ' || e.code === 'Space') {
                e.preventDefault();
                spaceHeld = false;
                isPanning = false;
                lastPanPoint = null;
                // Restaurar herramienta actual
                setTool(currentTool);
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

    $(document).on('click', '.dibujo-fontsize', function() {
        currentFontSize = parseInt($(this).data('fontsize'));
        // Actualizar botones activos
        document.querySelectorAll('.dibujo-fontsize').forEach(function(btn) {
            btn.classList.toggle('active', parseInt(btn.dataset.fontsize) === currentFontSize);
        });
        // Si hay un texto seleccionado, cambiarle el tamano
        if (fabricCanvas) {
            var activeObj = fabricCanvas.getActiveObject();
            if (activeObj && activeObj.type === 'i-text') {
                activeObj.set('fontSize', currentFontSize);
                fabricCanvas.renderAll();
                saveState();
            }
        }
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
