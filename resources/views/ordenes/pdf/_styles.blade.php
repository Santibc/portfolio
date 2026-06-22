/* Reset y base */
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: sans-serif; font-size: 10px; color: #1f2937; line-height: 1.4; margin: 0; padding: 0; }

/* Paginas con margen vs sin margen */
.page-con-margen { padding: 25px 30px; }
.page-sin-margen { padding: 5px; }

/* Utilidades */
.text-center { text-align: center; }
.text-end { text-align: right; }
.text-muted { color: #6b7280; }
.text-success { color: #16a34a; }
.text-danger { color: #dc2626; }
.text-warning { color: #d97706; }
.fw-bold { font-weight: bold; }
.fw-semibold { font-weight: 600; }
.small { font-size: 8px; }
.mt-10 { margin-top: 10px; }
.mt-15 { margin-top: 15px; }
.mb-5 { margin-bottom: 5px; }

/* Header */
.pdf-header-table { width: 100%; margin-bottom: 15px; }
.pdf-header-table td { vertical-align: top; }
.company-logo { max-height: 60px; max-width: 180px; }
.orden-title { font-size: 18px; font-weight: bold; color: #4A7C59; margin-bottom: 2px; }
.orden-numero { font-size: 14px; color: #1f2937; font-weight: bold; }

/* Secciones */
.section { margin-bottom: 12px; }
.section-title {
    font-size: 11px;
    font-weight: bold;
    color: #4A7C59;
    border-bottom: 2px solid #4A7C59;
    padding-bottom: 3px;
    margin-bottom: 8px;
}

/* Tabla info (clave-valor) */
.info-table { width: 100%; font-size: 9px; }
.info-table td { padding: 2px 5px; vertical-align: top; }
.info-label { font-weight: bold; color: #374151; width: 100px; }

/* Tabla de datos */
.data-table { width: 100%; border-collapse: collapse; font-size: 9px; }
.data-table th {
    background: #4A7C59;
    color: white;
    padding: 5px 4px;
    text-align: left;
    font-size: 8px;
    font-weight: 600;
}
.data-table td {
    padding: 4px;
    border-bottom: 1px solid #e5e7eb;
}
.data-table tr:nth-child(even) td { background: #f9fafb; }
.data-table .total-row td {
    border-top: 2px solid #4A7C59;
    font-weight: bold;
    background: white;
}

/* Estados */
.estado-badge {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 3px;
    font-size: 7px;
    font-weight: bold;
    text-transform: uppercase;
}
.estado-generada { background: #dbeafe; color: #1d4ed8; }
.estado-en_ejecucion { background: #fef3c7; color: #92400e; }
.estado-ejecutada_parcialmente { background: #fef3c7; color: #92400e; }
.estado-ejecutada { background: #dcfce7; color: #166534; }
.estado-anulada { background: #fee2e2; color: #991b1b; }
.estado-pendiente { background: #f3f4f6; color: #374151; }
.estado-completada { background: #dcfce7; color: #166534; }
.estado-entregada { background: #dcfce7; color: #166534; }

/* Financiero */
.financial-box {
    display: inline-block;
    border: 1px solid #e5e7eb;
    padding: 4px 10px;
    text-align: center;
    border-radius: 3px;
    margin-right: 5px;
}
.financial-label { font-size: 7px; color: #6b7280; text-transform: uppercase; }
.financial-value { font-size: 12px; font-weight: bold; }

/* Firma */
.firma-img { max-height: 80px; max-width: 250px; border: 1px solid #e5e7eb; }

/* Pie de pagina */
.pdf-footer {
    margin-top: 15px;
    padding-top: 8px;
    border-top: 1px solid #d1d5db;
    font-size: 8px;
    color: #9ca3af;
}

/* Bosquejos grid */
.bosquejos-table { width: 100%; border-collapse: collapse; }
.bosquejos-table tr { page-break-inside: avoid; }
.bosquejos-table td {
    text-align: center;
    padding: 8px;
    vertical-align: top;
}
.bosquejo-img { width: 100%; height: auto; border: 1px solid #e5e7eb; }
.bosquejo-nombre { font-size: 8px; color: #374151; margin-top: 3px; }

/* Page break */
.page-break { page-break-before: always; }

/* Saltos controlados: flujo continuo evitando cortar bloques al medio */
.avoid-break-section { page-break-inside: avoid; }
.data-table tr { page-break-inside: avoid; }

/* Fila de observaciones debajo de cada pieza (patron del wizard de creacion) */
.pieza-notas-row td {
    background: #fafafa !important;
    border-top: 0;
    border-bottom: 1px solid #e5e7eb;
    padding: 3px 6px 5px 8px;
    font-size: 8px;
    color: #374151;
    border-left: 2px solid #4A7C59;
}
.pieza-notas-label {
    font-weight: 600;
    color: #4A7C59;
    margin-right: 4px;
}
.pieza-notas-texto { color: #374151; }
