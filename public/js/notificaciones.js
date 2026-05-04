(function() {
    'use strict';

    var POLL_INTERVAL = 10000; // 10 segundos
    var TOAST_DURATION = 5000; // 5 segundos
    var lastNotifIds = [];
    var panelOpen = false;

    // =====================
    // POLLING
    // =====================
    function fetchNotificaciones() {
        $.ajax({
            url: '/notificaciones',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                updateBadge(data.no_leidas);
                detectNewNotifications(data.notificaciones);
                if (panelOpen) {
                    renderPanel(data.notificaciones);
                }
            }
        });
    }

    function updateBadge(count) {
        var $badge = $('#notifBadge');
        if (count > 0) {
            $badge.text(count > 99 ? '99+' : count).show();
        } else {
            $badge.hide();
        }
    }

    function detectNewNotifications(notificaciones) {
        var currentIds = notificaciones.map(function(n) { return n.id; });

        if (lastNotifIds.length > 0) {
            var newOnes = notificaciones.filter(function(n) {
                return !n.leida && lastNotifIds.indexOf(n.id) === -1;
            });
            newOnes.forEach(function(n) {
                showToast(n);
            });
        }

        lastNotifIds = currentIds;
    }

    // =====================
    // TOAST
    // =====================
    function showToast(notif) {
        var $container = $('#notifToastContainer');
        var $toast = $('<div class="notif-toast">' +
            '<div class="notif-toast-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>' +
            '<div class="notif-toast-content">' +
                '<div class="notif-toast-title">' + escapeHtml(notif.titulo) + '</div>' +
                '<div class="notif-toast-text">' + escapeHtml(notif.contenido || '') + '</div>' +
            '</div>' +
            '<button class="notif-toast-close"><i class="bi bi-x"></i></button>' +
        '</div>');

        $toast.find('.notif-toast-close').on('click', function() {
            $toast.addClass('notif-toast-hiding');
            setTimeout(function() { $toast.remove(); }, 300);
        });

        $container.append($toast);

        // Trigger animation
        setTimeout(function() { $toast.addClass('notif-toast-visible'); }, 10);

        // Auto-close
        setTimeout(function() {
            if ($toast.parent().length) {
                $toast.addClass('notif-toast-hiding');
                setTimeout(function() { $toast.remove(); }, 300);
            }
        }, TOAST_DURATION);
    }

    // =====================
    // PANEL
    // =====================
    function renderPanel(notificaciones) {
        var $body = $('#notifPanelBody');

        if (!notificaciones || notificaciones.length === 0) {
            $body.html('<div class="notif-empty">Sin notificaciones</div>');
            return;
        }

        var html = '';
        notificaciones.forEach(function(n) {
            var leida = n.leida ? ' notif-item-leida' : '';
            var fecha = formatFecha(n.created_at);
            html += '<div class="notif-item' + leida + '" data-id="' + n.id + '">' +
                '<div class="notif-item-content">' +
                    (n.url ? '<a href="' + escapeHtml(n.url) + '" class="notif-item-link">' : '<div class="notif-item-link">') +
                        '<div class="notif-item-title">' + escapeHtml(n.titulo) + '</div>' +
                        '<div class="notif-item-text">' + escapeHtml(n.contenido || '') + '</div>' +
                        '<div class="notif-item-time">' + fecha + '</div>' +
                    (n.url ? '</a>' : '</div>') +
                '</div>' +
                '<button class="notif-item-delete" data-id="' + n.id + '" title="Eliminar"><i class="bi bi-x"></i></button>' +
            '</div>';
        });

        $body.html(html);
    }

    function togglePanel() {
        var $panel = $('#notifPanel');
        panelOpen = !panelOpen;

        if (panelOpen) {
            $panel.show();
            fetchNotificaciones();
        } else {
            $panel.hide();
        }
    }

    function deleteNotificacion(id) {
        $.ajax({
            url: '/notificaciones/' + id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                $('.notif-item[data-id="' + id + '"]').slideUp(200, function() {
                    $(this).remove();
                    if ($('#notifPanelBody .notif-item').length === 0) {
                        $('#notifPanelBody').html('<div class="notif-empty">Sin notificaciones</div>');
                    }
                });
                fetchNotificaciones();
            }
        });
    }

    function marcarTodasLeidas() {
        $.ajax({
            url: '/notificaciones/marcar-leidas',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                fetchNotificaciones();
            }
        });
    }

    function eliminarTodas() {
        $.ajax({
            url: '/notificaciones/eliminar-todas',
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                lastNotifIds = [];
                $('#notifPanelBody').html('<div class="notif-empty">Sin notificaciones</div>');
                updateBadge(0);
            }
        });
    }

    function confirmarEliminarTodas() {
        if ($('#notifPanelBody .notif-item').length === 0) {
            return;
        }

        if (window.Swal) {
            window.Swal.fire({
                title: 'Eliminar todas las notificaciones?',
                text: 'Esta accion no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    eliminarTodas();
                }
            });
        } else if (confirm('Eliminar todas las notificaciones?')) {
            eliminarTodas();
        }
    }

    // =====================
    // HELPERS
    // =====================
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function formatFecha(dateStr) {
        if (!dateStr) return '';
        var d = new Date(dateStr);
        var now = new Date();
        var diff = Math.floor((now - d) / 1000);

        if (diff < 60) return 'Hace un momento';
        if (diff < 3600) return 'Hace ' + Math.floor(diff / 60) + ' min';
        if (diff < 86400) return 'Hace ' + Math.floor(diff / 3600) + 'h';

        var day = ('0' + d.getDate()).slice(-2);
        var month = ('0' + (d.getMonth() + 1)).slice(-2);
        return day + '/' + month + '/' + d.getFullYear();
    }

    // =====================
    // EVENTS
    // =====================
    $(document).ready(function() {
        // Toggle panel
        $('#notifBellBtn').on('click', function(e) {
            e.stopPropagation();
            togglePanel();
        });

        // Close panel
        $('#notifPanelClose').on('click', function(e) {
            e.stopPropagation();
            panelOpen = false;
            $('#notifPanel').hide();
        });

        // Click outside to close
        $(document).on('click', function(e) {
            if (panelOpen && !$(e.target).closest('#notifBellWrapper').length) {
                panelOpen = false;
                $('#notifPanel').hide();
            }
        });

        // Delete individual
        $(document).on('click', '.notif-item-delete', function(e) {
            e.preventDefault();
            e.stopPropagation();
            deleteNotificacion($(this).data('id'));
        });

        // Mark all read
        $('#notifMarkAll').on('click', function() {
            marcarTodasLeidas();
        });

        // Delete all
        $('#notifDeleteAll').on('click', function() {
            confirmarEliminarTodas();
        });

        // Initial fetch
        fetchNotificaciones();

        // Start polling
        setInterval(fetchNotificaciones, POLL_INTERVAL);
    });
})();
