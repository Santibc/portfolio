<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

echo "\n===================================\n";
echo "  GENERANDO MANUAL DE USUARIO  \n";
echo "===================================\n\n";

// HTML del manual de usuario construido directamente
$html = <<<'HTML'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual de Usuario</title>
    <style>
        @page {
            margin: 30mm 30mm 30mm 30mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }

        h1 {
            color: #27ae60;
            font-size: 22pt;
            margin-top: 30px;
            margin-bottom: 20px;
            border-bottom: 3px solid #27ae60;
            padding-bottom: 10px;
            page-break-after: avoid;
        }

        h2 {
            color: #16a085;
            font-size: 18pt;
            margin-top: 25px;
            margin-bottom: 15px;
            border-bottom: 2px solid #bdc3c7;
            padding-bottom: 8px;
            page-break-after: avoid;
        }

        h3 {
            color: #16a085;
            font-size: 14pt;
            margin-top: 20px;
            margin-bottom: 12px;
            page-break-after: avoid;
        }

        h4 {
            color: #7f8c8d;
            font-size: 12pt;
            margin-top: 15px;
            margin-bottom: 10px;
            page-break-after: avoid;
        }

        p {
            margin: 0 0 12px 0;
            text-align: justify;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
            page-break-inside: avoid;
        }

        thead {
            background-color: #27ae60;
            color: white;
        }

        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #229954;
        }

        td {
            padding: 8px;
            border: 1px solid #bdc3c7;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .portada {
            text-align: center;
            margin-top: 200px;
            page-break-after: always;
        }

        .portada h1 {
            font-size: 36pt;
            color: #27ae60;
            border: none;
            margin-bottom: 40px;
        }

        .portada h2 {
            font-size: 20pt;
            color: #7f8c8d;
            border: none;
            margin-bottom: 80px;
        }

        .portada p {
            font-size: 12pt;
            margin: 15px 0;
            color: #34495e;
        }

        .alerta {
            background-color: #fff3cd;
            border-left: 4px solid #f39c12;
            padding: 12px 15px;
            margin: 15px 0;
            page-break-inside: avoid;
        }

        .alerta strong {
            color: #856404;
        }

        strong {
            font-weight: bold;
            color: #2c3e50;
        }

        ul, ol {
            margin: 12px 0 12px 30px;
        }

        li {
            margin: 8px 0;
        }
    </style>
</head>
<body>

<!-- PORTADA -->
<div class="portada">
    <h1>MANUAL DE USUARIO</h1>
    <h2>Guia Practica para Usuarios del Sistema</h2>
    <p><strong>Sistema:</strong> E-Commerce B2B + Servicio Tecnico</p>
    <p><strong>Version:</strong> 1.0</p>
    <p><strong>Fecha:</strong> 25/11/2024</p>
    <p><strong>Framework:</strong> Laravel 9.52</p>
</div>

<!-- CONTENIDO -->
<h1>INTRODUCCION PARA USUARIOS</h1>

<h2>Roles y Permisos</h2>

<p>El sistema maneja tres tipos de usuarios:</p>

<table>
    <thead>
        <tr>
            <th>Rol</th>
            <th>Que puede hacer</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Administrador</strong></td>
            <td>Acceso completo a todos los modulos. Puede crear usuarios, productos, categorias, clientes, ver metricas y gestionar todo el sistema.</td>
        </tr>
        <tr>
            <td><strong>Vendedor</strong></td>
            <td>Puede gestionar sus clientes asignados, crear cotizaciones, generar enlaces de catalogo y ver productos. NO puede crear productos ni usuarios.</td>
        </tr>
        <tr>
            <td><strong>Tecnico</strong></td>
            <td>Acceso al modulo de Servicio Tecnico. Puede gestionar clientes ST, equipos, repuestos y ordenes de servicio.</td>
        </tr>
    </tbody>
</table>

<h2>Navegacion del Sistema</h2>

<p>El menu lateral esta organizado por modulos:</p>

<ol>
    <li><strong>Inicio:</strong> Dashboard con resumen general</li>
    <li><strong>Servicio:</strong> Modulo de ordenes de servicio tecnico</li>
    <li><strong>Metricas:</strong> Reportes y estadisticas</li>
    <li><strong>Usuarios:</strong> Administracion de usuarios y roles</li>
    <li><strong>Categorias:</strong> Categorias de productos</li>
    <li><strong>Productos:</strong> Gestion completa de productos</li>
    <li><strong>Cotizaciones:</strong> Solicitudes de cotizacion de clientes</li>
    <li><strong>Clientes:</strong> Clientes B2B</li>
    <li><strong>Catalogo:</strong> Ver catalogo como cliente</li>
    <li><strong>Links:</strong> Enlaces de acceso temporal al catalogo</li>
    <li><strong>Stock:</strong> Control de inventario</li>
</ol>

<h1>GUIA DE CONFIGURACION INICIAL</h1>

<p class="alerta"><strong>IMPORTANTE:</strong> Siga estos pasos en orden. Cada paso depende del anterior.</p>

<h2>PASO 1: Crear Usuarios</h2>

<p><strong>Quien lo hace?</strong> Solo Administradores</p>
<p><strong>Por que es importante?</strong> Necesita crear usuarios con roles especificos (vendedores, tecnicos) antes de poder asignarlos a clientes u ordenes.</p>

<h3>Como crear un usuario:</h3>

<ol>
    <li>En el menu lateral, haga clic en <strong>"Usuarios"</strong></li>
    <li>Haga clic en el boton <strong>"+ Nuevo Usuario"</strong></li>
    <li>Complete el formulario:</li>
</ol>

<table>
    <thead>
        <tr>
            <th>Campo</th>
            <th>Es obligatorio?</th>
            <th>Que significa?</th>
            <th>Ejemplo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Nombre*</strong></td>
            <td>SI</td>
            <td>Nombre completo del usuario</td>
            <td>Juan Perez</td>
        </tr>
        <tr>
            <td><strong>Email*</strong></td>
            <td>SI</td>
            <td>Correo electronico (debe ser unico)</td>
            <td>juan@empresa.com</td>
        </tr>
        <tr>
            <td><strong>Password*</strong></td>
            <td>SI</td>
            <td>Contraseña (minimo 8 caracteres)</td>
            <td>********</td>
        </tr>
        <tr>
            <td><strong>Rol*</strong></td>
            <td>SI</td>
            <td>Nivel de acceso</td>
            <td>admin / vendedor / tecnico</td>
        </tr>
    </tbody>
</table>

<p>*Campo obligatorio</p>

<ol start="4">
    <li>Haga clic en <strong>"Guardar"</strong></li>
</ol>

<p><strong>Nota:</strong> El usuario recibira un correo electronico con sus credenciales de acceso.</p>

<h2>PASO 2: Crear Categorias</h2>

<p><strong>Quien lo hace?</strong> Solo Administradores</p>
<p><strong>Por que es importante?</strong> Los productos deben pertenecer a una categoria. Sin categorias, no puede crear productos.</p>

<h3>Como crear una categoria:</h3>

<ol>
    <li>En el menu lateral, haga clic en <strong>"Categorias"</strong></li>
    <li>Haga clic en el boton <strong>"+ Nueva Categoria"</strong></li>
    <li>Complete el formulario:</li>
</ol>

<table>
    <thead>
        <tr>
            <th>Campo</th>
            <th>Es obligatorio?</th>
            <th>Que significa?</th>
            <th>Ejemplo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Nombre*</strong></td>
            <td>SI</td>
            <td>Nombre de la categoria</td>
            <td>Camaras IP</td>
        </tr>
        <tr>
            <td><strong>Slug</strong></td>
            <td>NO</td>
            <td>Identificador en URL (se genera automaticamente)</td>
            <td>camaras-ip</td>
        </tr>
        <tr>
            <td><strong>Descripcion</strong></td>
            <td>NO</td>
            <td>Descripcion de la categoria</td>
            <td>Camaras de vigilancia con conexion IP</td>
        </tr>
        <tr>
            <td><strong>Orden*</strong></td>
            <td>SI</td>
            <td>Orden de aparicion en el catalogo (numero)</td>
            <td>1</td>
        </tr>
    </tbody>
</table>

<p>*Campo obligatorio</p>

<ol start="4">
    <li>Haga clic en <strong>"Guardar"</strong></li>
</ol>

<p><strong>Consejos:</strong></p>
<ul>
    <li>Use numeros consecutivos para el orden (1, 2, 3...)</li>
    <li>Las categorias con menor numero aparecen primero</li>
    <li>Puede cambiar el orden despues editando la categoria</li>
</ul>

<h2>PASO 3: Crear Productos</h2>

<p><strong>Quien lo hace?</strong> Solo Administradores</p>
<p><strong>Por que es importante?</strong> Los productos son el corazon del catalogo. Sin productos, los clientes no pueden solicitar cotizaciones.</p>

<p class="alerta"><strong>REQUISITO:</strong> Debe tener al menos una categoria creada.</p>

<h3>Como crear un producto:</h3>

<ol>
    <li>En el menu lateral, haga clic en <strong>"Productos"</strong></li>
    <li>Haga clic en el boton <strong>"+ Nuevo Producto"</strong></li>
    <li>Complete el formulario (tiene varias secciones):</li>
</ol>

<h3>SECCION A: Informacion Basica</h3>

<table>
    <thead>
        <tr>
            <th>Campo</th>
            <th>Es obligatorio?</th>
            <th>Que significa?</th>
            <th>Ejemplo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Referencia*</strong></td>
            <td>SI</td>
            <td>Codigo unico del producto (SKU)</td>
            <td>CAM-IP-001</td>
        </tr>
        <tr>
            <td><strong>Nombre del Producto*</strong></td>
            <td>SI</td>
            <td>Nombre descriptivo</td>
            <td>Camara IP 2MP Domo</td>
        </tr>
        <tr>
            <td><strong>Descripcion</strong></td>
            <td>NO</td>
            <td>Descripcion detallada del producto</td>
            <td>Camara domo con resolucion 1080p, vision nocturna 30m</td>
        </tr>
        <tr>
            <td><strong>Marca</strong></td>
            <td>NO</td>
            <td>Marca del producto</td>
            <td>Hikvision</td>
        </tr>
        <tr>
            <td><strong>Unidad de Venta*</strong></td>
            <td>SI</td>
            <td>Como se vende el producto</td>
            <td>Unidad</td>
        </tr>
        <tr>
            <td><strong>Unidad de Empaque*</strong></td>
            <td>SI</td>
            <td>Como se empaca</td>
            <td>Caja</td>
        </tr>
        <tr>
            <td><strong>Extension (Color/Motivo)</strong></td>
            <td>NO</td>
            <td>Color, diseño o variante</td>
            <td>Blanco</td>
        </tr>
        <tr>
            <td><strong>Categoria*</strong></td>
            <td>SI</td>
            <td>Seleccione una categoria</td>
            <td>Camaras IP</td>
        </tr>
        <tr>
            <td><strong>Tiene Variantes</strong></td>
            <td>NO</td>
            <td>El producto viene en diferentes tallas/colores?</td>
            <td>No (desmarcado)</td>
        </tr>
    </tbody>
</table>

<p>*Campo obligatorio</p>

<h3>SECCION B: Control de Stock</h3>

<table>
    <thead>
        <tr>
            <th>Campo</th>
            <th>Es obligatorio?</th>
            <th>Que significa?</th>
            <th>Ejemplo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Controlar Stock</strong></td>
            <td>NO (marcado por defecto)</td>
            <td>Quiere llevar control de inventario?</td>
            <td>Si (marcado)</td>
        </tr>
        <tr>
            <td><strong>Permitir Venta Sin Stock</strong></td>
            <td>NO</td>
            <td>Permitir vender aunque no haya inventario?</td>
            <td>No (desmarcado)</td>
        </tr>
    </tbody>
</table>

<p><strong>Si marco "Controlar Stock" y NO marco "Tiene Variantes":</strong></p>

<table>
    <thead>
        <tr>
            <th>Campo</th>
            <th>Es obligatorio?</th>
            <th>Que significa?</th>
            <th>Ejemplo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Stock Inicial*</strong></td>
            <td>SI</td>
            <td>Cantidad inicial en inventario</td>
            <td>50</td>
        </tr>
        <tr>
            <td><strong>Stock Minimo</strong></td>
            <td>NO</td>
            <td>Cantidad minima (alerta de stock bajo)</td>
            <td>10</td>
        </tr>
        <tr>
            <td><strong>Stock Maximo</strong></td>
            <td>NO</td>
            <td>Cantidad maxima (opcional)</td>
            <td>200</td>
        </tr>
        <tr>
            <td><strong>Ubicacion en Bodega</strong></td>
            <td>NO</td>
            <td>Donde esta almacenado</td>
            <td>Bodega A, Estante 3</td>
        </tr>
    </tbody>
</table>

<p>*Campo obligatorio solo al crear producto nuevo</p>

<p><strong>Nota:</strong> Si marca "Tiene Variantes", el stock se gestiona por cada variante, no aqui.</p>

<h3>SECCION C: Imagenes del Producto</h3>

<table>
    <thead>
        <tr>
            <th>Campo</th>
            <th>Es obligatorio?</th>
            <th>Que significa?</th>
            <th>Restricciones</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Agregar Imagenes</strong></td>
            <td>NO</td>
            <td>Fotos del producto</td>
            <td>JPG, PNG, WebP - Max 2MB cada una</td>
        </tr>
        <tr>
            <td><strong>Imagen Principal</strong></td>
            <td>NO (pero recomendado)</td>
            <td>La imagen destacada</td>
            <td>Marque una con el radio button</td>
        </tr>
    </tbody>
</table>

<p><strong>Pasos:</strong></p>
<ol>
    <li>Haga clic en "Seleccionar archivos"</li>
    <li>Elija una o varias imagenes</li>
    <li>Marque una como "Imagen Principal"</li>
    <li>Puede agregar mas imagenes despues editando el producto</li>
</ol>

<h3>SECCION D: Precios por Lista</h3>

<p>Vera una tabla con las listas de precios del sistema:</p>

<table>
    <thead>
        <tr>
            <th>Lista de Precio</th>
            <th>Es obligatorio?</th>
            <th>Que significa?</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Export 1</td>
            <td>NO</td>
            <td>Precio para exportacion tier 1</td>
        </tr>
        <tr>
            <td>Export 2</td>
            <td>NO</td>
            <td>Precio para exportacion tier 2</td>
        </tr>
        <tr>
            <td>Local 1</td>
            <td>NO</td>
            <td>Precio local tier 1</td>
        </tr>
        <tr>
            <td>Local 2</td>
            <td>NO</td>
            <td>Precio local tier 2</td>
        </tr>
        <tr>
            <td>Local 3</td>
            <td>NO</td>
            <td>Precio local tier 3</td>
        </tr>
        <tr>
            <td>Local 4</td>
            <td>NO</td>
            <td>Precio local tier 4</td>
        </tr>
    </tbody>
</table>

<p><strong>Ingrese el precio para cada lista que use.</strong> Puede dejarlo vacio si no aplica.</p>

<ol start="4">
    <li>Revise toda la informacion</li>
    <li>Haga clic en <strong>"Guardar Producto"</strong></li>
</ol>

<p><strong>Consejos:</strong></p>
<ul>
    <li>La referencia debe ser unica (no puede haber dos productos con la misma referencia)</li>
    <li>Si no sube imagen, el producto aparecera con imagen por defecto</li>
    <li>Puede editar el producto despues para agregar mas imagenes o cambiar precios</li>
    <li>Si tiene muchos productos, puede importarlos desde Excel (opcion avanzada)</li>
</ul>

<h2>PASO 4: Crear Clientes B2B</h2>

<p><strong>Quien lo hace?</strong> Administradores y Vendedores</p>
<p><strong>Por que es importante?</strong> Necesita clientes registrados para generarles enlaces de catalogo y recibir cotizaciones.</p>

<p class="alerta"><strong>REQUISITO:</strong> Debe tener al menos un usuario con rol "vendedor" creado.</p>

<h3>Como crear un cliente:</h3>

<ol>
    <li>En el menu lateral, haga clic en <strong>"Clientes"</strong></li>
    <li>Haga clic en el boton <strong>"+ Nuevo Cliente"</strong></li>
    <li>Complete el formulario:</li>
</ol>

<table>
    <thead>
        <tr>
            <th>Campo</th>
            <th>Es obligatorio?</th>
            <th>Que significa?</th>
            <th>Ejemplo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Identificacion*</strong></td>
            <td>SI</td>
            <td>NIT o cedula del cliente</td>
            <td>900123456-7</td>
        </tr>
        <tr>
            <td><strong>Contacto*</strong></td>
            <td>SI</td>
            <td>Nombre de la persona de contacto</td>
            <td>Maria Garcia</td>
        </tr>
        <tr>
            <td><strong>Email*</strong></td>
            <td>SI</td>
            <td>Correo electronico (unico)</td>
            <td>maria@clienteempresa.com</td>
        </tr>
        <tr>
            <td><strong>Telefono</strong></td>
            <td>NO</td>
            <td>Telefono de contacto</td>
            <td>601 234 5678</td>
        </tr>
        <tr>
            <td><strong>Departamento*</strong></td>
            <td>SI</td>
            <td>Departamento (carga ciudades)</td>
            <td>Cundinamarca</td>
        </tr>
        <tr>
            <td><strong>Ciudad*</strong></td>
            <td>SI</td>
            <td>Ciudad del cliente</td>
            <td>Bogota</td>
        </tr>
        <tr>
            <td><strong>Vendedor*</strong></td>
            <td>SI</td>
            <td>Vendedor asignado</td>
            <td>Juan Perez (vendedor)</td>
        </tr>
        <tr>
            <td><strong>Lista de Precio*</strong></td>
            <td>SI</td>
            <td>Lista de precios que vera el cliente</td>
            <td>Local 1</td>
        </tr>
    </tbody>
</table>

<p>*Campo obligatorio</p>

<ol start="4">
    <li>Haga clic en <strong>"Guardar"</strong></li>
</ol>

<p><strong>Notas importantes:</strong></p>
<ul>
    <li>Si es <strong>Vendedor</strong>, automaticamente usted sera el vendedor asignado (no puede cambiarlo)</li>
    <li>Si es <strong>Administrador</strong>, puede asignar cualquier vendedor</li>
    <li>Al seleccionar Departamento, las ciudades se cargan automaticamente</li>
    <li>La lista de precio determina que precios vera este cliente en el catalogo</li>
</ul>

<h1>FIN DEL MANUAL DE USUARIO</h1>

<p style="text-align: center; margin-top: 100px; color: #7f8c8d;">
    <strong>Sistema:</strong> Portfolio B2B + Servicio Tecnico<br>
    <strong>Version:</strong> 1.0<br>
    <strong>Soporte:</strong> Contacte a su administrador del sistema
</p>

</body>
</html>
HTML;

try {
    echo "✓ Generando PDF del Manual de Usuario...\n";

    $pdf = Pdf::loadHTML($html);
    $pdf->setPaper('a4', 'portrait');

    $outputPath = public_path('documentacion/MANUAL_USUARIO_NUEVO.pdf');
    $pdf->save($outputPath);

    $fileSize = round(filesize($outputPath) / 1024, 2);

    echo "\n✓ PDF GENERADO EXITOSAMENTE!\n";
    echo "Archivo: $outputPath\n";
    echo "Tamaño: {$fileSize} KB\n";
    echo "URL: " . url('documentacion/MANUAL_USUARIO_NUEVO.pdf') . "\n\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
