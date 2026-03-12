/**
 * SINDEN - Conexion Handler (conexion-handler.js)
 * Maneja: deteccion online/offline, banner UI, intercepcion AJAX,
 * backup en localStorage, sincronizacion al reconectar, recuperacion de datos.
 */
window.SindenConexion = (function($) {
    'use strict';

    // ==========================================
    // ESTADO INTERNO
    // ==========================================
    var state = {
        online: navigator.onLine,
        lastPingSuccess: null,
        syncInProgress: false,
        pingInterval: null,
        initialized: false
    };

    var CONFIG = {
        PING_URL: '/api/ping',
        CSRF_REFRESH_URL: '/api/csrf-refresh',
        PING_INTERVAL_ONLINE: 15000,   // 15s cuando online
        PING_INTERVAL_OFFLINE: 5000,   // 5s cuando offline
        PING_TIMEOUT: 5000,            // timeout del ping
        LS_PREFIX: 'sinden_cx_',
        MAX_QUEUE_AGE_MS: 3600000,     // 1 hora max para datos encolados
        // URLs que se ignoran en deteccion de errores
        IGNORED_URL_PATTERNS: ['/heartbeat', 'draw=', '/api/ping', '/notificaciones']
    };

    // ==========================================
    // INICIALIZACION
    // ==========================================
    function init() {
        if (state.initialized) return;
        state.initialized = true;

        // Eventos del navegador
        window.addEventListener('online', function() { doPing(); });
        window.addEventListener('offline', function() { setOffline(); });

        // Interceptor AJAX global
        setupAjaxInterceptor();
        setupGlobalAjaxError();

        // Iniciar ping
        startPing();

        // Verificar datos de recuperacion
        setTimeout(function() { checkRecoveryData(); }, 1500);

        // Limpiar datos expirados
        cleanExpiredData();
    }

    // ==========================================
    // DETECCION: PING ACTIVO
    // ==========================================
    function startPing() {
        if (state.pingInterval) clearInterval(state.pingInterval);

        var interval = state.online ? CONFIG.PING_INTERVAL_ONLINE : CONFIG.PING_INTERVAL_OFFLINE;
        state.pingInterval = setInterval(function() { doPing(); }, interval);
    }

    function doPing() {
        $.ajax({
            url: CONFIG.PING_URL,
            method: 'GET',
            timeout: CONFIG.PING_TIMEOUT,
            global: false, // No dispara ajaxError global
            success: function() {
                state.lastPingSuccess = Date.now();
                if (!state.online) setOnline();
            },
            error: function(xhr) {
                // Solo marcar offline si es error de red (status 0), no errores HTTP
                if (xhr.status === 0) {
                    if (state.online) setOffline();
                }
            }
        });
    }

    // ==========================================
    // TRANSICIONES DE ESTADO
    // ==========================================
    function setOnline() {
        if (state.online) return;
        state.online = true;

        // UI
        hideOfflineBanner();
        updateIndicator(true);
        enableSubmitButtons();

        // Reiniciar ping con intervalo normal
        startPing();

        // Sincronizar datos pendientes
        syncPendingData();
    }

    function setOffline() {
        if (!state.online) return;
        state.online = false;

        // UI
        showOfflineBanner();
        updateIndicator(false);
        disableSubmitButtons();

        // Reiniciar ping con intervalo rapido
        startPing();
    }

    function isOnline() {
        return state.online;
    }

    // ==========================================
    // UI: BANNER OFFLINE
    // ==========================================
    function showOfflineBanner() {
        var banner = $('#sindenOfflineBanner');
        if (banner.length) {
            banner.addClass('visible');
        }
    }

    function hideOfflineBanner() {
        var banner = $('#sindenOfflineBanner');
        if (banner.length) {
            banner.removeClass('visible');
        }
    }

    // ==========================================
    // UI: INDICADOR EN HEADER
    // ==========================================
    function updateIndicator(online) {
        var dot = $('#conexionDot');
        if (!dot.length) return;

        if (online) {
            dot.removeClass('offline').addClass('online').attr('title', 'Conectado');
        } else {
            dot.removeClass('online').addClass('offline').attr('title', 'Sin conexion');
        }
    }

    // ==========================================
    // UI: DESHABILITAR/HABILITAR BOTONES
    // ==========================================
    var SUBMIT_SELECTORS = [
        'button[type="submit"]',
        '#btnActualizarOrden',
        '#btnGuardar',
        '#btnGenerar',
        '#btnRegistrarPago',
        '.btn-aprobar-pago',
        '.btn-aprobar-masivo',
        '[data-action="guardar"]',
        '[data-action="generar"]'
    ].join(', ');

    function disableSubmitButtons() {
        $(SUBMIT_SELECTORS).each(function() {
            var $btn = $(this);
            if (!$btn.prop('disabled')) {
                $btn.prop('disabled', true).attr('data-offline-disabled', 'true');
            }
        });
    }

    function enableSubmitButtons() {
        $('[data-offline-disabled="true"]').each(function() {
            $(this).prop('disabled', false).removeAttr('data-offline-disabled');
        });
    }

    // ==========================================
    // INTERCEPCION AJAX: $.ajaxPrefilter
    // ==========================================
    function setupAjaxInterceptor() {
        $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
            // No interceptar pings propios
            if (options.url && options.url.indexOf('/api/ping') !== -1) return;
            if (options.url && options.url.indexOf('/api/csrf-refresh') !== -1) return;

            // Si estamos online, solo inyectar CSRF actualizado
            if (state.online) {
                injectCsrfToken(options);
                return;
            }

            // OFFLINE: determinar accion segun metodo y tipo de datos
            var method = (options.type || options.method || 'GET').toUpperCase();

            if (method === 'GET') {
                // GETs offline: abortar silenciosamente (excepto los ignorados)
                if (!isIgnoredUrl(options.url)) {
                    showToast('warning', 'Sin conexion', 'No se puede cargar la informacion sin conexion.');
                }
                jqXHR.abort();
                return;
            }

            // POST/PUT/DELETE offline
            var isFormData = options.data instanceof FormData ||
                            (options.contentType && options.contentType.indexOf('multipart') !== -1);

            if (isFormData) {
                // Archivos: no se pueden encolar
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin conexion',
                    text: 'No se pueden enviar archivos sin conexion. Intente de nuevo cuando se restablezca la conexion.',
                    confirmButtonColor: '#4A7C59'
                });
                jqXHR.abort();
                return;
            }

            // JSON/form data: encolar
            var queueItem = {
                url: options.url,
                method: method,
                data: options.data,
                contentType: options.contentType || 'application/x-www-form-urlencoded',
                timestamp: Date.now()
            };

            addToQueue(queueItem);
            showToast('info', 'Guardado localmente', 'Se enviara automaticamente al reconectar.');
            jqXHR.abort();
        });
    }

    // ==========================================
    // INTERCEPCION AJAX: Error global
    // ==========================================
    function setupGlobalAjaxError() {
        $(document).ajaxError(function(event, xhr, settings) {
            // Ignorar requests abortados (por nosotros o el usuario)
            if (xhr.statusText === 'abort') return;

            // Ignorar URLs que sabemos pueden fallar sin ser offline
            if (isIgnoredUrl(settings.url)) return;

            // Status 0 = error de red (sin respuesta del servidor)
            if (xhr.status === 0 && xhr.readyState === 0) {
                if (state.online) {
                    setOffline();
                }
            }

            // 419 = CSRF token expirado
            if (xhr.status === 419) {
                refreshCsrfToken().then(function() {
                    showToast('info', 'Sesion actualizada', 'Intente la operacion de nuevo.');
                });
            }

            // 401 = sesion expirada
            if (xhr.status === 401) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sesion expirada',
                    text: 'Su sesion ha expirado. Sera redirigido al inicio de sesion.',
                    confirmButtonColor: '#4A7C59'
                }).then(function() {
                    window.location.href = '/login';
                });
            }
        });
    }

    // ==========================================
    // HELPERS AJAX
    // ==========================================
    function isIgnoredUrl(url) {
        if (!url) return false;
        for (var i = 0; i < CONFIG.IGNORED_URL_PATTERNS.length; i++) {
            if (url.indexOf(CONFIG.IGNORED_URL_PATTERNS[i]) !== -1) return true;
        }
        return false;
    }

    function injectCsrfToken(options) {
        var token = $('meta[name="csrf-token"]').attr('content');
        if (token) {
            if (!options.headers) options.headers = {};
            options.headers['X-CSRF-TOKEN'] = token;
        }
    }

    // ==========================================
    // LOCAL STORAGE: COLA DE REQUESTS
    // ==========================================
    function addToQueue(item) {
        var queue = getQueue();
        queue.push(item);
        saveToLS('queue', queue);
    }

    function getQueue() {
        return loadFromLS('queue') || [];
    }

    function clearQueue() {
        removeFromLS('queue');
    }

    // ==========================================
    // LOCAL STORAGE: DATOS DE MODULO
    // ==========================================
    function saveModuleData(module, key, data) {
        var lsKey = module + '_' + key;
        saveToLS(lsKey, data);
    }

    function loadModuleData(module, key) {
        var lsKey = module + '_' + key;
        return loadFromLS(lsKey);
    }

    function clearModuleData(module, key) {
        var lsKey = module + '_' + key;
        removeFromLS(lsKey);
    }

    // ==========================================
    // LOCAL STORAGE: PRIMITIVAS
    // ==========================================
    function saveToLS(key, data) {
        try {
            localStorage.setItem(CONFIG.LS_PREFIX + key, JSON.stringify(data));
        } catch (e) {
            // localStorage lleno o no disponible
            console.warn('SindenConexion: No se pudo guardar en localStorage', e);
        }
    }

    function loadFromLS(key) {
        try {
            var raw = localStorage.getItem(CONFIG.LS_PREFIX + key);
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    }

    function removeFromLS(key) {
        try {
            localStorage.removeItem(CONFIG.LS_PREFIX + key);
        } catch (e) {}
    }

    function cleanExpiredData() {
        try {
            var now = Date.now();
            for (var i = localStorage.length - 1; i >= 0; i--) {
                var key = localStorage.key(i);
                if (key && key.indexOf(CONFIG.LS_PREFIX) === 0) {
                    var data = loadFromLS(key.replace(CONFIG.LS_PREFIX, ''));
                    if (data && data.timestamp && (now - data.timestamp) > CONFIG.MAX_QUEUE_AGE_MS) {
                        localStorage.removeItem(key);
                    }
                }
            }

            // Limpiar cola de items expirados
            var queue = getQueue();
            if (queue.length > 0) {
                var filtered = queue.filter(function(item) {
                    return (now - item.timestamp) < CONFIG.MAX_QUEUE_AGE_MS;
                });
                if (filtered.length !== queue.length) {
                    saveToLS('queue', filtered);
                }
            }
        } catch (e) {}
    }

    // ==========================================
    // SINCRONIZACION AL RECONECTAR
    // ==========================================
    function syncPendingData() {
        if (state.syncInProgress) return;

        var queue = getQueue();
        var hasOperarioData = hasModuleData('operario');
        var hasWizardData = hasModuleData('wizard');

        if (queue.length === 0 && !hasOperarioData && !hasWizardData) return;

        state.syncInProgress = true;

        var totalItems = queue.length + (hasOperarioData ? 1 : 0) + (hasWizardData ? 1 : 0);

        showToast('info', 'Sincronizando', 'Enviando ' + totalItems + ' operacion(es) pendiente(s)...');

        // Paso 1: Refrescar CSRF
        refreshCsrfToken()
            .then(function() {
                // Paso 2: Procesar cola FIFO
                return processQueue();
            })
            .then(function(results) {
                state.syncInProgress = false;

                var exitosos = results.filter(function(r) { return r.success; }).length;
                var fallidos = results.filter(function(r) { return !r.success; }).length;

                if (exitosos > 0 && fallidos === 0) {
                    showToast('success', 'Sincronizado', exitosos + ' operacion(es) enviada(s) correctamente.');
                } else if (exitosos > 0 && fallidos > 0) {
                    showToast('warning', 'Sincronizacion parcial',
                        exitosos + ' enviada(s), ' + fallidos + ' fallida(s). Se reintentaran.');
                } else if (fallidos > 0) {
                    showToast('error', 'Error de sincronizacion',
                        fallidos + ' operacion(es) no se pudieron enviar. Se reintentaran.');
                }
            })
            .catch(function() {
                state.syncInProgress = false;
                showToast('error', 'Error', 'No se pudo sincronizar. Se reintentara.');
            });
    }

    function processQueue() {
        var queue = getQueue();
        var results = [];

        if (queue.length === 0) {
            return $.Deferred().resolve(results).promise();
        }

        // Procesar secuencialmente (FIFO)
        var deferred = $.Deferred();
        var index = 0;

        function processNext() {
            if (index >= queue.length) {
                // Limpiar items exitosos de la cola
                var remaining = [];
                for (var i = 0; i < queue.length; i++) {
                    if (!results[i] || !results[i].success) {
                        remaining.push(queue[i]);
                    }
                }
                if (remaining.length > 0) {
                    saveToLS('queue', remaining);
                } else {
                    clearQueue();
                }
                deferred.resolve(results);
                return;
            }

            var item = queue[index];

            // Inyectar CSRF actualizado
            var data = item.data;
            if (typeof data === 'string') {
                try {
                    var parsed = JSON.parse(data);
                    parsed._token = $('meta[name="csrf-token"]').attr('content');
                    data = JSON.stringify(parsed);
                } catch (e) {
                    // Si no es JSON, agregar token como param
                    if (data.indexOf('_token=') !== -1) {
                        data = data.replace(/_token=[^&]*/, '_token=' + encodeURIComponent($('meta[name="csrf-token"]').attr('content')));
                    }
                }
            }

            $.ajax({
                url: item.url,
                method: item.method,
                data: data,
                contentType: item.contentType,
                global: false, // No disparar interceptores
                timeout: 10000,
                success: function() {
                    results.push({ success: true, index: index });
                    index++;
                    processNext();
                },
                error: function(xhr) {
                    results.push({ success: false, index: index, status: xhr.status });
                    index++;
                    processNext();
                }
            });
        }

        processNext();
        return deferred.promise();
    }

    // ==========================================
    // CSRF TOKEN REFRESH
    // ==========================================
    function refreshCsrfToken() {
        return $.ajax({
            url: CONFIG.CSRF_REFRESH_URL,
            method: 'GET',
            global: false,
            timeout: 5000
        }).then(function(response) {
            if (response && response.token) {
                var newToken = response.token;

                // Actualizar meta tag
                $('meta[name="csrf-token"]').attr('content', newToken);

                // Actualizar variables globales conocidas
                if (typeof window.CSRF_TOKEN !== 'undefined') {
                    window.CSRF_TOKEN = newToken;
                }
                if (window.WIZARD_CONFIG && window.WIZARD_CONFIG.csrfToken) {
                    window.WIZARD_CONFIG.csrfToken = newToken;
                }

                // Actualizar header global de jQuery
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': newToken }
                });
            }
        });
    }

    // ==========================================
    // VERIFICACION DE DATOS DE RECUPERACION
    // ==========================================
    function hasModuleData(modulePrefix) {
        try {
            for (var i = 0; i < localStorage.length; i++) {
                var key = localStorage.key(i);
                if (key && key.indexOf(CONFIG.LS_PREFIX + modulePrefix + '_') === 0) {
                    return true;
                }
            }
        } catch (e) {}
        return false;
    }

    function checkRecoveryData() {
        // La recuperacion especifica de operario y wizard se maneja en sus propios JS
        // Aqui solo verificamos la cola generica
        var queue = getQueue();
        if (queue.length === 0) return;

        var oldestTime = queue[0].timestamp;
        var fecha = new Date(oldestTime).toLocaleString('es-CO', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        Swal.fire({
            title: 'Operaciones pendientes',
            html: 'Se encontraron <b>' + queue.length + '</b> operacion(es) no enviada(s) desde el <b>' + fecha + '</b>.<br>¿Desea enviarlas ahora?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Enviar ahora',
            cancelButtonText: 'Descartar',
            confirmButtonColor: '#4A7C59'
        }).then(function(result) {
            if (result.isConfirmed) {
                syncPendingData();
            } else {
                clearQueue();
            }
        });
    }

    // ==========================================
    // UTILIDADES
    // ==========================================
    function showToast(icon, title, text) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            text: text,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    }

    // ==========================================
    // API PUBLICA
    // ==========================================
    return {
        init: init,
        isOnline: isOnline,
        saveModuleData: saveModuleData,
        loadModuleData: loadModuleData,
        clearModuleData: clearModuleData,
        syncNow: syncPendingData
    };

})(jQuery);

// Auto-inicializar al cargar DOM
$(function() {
    window.SindenConexion.init();
});
