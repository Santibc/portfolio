/**
 * Manzer - Main JavaScript
 * Plataforma de Cursos Educativos
 */

document.addEventListener("DOMContentLoaded", function () {
    /* =====================================================
       SIDEBAR TOGGLE
    ====================================================== */
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");

    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener("click", function () {
            const isMobile = window.innerWidth <= 1024;

            if (isMobile) {
                // Mobile usa `.open`
                sidebar.classList.toggle("open");

                // mover contenido
                mainContent.classList.toggle("shifted");
            } else {
                // Desktop usa `.collapsed`
                sidebar.classList.toggle("collapsed");
                mainContent.classList.toggle("expanded");

                // Guardar estado solo en escritorio
                const isCollapsed = sidebar.classList.contains("collapsed");
                localStorage.setItem("sidebarCollapsed", isCollapsed);
            }
        });

        /* Restaurar estado en desktop */
        if (window.innerWidth > 1024) {
            const savedState = localStorage.getItem("sidebarCollapsed");
            if (savedState === "true") {
                sidebar.classList.add("collapsed");
                mainContent.classList.add("expanded");
            }
        }
    }

    /* =====================================================
       MOBILE - Sidebar cerrado por defecto
    ====================================================== */
    if (window.innerWidth <= 1024 && sidebar) {
        sidebar.classList.remove("collapsed");
        sidebar.classList.remove("open");
    }

    /* =====================================================
       AUTO-SCROLL SIDEBAR TO ACTIVE ITEM
    ====================================================== */
    const sidebarNav = document.querySelector(".sidebar-nav");
    const activeItem = sidebarNav?.querySelector(".nav-item.active");

    if (sidebarNav && activeItem) {
        // Use setTimeout to ensure layout is complete
        setTimeout(() => {
            activeItem.scrollIntoView({
                block: "center",
                behavior: "instant"
            });
        }, 0);
    }

    /* =====================================================
       Cerrar sidebar móvil al hacer click fuera
    ====================================================== */
    document.addEventListener("click", function (e) {
        if (window.innerWidth <= 1024) {
            // No cerrar si el click fue en un dropdown o sus hijos
            if (e.target.closest('.dropdown') || e.target.closest('.dropdown-menu')) {
                return;
            }
            if (
                sidebar &&
                !sidebar.contains(e.target) &&
                !sidebarToggle.contains(e.target)
            ) {
                sidebar.classList.remove("open");
                mainContent.classList.remove("shifted");
            }
        }
    });

    /* =====================================================
       AUTO-HIDE ALERTS
    ====================================================== */
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach(function (alert) {
        setTimeout(() => {
            alert.style.opacity = "0";
            alert.style.transform = "translateY(-10px)";
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    /* =====================================================
       DATATABLES (ESPAÑOL)
    ====================================================== */
    if (typeof $.fn.DataTable !== "undefined") {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron registros",
                emptyTable: "No hay datos disponibles",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último",
                },
            },
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Todos"],
            ],
        });
    }

    /* =====================================================
       SWEET ALERT DELETE CONFIRM
    ====================================================== */
    window.confirmDelete = function (
        formId,
        message = "¿Estás seguro de eliminar este registro?"
    ) {
        Swal.fire({
            title: "¿Estás seguro?",
            text: message,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#EF4444",
            cancelButtonColor: "#6B7280",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    };

    /* =====================================================
       TOAST
    ====================================================== */
    window.showToast = function (type, title, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        Toast.fire({
            icon: type,
            title: title,
            text: message,
        });
    };
});
/* ============================================================
   UTILITIES
============================================================ */
window.formatDuration = function (seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    if (hours > 0) {
        return `${hours}:${String(minutes).padStart(2, "0")}:${String(
            secs
        ).padStart(2, "0")}`;
    }
    return `${minutes}:${String(secs).padStart(2, "0")}`;
};

window.formatFileSize = function (bytes) {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

window.debounce = function (func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
};
