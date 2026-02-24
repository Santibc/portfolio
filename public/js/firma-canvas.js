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
        return { x: e.clientX - rect.left, y: e.clientY - rect.top };
    }

    function getTouchPos(e) {
        var rect = canvas.getBoundingClientRect();
        var touch = e.touches[0];
        return { x: touch.clientX - rect.left, y: touch.clientY - rect.top };
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
    }

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
