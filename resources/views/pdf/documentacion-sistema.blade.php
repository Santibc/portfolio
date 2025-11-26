<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación Completa del Sistema</title>
    <style>
        /* Reset y fuentes */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
        }

        /* Página */
        @page {
            margin: 2cm 1.5cm;
            @top-center {
                content: element(header);
            }
            @bottom-center {
                content: element(footer);
            }
        }

        /* Encabezado */
        header {
            position: fixed;
            top: -2cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            text-align: center;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 5px;
        }

        header h1 {
            font-size: 14pt;
            color: #0066cc;
            margin: 0;
        }

        /* Pie de página */
        footer {
            position: fixed;
            bottom: -2cm;
            left: 0;
            right: 0;
            height: 1cm;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            font-size: 8pt;
            color: #666;
        }

        footer .page-number:after {
            content: counter(page);
        }

        /* Portada */
        .portada {
            text-align: center;
            margin-top: 8cm;
            page-break-after: always;
        }

        .portada h1 {
            font-size: 28pt;
            color: #0066cc;
            margin-bottom: 1cm;
            text-transform: uppercase;
        }

        .portada h2 {
            font-size: 18pt;
            color: #666;
            margin-bottom: 2cm;
        }

        .portada .info {
            font-size: 12pt;
            color: #333;
            margin: 0.5cm 0;
        }

        /* Tabla de contenidos */
        .toc {
            page-break-after: always;
        }

        .toc h2 {
            font-size: 18pt;
            color: #0066cc;
            margin-bottom: 1cm;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 0.3cm;
        }

        .toc ul {
            list-style: none;
            padding-left: 0;
        }

        .toc ul li {
            margin: 0.3cm 0;
            padding-left: 0.5cm;
        }

        .toc ul ul li {
            padding-left: 1.5cm;
            font-size: 9pt;
        }

        /* Títulos */
        h1 {
            font-size: 20pt;
            color: #0066cc;
            margin-top: 1cm;
            margin-bottom: 0.5cm;
            page-break-before: always;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 0.3cm;
        }

        h2 {
            font-size: 16pt;
            color: #0066cc;
            margin-top: 0.8cm;
            margin-bottom: 0.4cm;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 0.2cm;
        }

        h3 {
            font-size: 13pt;
            color: #004080;
            margin-top: 0.6cm;
            margin-bottom: 0.3cm;
        }

        h4 {
            font-size: 11pt;
            color: #333;
            font-weight: bold;
            margin-top: 0.4cm;
            margin-bottom: 0.2cm;
        }

        /* Párrafos */
        p {
            text-align: justify;
            margin-bottom: 0.3cm;
        }

        /* Listas */
        ul, ol {
            margin-left: 1cm;
            margin-bottom: 0.3cm;
        }

        ul li, ol li {
            margin-bottom: 0.2cm;
        }

        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.5cm 0;
            font-size: 9pt;
        }

        table th {
            background-color: #0066cc;
            color: white;
            font-weight: bold;
            padding: 0.3cm;
            text-align: left;
            border: 1px solid #0066cc;
        }

        table td {
            padding: 0.25cm;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Código */
        pre, code {
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            background-color: #f5f5f5;
            padding: 0.3cm;
            border-left: 3px solid #0066cc;
            overflow-x: auto;
            margin: 0.3cm 0;
        }

        code {
            padding: 0.1cm 0.2cm;
            display: inline;
        }

        /* Alertas */
        .alert {
            padding: 0.3cm;
            margin: 0.5cm 0;
            border-left: 4px solid;
            background-color: #f9f9f9;
        }

        .alert-warning {
            border-color: #ff9800;
            background-color: #fff3e0;
        }

        .alert-info {
            border-color: #2196f3;
            background-color: #e3f2fd;
        }

        .alert-success {
            border-color: #4caf50;
            background-color: #e8f5e9;
        }

        /* Cajas */
        .box {
            border: 1px solid #ddd;
            padding: 0.4cm;
            margin: 0.5cm 0;
            background-color: #fafafa;
        }

        .box-title {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 0.2cm;
        }

        /* Saltos de página */
        .page-break {
            page-break-after: always;
        }

        /* Evitar saltos de página */
        .no-break {
            page-break-inside: avoid;
        }

        /* Énfasis */
        strong {
            color: #000;
        }

        em {
            color: #666;
        }

        /* Enlaces (en PDF no son clickeables, pero se ven como tal) */
        a {
            color: #0066cc;
            text-decoration: none;
        }

        /* Sección de portada */
        .parte-titulo {
            text-align: center;
            margin: 3cm 0;
            page-break-before: always;
            page-break-after: always;
        }

        .parte-titulo h1 {
            font-size: 24pt;
            color: #0066cc;
            border: none;
            padding: 1cm;
            background-color: #f0f8ff;
        }

        /* Línea horizontal */
        hr {
            border: none;
            border-top: 2px solid #0066cc;
            margin: 1cm 0;
        }
    </style>
</head>
<body>

{{-- Encabezado --}}
<header>
    <h1>Sistema B2B + Servicio Técnico - Documentación Completa</h1>
</header>

{{-- Pie de página --}}
<footer>
    <span>Página <span class="page-number"></span></span>
    <span style="float: left;">© {{ date('Y') }} - Laravel 9.52</span>
    <span style="float: right;">Versión 1.0</span>
</footer>

{{-- PORTADA --}}
<div class="portada">
    <h1>Documentación Completa del Sistema</h1>
    <h2>Sistema de E-Commerce B2B<br>y Gestión de Servicio Técnico</h2>
    <div class="info"><strong>Framework:</strong> Laravel 9.52</div>
    <div class="info"><strong>Versión:</strong> 1.0</div>
    <div class="info"><strong>Fecha:</strong> {{ date('d/m/Y') }}</div>
    <div class="info"><strong>PHP:</strong> 8.0+ | <strong>MySQL:</strong> Via XAMPP</div>
</div>

{{-- TABLA DE CONTENIDOS --}}
<div class="toc">
    <h2>Tabla de Contenidos</h2>

    <h3 style="color: #0066cc; margin-top: 0.5cm;">PARTE 1: DOCUMENTACIÓN TÉCNICA</h3>
    <ul>
        <li><strong>1.</strong> Resumen Ejecutivo</li>
        <li><strong>2.</strong> Arquitectura del Sistema</li>
        <li><strong>3.</strong> Esquema de Base de Datos</li>
        <li><strong>4.</strong> Módulos del Sistema
            <ul>
                <li>4.1. Inicio (Dashboard)</li>
                <li>4.2. Servicio Técnico</li>
                <li>4.3. Métricas</li>
                <li>4.4. Usuarios</li>
                <li>4.5. Categorías</li>
                <li>4.6. Productos</li>
                <li>4.7. Cotizaciones</li>
                <li>4.8. Clientes</li>
                <li>4.9. Catálogo</li>
                <li>4.10. Links (Enlaces)</li>
                <li>4.11. Gestión de Stock</li>
            </ul>
        </li>
        <li><strong>5.</strong> API y Endpoints</li>
        <li><strong>6.</strong> Seguridad y Autenticación</li>
        <li><strong>7.</strong> Workflows Técnicos</li>
        <li><strong>8.</strong> Comandos de Desarrollo</li>
    </ul>

    <h3 style="color: #0066cc; margin-top: 0.8cm;">PARTE 2: MANUAL DE USUARIO</h3>
    <ul>
        <li><strong>9.</strong> Introducción para Usuarios</li>
        <li><strong>10.</strong> Guía de Configuración Inicial
            <ul>
                <li>10.1. Paso 1: Crear Usuarios</li>
                <li>10.2. Paso 2: Crear Categorías</li>
                <li>10.3. Paso 3: Crear Productos</li>
                <li>10.4. Paso 4: Crear Clientes B2B</li>
                <li>10.5. Paso 5: Generar Enlaces de Acceso</li>
            </ul>
        </li>
        <li><strong>11.</strong> Módulos de Servicio Técnico
            <ul>
                <li>11.1. Paso 6: Crear Clientes ST</li>
                <li>11.2. Paso 7: Registrar Técnicos</li>
                <li>11.3. Paso 8: Registrar Equipos</li>
                <li>11.4. Paso 9: Registrar Repuestos</li>
                <li>11.5. Paso 10: Crear Órdenes de Servicio</li>
            </ul>
        </li>
        <li><strong>12.</strong> Operaciones Diarias</li>
        <li><strong>13.</strong> Flujos de Trabajo Completos</li>
        <li><strong>14.</strong> Preguntas Frecuentes</li>
        <li><strong>15.</strong> Glosario</li>
    </ul>
</div>

{{-- PARTE 1: DOCUMENTACIÓN TÉCNICA --}}
<div class="parte-titulo">
    <h1>PARTE 1<br>DOCUMENTACIÓN TÉCNICA</h1>
</div>

{!! $contenidoHtml !!}

{{-- Última página --}}
<div class="page-break"></div>
<div style="text-align: center; margin-top: 8cm;">
    <h2 style="color: #0066cc;">FIN DE LA DOCUMENTACIÓN</h2>
    <p style="margin-top: 2cm;">
        <strong>Versión:</strong> 1.0<br>
        <strong>Fecha de actualización:</strong> {{ date('d/m/Y') }}<br>
        <strong>Sistema:</strong> Portfolio B2B + Servicio Técnico<br>
        <strong>Framework:</strong> Laravel 9.52
    </p>
    <p style="margin-top: 2cm; color: #666;">
        Para soporte técnico o preguntas adicionales,<br>
        contacte a su administrador del sistema.
    </p>
</div>

</body>
</html>
