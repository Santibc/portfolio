/**
 * SINDEN - Firma Canvas
 * Pad de firma digital con soporte mouse + touch.
 */
(function() {
    var canvas, ctx, isDrawing = false;
    var lastX = 0, lastY = 0;

    window.initFirmaCanvas = function() {
        canvas = document.getElementById('firmaCanvas');
        if (!canvas) return;
        ctx = canvas.getContext('2d');

        // Ajustar resolucion al tamano real
        var rect = canvas.getBoundingClientRect();
        canvas.width = rect.width || 600;
        canvas.height = rect.height || 200;

        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        // Mouse
        canvas.addEventListener('mousedown', function(e) { startDraw(getMousePos(e)); });
        canvas.addEventListener('mousemove', function(e) { if (isDrawing) draw(getMousePos(e)); });
        canvas.addEventListener('mouseup', endDraw);
        canvas.addEventListener('mouseleave', endDraw);

        // Touch
        canvas.addEventListener('touchstart', function(e) {
            e.preventDefault();
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
    };

    function getMousePos(e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        return { x: (e.clientX - rect.left) * scaleX, y: (e.clientY - rect.top) * scaleY };
    }

    function getTouchPos(e) {
        var rect = canvas.getBoundingClientRect();
        var scaleX = canvas.width / rect.width;
        var scaleY = canvas.height / rect.height;
        var touch = e.touches[0];
        return { x: (touch.clientX - rect.left) * scaleX, y: (touch.clientY - rect.top) * scaleY };
    }

    function startDraw(pos) {
        isDrawing = true;
        lastX = pos.x;
        lastY = pos.y;
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
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
        // Actualizar estado del wizard
        if (typeof wizardState !== 'undefined') {
            wizardState.firmaData = obtenerFirmaData();
        }
        // Marcar step 4 como completado
        if (typeof marcarStepCompletado === 'function') {
            marcarStepCompletado(4);
        }
        // Disparar auto-guardado (canvas no emite eventos input/change)
        if (typeof triggerAutoSave === 'function') {
            triggerAutoSave('firma');
        }
    }

    // Pinta una firma existente sobre el canvas (modo edicion)
    window.cargarFirmaEnCanvas = function(src) {
        if (!canvas || !ctx || !src) return;
        var img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            if (typeof wizardState !== 'undefined') {
                wizardState.firmaData = obtenerFirmaData();
            }
        };
        img.onerror = function() {
            console.warn('No se pudo cargar la firma existente:', src);
        };
        img.src = src;
    };

    window.limpiarFirma = function() {
        if (!canvas || !ctx) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (typeof wizardState !== 'undefined') {
            wizardState.firmaData = null;
        }
        if (typeof desmarcarStep === 'function') {
            desmarcarStep(4);
        }
    };

    window.obtenerFirmaData = function() {
        if (!canvas) return null;
        // Verificar si el canvas tiene dibujo (no esta en blanco)
        var blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        if (canvas.toDataURL() === blank.toDataURL()) return null;
        return canvas.toDataURL('image/png');
    };
})();
