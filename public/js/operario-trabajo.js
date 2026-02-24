/**
 * SINDEN - Operario: Vista de Trabajo (operario-trabajo.js)
 * Maneja: sliders, fotos, transferencias, actualizar orden, heartbeat, inactividad, forzar cierre.
 */

(function() {
    'use strict';

    var piezasCambios = {};
    var heartbeatInterval = null;
    var inactivityTimer = null;
    var forceCloseInterval = null;

    // ==========================================
    // INIT
    // ==========================================
    $(function() {
        if (!LOCK_ACQUIRED) return;

        initSliders();
        initFotoUploads();
        initTransferButtons();
        initDejarColaButtons();
        initActualizarOrden();
        startHeartbeat();
        startInactivityTimer();
        startForceCloseCheck();

        // Release lock on page unload
        $(window).on('beforeunload', function() {
            if (navigator.sendBeacon) {
                var data = new URLSearchParams({ _token: CSRF_TOKEN });
                navigator.sendBeacon(OPERARIO_ROUTES.desbloquear, data);
            }
        });
    });

    // ==========================================
    // SLIDER <-> INPUT SYNC
    // ==========================================
    function initSliders() {
        // Slider change -> update input
        $(document).on('input', '.pieza-slider', function() {
            var piezaId = $(this).data('pieza-id');
            var val = parseInt($(this).val());
            $('input.pieza-porcentaje-input[data-pieza-id="' + piezaId + '"]').val(val);
            updatePorcentajeDisplay(piezaId, val);
            trackCambio(piezaId, val);
        });

        // Input change -> update slider
        $(document).on('input', '.pieza-porcentaje-input', function() {
            var piezaId = $(this).data('pieza-id');
            var val = Math.max(0, Math.min(100, parseInt($(this).val()) || 0));
            $('input.pieza-slider[data-pieza-id="' + piezaId + '"]').val(val);
            updatePorcentajeDisplay(piezaId, val);
            trackCambio(piezaId, val);
        });
    }

    function updatePorcentajeDisplay(piezaId, val) {
        var card = $('#pieza-' + piezaId);
        var display = card.find('.pieza-porcentaje-display');
        display.text(val + '%');

        // Update color
        display.removeClass('text-success text-warning text-info text-muted');
        if (val >= 100) display.addClass('text-success');
        else if (val >= 50) display.addClass('text-warning');
        else if (val > 0) display.addClass('text-info');
        else display.addClass('text-muted');
    }

    function trackCambio(piezaId, porcentaje) {
        var original = parseFloat($('#pieza-' + piezaId).data('porcentaje-original'));
        if (Math.abs(porcentaje - original) < 0.5) {
            delete piezasCambios[piezaId];
        } else {
            piezasCambios[piezaId] = porcentaje;
        }
    }

    // ==========================================
    // FOTO UPLOAD
    // ==========================================
    function initFotoUploads() {
        $(document).on('change', '.foto-input', function() {
            var input = this;
            var piezaId = $(this).data('pieza-id');
            var file = input.files[0];

            if (!file) return;

            var reader = new FileReader();
            reader.onload = function(e) {
                Swal.fire({
                    title: 'Esta bien la foto?',
                    imageUrl: e.target.result,
                    imageWidth: 300,
                    imageAlt: 'Preview',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Repetir',
                    confirmButtonColor: '#4A7C59'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        subirFotoAjax(piezaId, file);
                    }
                    // Clear input para permitir seleccionar la misma foto
                    $(input).val('');
                });
            };
            reader.readAsDataURL(file);
        });
    }

    function subirFotoAjax(piezaId, file) {
        var formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('foto', file);

        $.ajax({
            url: '/operario/piezas/' + piezaId + '/foto',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.success) {
                    showToast('success', 'Foto subida', 'La foto se adjunto correctamente.');
                    // Add thumbnail to preview area
                    var previewHtml = '<img src="' + data.foto.url + '" class="foto-thumb rounded border" style="width:60px;height:60px;object-fit:cover;">';
                    $('#fotoPreview' + piezaId).append(previewHtml);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo subir la foto.' });
                }
            },
            error: function(xhr) {
                var msg = 'No se pudo subir la foto.';
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.foto) {
                    msg = xhr.responseJSON.errors.foto[0];
                }
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    }

    // ==========================================
    // TRANSFERIR PIEZA
    // ==========================================
    function initTransferButtons() {
        $(document).on('click', '.btn-transferir', function() {
            var piezaId = $(this).data('pieza-id');
            var piezaNombre = $(this).data('pieza-nombre');

            $('#transferirPiezaId').val(piezaId);
            $('#transferirPiezaNombre').text(piezaNombre);
            $('#transferirOperarioSelect').val('');
            $('#transferirNotas').val('');

            new bootstrap.Modal(document.getElementById('modalTransferir')).show();
        });

        $('#btnConfirmarTransferencia').on('click', function() {
            var piezaId = $('#transferirPiezaId').val();
            var operarioId = $('#transferirOperarioSelect').val();
            var notas = $('#transferirNotas').val();

            if (!operarioId) {
                Swal.fire({ icon: 'warning', title: 'Selecciona un operario', text: 'Debes elegir a quien transferir la pieza.' });
                return;
            }

            var btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: '/operario/piezas/' + piezaId + '/transferir',
                method: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    nuevo_operario_id: operarioId,
                    notas: notas
                },
                success: function(data) {
                    bootstrap.Modal.getInstance(document.getElementById('modalTransferir')).hide();
                    btn.prop('disabled', false);

                    if (data.success) {
                        showToast('success', 'Pieza transferida', 'La pieza fue transferida a ' + data.nuevo_operario);
                        $('#pieza-' + piezaId).fadeOut(400, function() { $(this).remove(); });
                        delete piezasCambios[piezaId];
                        checkPiezasRestantes();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'No se pudo transferir.' });
                    }
                },
                error: function() {
                    btn.prop('disabled', false);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo completar la transferencia.' });
                }
            });
        });
    }

    // ==========================================
    // DEJAR EN COLA
    // ==========================================
    function initDejarColaButtons() {
        $(document).on('click', '.btn-dejar-cola', function() {
            var piezaId = $(this).data('pieza-id');
            var piezaNombre = $(this).data('pieza-nombre');

            Swal.fire({
                title: 'Dejar en cola general?',
                html: 'La pieza <b>' + piezaNombre + '</b> quedara disponible para que otro operario la tome.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Si, dejar en cola',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/operario/piezas/' + piezaId + '/dejar-cola',
                        method: 'POST',
                        data: { _token: CSRF_TOKEN },
                        success: function(data) {
                            if (data.success) {
                                showToast('success', 'Pieza liberada', 'La pieza fue dejada en la cola general.');
                                $('#pieza-' + piezaId).fadeOut(400, function() { $(this).remove(); });
                                delete piezasCambios[piezaId];
                                checkPiezasRestantes();
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'No se pudo liberar la pieza.' });
                            }
                        },
                        error: function() {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo completar la operacion.' });
                        }
                    });
                }
            });
        });
    }

    // ==========================================
    // ACTUALIZAR ORDEN (MAIN SAVE)
    // ==========================================
    function initActualizarOrden() {
        $('#btnActualizarOrden').on('click', function() {
            var cambios = recopilarCambios();

            if (cambios.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin cambios',
                    text: 'No se modifico porcentaje de alguna pieza, esta seguro que no hizo algun avance?',
                    showCancelButton: true,
                    confirmButtonText: 'Si, no hice cambios',
                    cancelButtonText: 'Volver a revisar'
                });
                return;
            }

            // Check for pieces at 100%
            var piezasTerminadas = [];
            var todasPiezas100 = true;

            $('.pieza-trabajo').each(function() {
                var piezaId = $(this).data('pieza-id');
                var slider = $(this).find('.pieza-slider');
                var val = parseInt(slider.val());

                if (val < 100) {
                    todasPiezas100 = false;
                }
            });

            cambios.forEach(function(c) {
                if (c.porcentaje >= 100) {
                    var nombre = $('#pieza-' + c.pieza_id).find('.fw-bold').first().text().trim();
                    piezasTerminadas.push(nombre);
                }
            });

            if (todasPiezas100 && piezasTerminadas.length > 0) {
                // Case 3: ALL pieces at 100%
                Swal.fire({
                    title: 'Orden Completada?',
                    html: 'Esta seguro de colocar la Orden <b>' + NUMERO_ORDEN + '</b> como EJECUTADA?<br><br>Todas las piezas quedaran al 100%.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Si, Ejecutar Orden',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#28a745'
                }).then(function(r) { if (r.isConfirmed) enviarActualizacion(cambios); });
            } else if (piezasTerminadas.length > 0) {
                // Case 2: Some pieces at 100%
                Swal.fire({
                    title: 'Piezas Terminadas',
                    html: 'Esta seguro de colocar terminada(s) esta(s) pieza(s)?<br><b>' + piezasTerminadas.join(', ') + '</b>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Si, Actualizar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#4A7C59'
                }).then(function(r) { if (r.isConfirmed) enviarActualizacion(cambios); });
            } else {
                // Normal update
                enviarActualizacion(cambios);
            }
        });
    }

    function recopilarCambios() {
        var cambios = [];
        for (var piezaId in piezasCambios) {
            cambios.push({
                pieza_id: parseInt(piezaId),
                porcentaje: piezasCambios[piezaId]
            });
        }
        return cambios;
    }

    function enviarActualizacion(cambios) {
        var btn = $('#btnActualizarOrden');
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Actualizando...');

        $.ajax({
            url: OPERARIO_ROUTES.actualizarAvances,
            method: 'POST',
            data: JSON.stringify({ _token: CSRF_TOKEN, cambios: cambios }),
            contentType: 'application/json',
            success: function(data) {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-2"></i>ACTUALIZAR ORDEN');

                if (data.success) {
                    // Update originals
                    cambios.forEach(function(c) {
                        $('#pieza-' + c.pieza_id).data('porcentaje-original', c.porcentaje);
                    });
                    piezasCambios = {};

                    var msg = data.piezas_actualizadas + ' pieza(s) actualizada(s).';
                    if (data.piezas_terminadas && data.piezas_terminadas.length > 0) {
                        msg += ' Terminadas: ' + data.piezas_terminadas.join(', ') + '.';
                    }

                    showToast('success', 'Avances guardados', msg);

                    if (data.orden_ejecutada) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Orden Ejecutada!',
                            text: 'La orden ' + NUMERO_ORDEN + ' ha sido marcada como EJECUTADA.',
                            confirmButtonColor: '#28a745'
                        }).then(function() {
                            window.location.href = OPERARIO_ROUTES.ordenesAsignadas;
                        });
                    } else {
                        // Reload to show updated historial
                        setTimeout(function() { location.reload(); }, 1500);
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'No se pudieron guardar los avances.' });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="bi bi-check-lg me-2"></i>ACTUALIZAR ORDEN');
                var msg = 'No se pudieron guardar los avances.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        });
    }

    // ==========================================
    // HEARTBEAT
    // ==========================================
    function startHeartbeat() {
        heartbeatInterval = setInterval(function() {
            $.post(OPERARIO_ROUTES.heartbeat, { _token: CSRF_TOKEN });
        }, 30000); // Every 30 seconds
    }

    // ==========================================
    // INACTIVITY TIMER
    // ==========================================
    function startInactivityTimer() {
        var warningShown = false;

        function resetTimer() {
            if (warningShown) return; // Don't reset if warning is showing
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(showInactivityWarning, TIMEOUT_INACTIVIDAD * 0.8);
        }

        function showInactivityWarning() {
            warningShown = true;
            var timerMs = Math.round(TIMEOUT_INACTIVIDAD * 0.2);

            Swal.fire({
                title: 'Inactividad detectada',
                html: 'Se cerrara la sesion de trabajo por inactividad...',
                icon: 'warning',
                timer: timerMs,
                timerProgressBar: true,
                showConfirmButton: true,
                confirmButtonText: 'Seguir trabajando',
                allowOutsideClick: false
            }).then(function(result) {
                warningShown = false;
                if (result.dismiss === Swal.DismissReason.timer) {
                    // Timeout reached - release lock and redirect
                    cerrarSesionTrabajo('La sesion de esta orden se cerro por inactividad.');
                } else {
                    // User clicked "Seguir trabajando"
                    $.post(OPERARIO_ROUTES.heartbeat, { _token: CSRF_TOKEN });
                    resetTimer();
                }
            });
        }

        $(document).on('mousemove keydown click touchstart', debounce(resetTimer, 1000));
        resetTimer();
    }

    // ==========================================
    // FORCE CLOSE CHECK
    // ==========================================
    function startForceCloseCheck() {
        forceCloseInterval = setInterval(function() {
            $.get(OPERARIO_ROUTES.estadoBloqueo).done(function(data) {
                if (data.force_close) {
                    clearInterval(forceCloseInterval);
                    clearInterval(heartbeatInterval);

                    var seconds = data.force_close_seconds_remaining || 60;

                    Swal.fire({
                        title: 'Cierre Requerido',
                        html: 'Un usuario de mayor jerarquia necesita editar esta orden.<br>Se cerrara en <b id="forceCountdown">' + seconds + '</b> segundos.<br><br>Su progreso se guardara automaticamente.',
                        icon: 'warning',
                        timer: seconds * 1000,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        showConfirmButton: true,
                        confirmButtonText: 'Cerrar ahora'
                    }).then(function() {
                        // Save progress before leaving
                        var cambios = recopilarCambios();
                        if (cambios.length > 0) {
                            enviarActualizacionSilenciosa(cambios, function() {
                                cerrarSesionTrabajo('La orden fue cerrada por el sistema. Su progreso se guardo automaticamente.');
                            });
                        } else {
                            cerrarSesionTrabajo('La orden fue cerrada por el sistema.');
                        }
                    });

                    // Countdown animation
                    var countdownInterval = setInterval(function() {
                        seconds--;
                        var el = document.getElementById('forceCountdown');
                        if (el) el.textContent = Math.max(0, seconds);
                        if (seconds <= 0) clearInterval(countdownInterval);
                    }, 1000);

                } else if (data.force_closed || !data.locked) {
                    // Lock was released externally
                    clearInterval(forceCloseInterval);
                    cerrarSesionTrabajo('La sesion de trabajo fue cerrada.');
                }
            });
        }, 10000); // Check every 10 seconds
    }

    // ==========================================
    // HELPERS
    // ==========================================
    function enviarActualizacionSilenciosa(cambios, callback) {
        $.ajax({
            url: OPERARIO_ROUTES.actualizarAvances,
            method: 'POST',
            data: JSON.stringify({ _token: CSRF_TOKEN, cambios: cambios }),
            contentType: 'application/json',
            complete: function() {
                if (callback) callback();
            }
        });
    }

    function cerrarSesionTrabajo(mensaje) {
        $.post(OPERARIO_ROUTES.desbloquear, { _token: CSRF_TOKEN }, function() {
            Swal.fire({
                icon: 'info',
                title: 'Sesion cerrada',
                text: mensaje,
                confirmButtonColor: '#4A7C59'
            }).then(function() {
                window.location.href = OPERARIO_ROUTES.ordenesAsignadas;
            });
        });
    }

    function checkPiezasRestantes() {
        if ($('.pieza-trabajo:visible').length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Sin piezas',
                text: 'Ya no tienes piezas asignadas en esta orden.',
                confirmButtonColor: '#4A7C59'
            }).then(function() {
                window.location.href = OPERARIO_ROUTES.ordenesAsignadas;
            });
        }
    }

})();
