@extends('layouts.app')
@section('title', 'Componentes')

@section('content')
<x-page-header
    title="Componentes UI"
    subtitle="Galeria de todos los componentes disponibles. Verifica visualmente en modo claro y oscuro."
    icon="component"
    :breadcrumb="[
        ['label' => 'Inicio', 'href' => route('dashboard')],
        ['label' => 'Componentes'],
    ]"
>
    <x-slot:actions>
        <x-button variant="secondary" icon="external-link" href="https://preline.co/docs/index.html">
            Docs Preline
        </x-button>
    </x-slot:actions>
</x-page-header>

{{-- Indice rapido --}}
<x-card padding="p-4" class="mb-8">
    <div class="flex flex-wrap gap-2 text-xs">
        @foreach ([
            ['typography', 'Tipografia', 'type'],
            ['buttons', 'Botones', 'mouse-pointer-click'],
            ['inputs', 'Inputs', 'text-cursor-input'],
            ['selects', 'Selects', 'list'],
            ['cards', 'Cards', 'square'],
            ['stats', 'Stat cards', 'gauge'],
            ['badges', 'Badges', 'tag'],
            ['alerts', 'Alerts', 'bell'],
            ['modals', 'Modales', 'square-stack'],
            ['dropdowns', 'Dropdowns', 'chevron-down'],
            ['tabs', 'Tabs / Acordeon', 'layers'],
            ['tooltips', 'Tooltips / Avatars', 'message-circle'],
            ['tables', 'Tablas', 'table'],
            ['charts', 'Graficas', 'bar-chart-3'],
            ['empty', 'Estados vacios', 'inbox'],
            ['progress', 'Progress / Spinners', 'loader-circle'],
            ['icons', 'Iconos', 'sparkles'],
        ] as [$id, $label, $icon])
            <a href="#{{ $id }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-cream-100 hover:bg-primary-100 hover:text-primary-800 text-cream-700 dark:bg-cream-800 dark:hover:bg-primary-900/40 dark:text-cream-300 dark:hover:text-primary-200 transition-colors">
                <x-icon :name="$icon" class="w-3.5 h-3.5" />
                {{ $label }}
            </a>
        @endforeach
    </div>
</x-card>

{{-- ===== Tipografia ===== --}}
<x-section title="Tipografia" description="Plus Jakarta Sans + Caveat (acentos)" id="typography">
    <x-card>
        <div class="space-y-3">
            <h1 class="text-4xl font-extrabold">Heading H1 — comida con amor</h1>
            <h2 class="text-3xl font-bold">Heading H2</h2>
            <h3 class="text-2xl font-semibold">Heading H3</h3>
            <h4 class="text-xl font-semibold">Heading H4</h4>
            <p class="brand-script text-3xl text-primary-600 dark:text-primary-300">Sopas y Sopitas (Caveat)</p>
            <p class="text-base text-cream-700 dark:text-cream-300">
                Lorem ipsum dolor sit amet consectetur. <strong>Bold inline</strong>, <em>italica</em>, <a href="#" class="text-primary-700 dark:text-primary-300 underline">enlace</a>.
            </p>
            <p class="text-sm text-cream-600 dark:text-cream-400">Texto secundario, pequeno, util para descripciones.</p>
        </div>
    </x-card>
</x-section>

{{-- ===== Botones ===== --}}
<x-section title="Botones" description="6 variantes x 4 tamanos, con icono opcional" id="buttons">
    <x-card>
        <div class="space-y-4">
            <div class="flex flex-wrap gap-2">
                <x-button variant="primary">Primary</x-button>
                <x-button variant="secondary">Secondary</x-button>
                <x-button variant="ghost">Ghost</x-button>
                <x-button variant="success">Success</x-button>
                <x-button variant="danger">Danger</x-button>
                <x-button variant="link">Link</x-button>
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                <x-button size="xs">XS</x-button>
                <x-button size="sm">SM</x-button>
                <x-button size="md">MD (default)</x-button>
                <x-button size="lg">LG</x-button>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-button variant="primary" icon="plus">Crear</x-button>
                <x-button variant="secondary" icon="download">Descargar</x-button>
                <x-button variant="primary" iconRight="arrow-right">Continuar</x-button>
                <x-button variant="ghost" icon="settings"></x-button>
            </div>
            <div class="flex flex-wrap gap-2">
                <x-button variant="primary" disabled>Disabled</x-button>
                <x-button variant="primary" href="#">Link button</x-button>
            </div>
        </div>
    </x-card>
</x-section>

{{-- ===== Inputs ===== --}}
<x-section title="Inputs" description="Labels, hints, errores, iconos" id="inputs">
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <x-input label="Nombre" placeholder="Tu nombre completo" icon="user" />
            <x-input label="Correo" type="email" placeholder="tu@correo.com" icon="mail" hint="No compartiremos tu correo" />
            <x-input label="Contrasena" type="password" placeholder="********" icon="lock" />
            <x-input label="Con error" :error="'Este campo es obligatorio'" placeholder="Ingresa algo" icon="alert-circle" />
            <x-textarea label="Mensaje" placeholder="Escribe aqui..." rows="4" />
            <div class="space-y-3">
                <x-checkbox label="Acepto los terminos" description="Lee la letra pequena" />
                <x-checkbox label="Suscribirme al newsletter" checked />
                <x-radio name="opt" value="a" label="Opcion A" checked />
                <x-radio name="opt" value="b" label="Opcion B" />
                <x-toggle label="Notificaciones" description="Recibir alertas por correo" checked />
            </div>
        </div>
    </x-card>
</x-section>

{{-- ===== Selects ===== --}}
<x-section title="Selects" description="Select nativo + TomSelect (auto-mejorado)" id="selects">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <x-card>
            <x-select label="Select nativo" :options="['italiana' => 'Italiana', 'mexicana' => 'Mexicana', 'colombiana' => 'Colombiana']" placeholder="Elige una opcion" />
        </x-card>
        <x-card>
            <x-select label="TomSelect (busqueda + tagging)" tomselect placeholder="Selecciona platos">
                <option value="ajiaco">Ajiaco</option>
                <option value="sancocho">Sancocho</option>
                <option value="sopa-pollo">Sopa de pollo</option>
                <option value="crema-tomate">Crema de tomate</option>
                <option value="caldo">Caldo de costilla</option>
            </x-select>
        </x-card>
    </div>
</x-section>

{{-- ===== Cards ===== --}}
<x-section title="Cards" description="Wrapper basico, con header/footer, hover" id="cards">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <x-card>
            <h4 class="font-semibold mb-2">Card simple</h4>
            <p class="text-sm text-cream-600 dark:text-cream-400">Solo contenido, padding p-6, rounded-2xl.</p>
        </x-card>
        <x-card>
            <x-slot:header>
                <h4 class="font-semibold">Card con header</h4>
            </x-slot:header>
            <p class="text-sm text-cream-600 dark:text-cream-400">El header se separa con un border-bottom.</p>
            <x-slot:footer>
                <div class="flex justify-end">
                    <x-button size="sm" variant="primary">Confirmar</x-button>
                </div>
            </x-slot:footer>
        </x-card>
        <x-card hover>
            <h4 class="font-semibold mb-2">Hover card</h4>
            <p class="text-sm text-cream-600 dark:text-cream-400">Pasa el cursor por encima.</p>
        </x-card>
    </div>
</x-section>

{{-- ===== Stat cards ===== --}}
<x-section title="Stat cards (KPIs)" id="stats">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card icon="dollar-sign" label="Ingresos" value="$12,450" :trend="8.2" trendLabel="vs mes anterior" color="primary" />
        <x-stat-card icon="shopping-bag" label="Pedidos" value="248" :trend="-2.5" color="rose" />
        <x-stat-card icon="users" label="Clientes" value="1,024" :trend="12.0" color="emerald" />
        <x-stat-card icon="star" label="Calificacion" value="4.8" color="accent" />
    </div>
</x-section>

{{-- ===== Badges ===== --}}
<x-section title="Badges" id="badges">
    <x-card>
        <div class="flex flex-wrap gap-2">
            <x-badge variant="primary">Primary</x-badge>
            <x-badge variant="accent">Accent</x-badge>
            <x-badge variant="success" icon="check">Success</x-badge>
            <x-badge variant="warning" icon="alert-triangle">Warning</x-badge>
            <x-badge variant="danger" icon="x">Danger</x-badge>
            <x-badge variant="neutral">Neutral</x-badge>
            <x-badge variant="sky">Sky</x-badge>
            <x-badge variant="primary" size="sm">Small</x-badge>
            <x-badge variant="primary" size="lg">Large</x-badge>
        </div>
    </x-card>
</x-section>

{{-- ===== Alerts ===== --}}
<x-section title="Alerts" id="alerts">
    <div class="space-y-3">
        <x-alert variant="info" title="Info" dismissible>
            Esto es un alert informativo.
        </x-alert>
        <x-alert variant="success" title="Success">
            La operacion se completo correctamente.
        </x-alert>
        <x-alert variant="warning" title="Warning" dismissible>
            Atento: esta accion puede tardar varios minutos.
        </x-alert>
        <x-alert variant="danger" title="Error">
            Algo salio mal. Intenta de nuevo.
        </x-alert>
    </div>
</x-section>

{{-- ===== Modales ===== --}}
<x-section title="Modales" description="Wrapper Preline data-hs-overlay" id="modals">
    <x-card>
        <div class="flex flex-wrap gap-2">
            <button type="button" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition-all" data-hs-overlay="#modal-demo">
                <x-icon name="square-stack" class="w-4 h-4" /> Abrir modal
            </button>
            <button type="button" class="inline-flex items-center gap-2 bg-cream-100 hover:bg-cream-200 text-cream-900 font-semibold px-4 py-2.5 rounded-xl text-sm dark:bg-cream-900 dark:text-cream-100 dark:hover:bg-cream-800 transition-all" data-hs-overlay="#modal-large">
                Modal grande
            </button>
        </div>
    </x-card>

    <x-modal id="modal-demo" title="Modal de prueba">
        <p class="text-sm text-cream-700 dark:text-cream-300">
            Este es el contenido del modal. Puede llevar formularios, listas, gráficas, lo que necesites.
        </p>
        <x-slot:footer>
            <x-button variant="ghost" data-hs-overlay="#modal-demo">Cancelar</x-button>
            <x-button variant="primary" icon="check">Confirmar</x-button>
        </x-slot:footer>
    </x-modal>

    <x-modal id="modal-large" title="Modal grande" size="lg">
        <div class="space-y-3">
            <p class="text-sm text-cream-700 dark:text-cream-300">Más espacio para contenido extenso.</p>
            <x-input label="Nombre" placeholder="..." />
            <x-textarea label="Comentarios" rows="3" />
        </div>
        <x-slot:footer>
            <x-button variant="ghost" data-hs-overlay="#modal-large">Cerrar</x-button>
            <x-button variant="primary">Guardar</x-button>
        </x-slot:footer>
    </x-modal>
</x-section>

{{-- ===== Dropdowns ===== --}}
<x-section title="Dropdowns" id="dropdowns">
    <x-card>
        <div class="flex flex-wrap gap-3">
            <x-dropdown>
                <x-slot:trigger>
                    <span class="inline-flex items-center gap-2 bg-cream-100 hover:bg-cream-200 text-cream-900 font-medium px-3 py-2 rounded-xl text-sm dark:bg-cream-900 dark:text-cream-100">
                        Acciones <x-icon name="chevron-down" class="w-4 h-4" />
                    </span>
                </x-slot:trigger>

                <x-dropdown-item href="#" icon="edit">Editar</x-dropdown-item>
                <x-dropdown-item href="#" icon="copy">Duplicar</x-dropdown-item>
                <x-dropdown-item href="#" icon="archive">Archivar</x-dropdown-item>
                <div class="my-1 h-px bg-cream-200 dark:bg-cream-800"></div>
                <x-dropdown-item icon="trash-2" class="text-rose-600 hover:bg-rose-50">Eliminar</x-dropdown-item>
            </x-dropdown>
        </div>
    </x-card>
</x-section>

{{-- ===== Tabs / Acordeon ===== --}}
<x-section title="Tabs y acordeon" id="tabs">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <x-card>
            <x-tabs :tabs="[
                ['key' => 'general', 'label' => 'General', 'icon' => 'home'],
                ['key' => 'config', 'label' => 'Config', 'icon' => 'settings'],
                ['key' => 'extras', 'label' => 'Extras', 'icon' => 'sparkles'],
            ]">
                <x-slot:general>
                    <p class="text-sm text-cream-700 dark:text-cream-300">Contenido del tab General.</p>
                </x-slot:general>
                <x-slot:config>
                    <p class="text-sm text-cream-700 dark:text-cream-300">Contenido del tab Config.</p>
                </x-slot:config>
                <x-slot:extras>
                    <p class="text-sm text-cream-700 dark:text-cream-300">Contenido del tab Extras.</p>
                </x-slot:extras>
            </x-tabs>
        </x-card>

        <x-accordion :items="[
            ['title' => '¿Que tipos de comida ofrecen?', 'content' => 'Comida casera, sopas, y opciones saludables.'],
            ['title' => '¿Hacen domicilios?', 'content' => 'Si, en toda la ciudad con tarifa segun zona.'],
            ['title' => '¿Aceptan reservas?', 'content' => 'Si, llamar con al menos 4 horas de anticipacion.'],
        ]" />
    </div>
</x-section>

{{-- ===== Tooltips / Avatars ===== --}}
<x-section title="Tooltips y avatars" id="tooltips">
    <x-card>
        <div class="flex flex-wrap items-center gap-6">
            <x-tooltip text="Esto es un tooltip">
                <x-button variant="ghost" icon="info">Hover me</x-button>
            </x-tooltip>
            <x-tooltip text="Tooltip abajo" position="bottom">
                <x-button variant="ghost">Bottom</x-button>
            </x-tooltip>

            <div class="flex items-center -space-x-2">
                <x-avatar name="Ana Perez" size="md" class="ring-2 ring-white dark:ring-surface-dark" />
                <x-avatar name="Luis Gomez" size="md" class="ring-2 ring-white dark:ring-surface-dark" />
                <x-avatar name="Maria Diaz" size="md" class="ring-2 ring-white dark:ring-surface-dark" />
                <x-avatar name="+5" size="md" class="ring-2 ring-white dark:ring-surface-dark" />
            </div>

            <div class="flex items-center gap-3">
                <x-avatar name="Mini" size="xs" />
                <x-avatar name="Small" size="sm" />
                <x-avatar name="Medium" size="md" />
                <x-avatar name="Large" size="lg" />
                <x-avatar name="Extra Large" size="xl" ring />
            </div>
        </div>
    </x-card>
</x-section>

{{-- ===== Tablas ===== --}}
<x-section title="Tablas" description="Tabla con busqueda global, filtros por columna, sort, selector de filas por pagina (5/10/25/50/100/Todas) y paginacion — todo en cliente. La busqueda y los filtros operan sobre el texto visible, no sobre el HTML de la celda." id="tables">
    <x-data-table
        :columns="[
            ['key' => 'plato', 'label' => 'Plato', 'sortable' => true],
            ['key' => 'precio', 'label' => 'Precio', 'sortable' => true],
            ['key' => 'estado', 'label' => 'Estado'],
            ['key' => 'pedidos', 'label' => 'Pedidos', 'sortable' => true],
        ]"
        :filters="[['key' => 'estado', 'label' => 'Estado']]"
        :rows="[
            ['plato' => 'Ajiaco', 'precio' => '$18,000', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold dark:bg-emerald-900/40 dark:text-emerald-200&quot;>Activo</span>', 'pedidos' => 124],
            ['plato' => 'Sancocho', 'precio' => '$22,000', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold dark:bg-emerald-900/40 dark:text-emerald-200&quot;>Activo</span>', 'pedidos' => 87],
            ['plato' => 'Crema de tomate', 'precio' => '$12,000', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold dark:bg-amber-900/40 dark:text-amber-200&quot;>Agotado</span>', 'pedidos' => 45],
            ['plato' => 'Sopa de pollo', 'precio' => '$15,000', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold dark:bg-emerald-900/40 dark:text-emerald-200&quot;>Activo</span>', 'pedidos' => 198],
            ['plato' => 'Caldo de costilla', 'precio' => '$16,500', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold dark:bg-emerald-900/40 dark:text-emerald-200&quot;>Activo</span>', 'pedidos' => 156],
            ['plato' => 'Mute santandereano', 'precio' => '$24,000', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-xs font-semibold dark:bg-rose-900/40 dark:text-rose-200&quot;>Inactivo</span>', 'pedidos' => 12],
            ['plato' => 'Sopa de lentejas', 'precio' => '$10,000', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold dark:bg-emerald-900/40 dark:text-emerald-200&quot;>Activo</span>', 'pedidos' => 78],
            ['plato' => 'Sopa de pasta', 'precio' => '$11,500', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold dark:bg-emerald-900/40 dark:text-emerald-200&quot;>Activo</span>', 'pedidos' => 60],
            ['plato' => 'Crema de espinaca', 'precio' => '$13,000', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold dark:bg-emerald-900/40 dark:text-emerald-200&quot;>Activo</span>', 'pedidos' => 32],
            ['plato' => 'Sopa de pescado', 'precio' => '$28,000', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold dark:bg-amber-900/40 dark:text-amber-200&quot;>Agotado</span>', 'pedidos' => 21],
            ['plato' => 'Crema de zanahoria', 'precio' => '$11,000', 'estado' => '<span class=&quot;inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold dark:bg-emerald-900/40 dark:text-emerald-200&quot;>Activo</span>', 'pedidos' => 55],
        ]"
    />
</x-section>

{{-- ===== Graficas ===== --}}
<x-section title="Graficas" description="ApexCharts con paleta café/oliva (lazy import)" id="charts">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <x-card>
            <x-slot:header><h4 class="font-semibold">Linea</h4></x-slot:header>
            <x-chart
                type="line"
                :series="[
                    ['name' => 'Ventas', 'data' => [12, 18, 14, 22, 28, 24, 32]],
                    ['name' => 'Costos', 'data' => [8, 11, 9, 13, 15, 12, 18]],
                ]"
                :options="['xaxis' => ['categories' => ['Lun','Mar','Mie','Jue','Vie','Sab','Dom']]]"
                :height="260"
            />
        </x-card>

        <x-card>
            <x-slot:header><h4 class="font-semibold">Barras</h4></x-slot:header>
            <x-chart
                type="bar"
                :series="[['name' => 'Pedidos', 'data' => [44, 55, 41, 67, 22, 43, 65]]]"
                :options="['xaxis' => ['categories' => ['Ene','Feb','Mar','Abr','May','Jun','Jul']], 'plotOptions' => ['bar' => ['borderRadius' => 8, 'columnWidth' => '50%']]]"
                :height="260"
            />
        </x-card>

        <x-card>
            <x-slot:header><h4 class="font-semibold">Donut</h4></x-slot:header>
            <x-chart
                type="donut"
                :series="[44, 55, 13, 33]"
                :options="['labels' => ['Sopas', 'Cremas', 'Caldos', 'Otros'], 'legend' => ['position' => 'bottom']]"
                :height="280"
            />
        </x-card>

        <x-card>
            <x-slot:header><h4 class="font-semibold">Area</h4></x-slot:header>
            <x-chart
                type="area"
                :series="[['name' => 'Visitas', 'data' => [30, 40, 35, 50, 49, 60, 70]]]"
                :options="[
                    'xaxis' => ['categories' => ['Lun','Mar','Mie','Jue','Vie','Sab','Dom']],
                    'fill' => ['type' => 'gradient', 'gradient' => ['shadeIntensity' => 1, 'opacityFrom' => 0.5, 'opacityTo' => 0.05]],
                ]"
                :height="260"
            />
        </x-card>
    </div>
</x-section>

{{-- ===== Empty state ===== --}}
<x-section title="Estado vacio" id="empty">
    <x-card>
        <x-empty-state
            title="Sin sopas en el menu"
            description="Cuando agregues platos al catalogo apareceran aqui."
        >
            <x-slot:actions>
                <x-button variant="primary" icon="plus" onclick="window.showToast('success', 'Demo: agregar sopa')">Agregar sopa</x-button>
                <x-button variant="ghost" icon="upload" onclick="window.showToast('info', 'Demo: importar CSV')">Importar CSV</x-button>
            </x-slot:actions>
        </x-empty-state>
    </x-card>
</x-section>

{{-- ===== Progress / Spinners ===== --}}
<x-section title="Progress y spinners" id="progress">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <x-card>
            <h4 class="font-semibold mb-4">Barras de progreso</h4>
            <div class="space-y-4">
                <x-progress :value="35" label="Pedidos del dia" showValue color="primary" />
                <x-progress :value="72" label="Capacidad" showValue color="accent" />
                <x-progress :value="92" label="Satisfaccion" showValue color="emerald" />
                <x-progress :value="14" label="Cancelaciones" showValue color="rose" />
            </div>
        </x-card>

        <x-card>
            <h4 class="font-semibold mb-4">Spinners</h4>
            <div class="flex flex-wrap items-end gap-6">
                <x-spinner size="xs" />
                <x-spinner size="sm" />
                <x-spinner size="md" />
                <x-spinner size="lg" />
                <x-spinner size="xl" label="Cargando..." />
            </div>
            <div class="mt-6">
                <div class="skeleton h-4 w-3/4 mb-2"></div>
                <div class="skeleton h-4 w-1/2"></div>
            </div>
        </x-card>
    </div>
</x-section>

{{-- ===== Iconos ===== --}}
<x-section title="Iconos Lucide" description="Tree-shakeable, stroke 1.75 por defecto" id="icons">
    <x-card>
        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-12 gap-4 text-center">
            @foreach (['home','user','user-cog','users','settings','search','plus','minus','x','check','heart','star','bell','mail','lock','eye','calendar','clock','map-pin','phone','utensils-crossed','chef-hat','soup','coffee','shopping-cart','shopping-bag','credit-card','dollar-sign','trending-up','trending-down','bar-chart-3','pie-chart','activity','zap','sparkles','sun','moon','cloud','wifi','log-in','log-out','arrow-right','arrow-left','arrow-up','arrow-down','chevron-up','chevron-down','chevron-left','chevron-right'] as $iconName)
                <div class="flex flex-col items-center gap-1.5 group">
                    <span class="inline-flex w-10 h-10 rounded-xl bg-cream-100 group-hover:bg-primary-100 group-hover:text-primary-700 text-cream-700 items-center justify-center transition-colors dark:bg-cream-900 dark:text-cream-300">
                        <x-icon :name="$iconName" class="w-5 h-5" />
                    </span>
                    <span class="text-[10px] text-cream-500 truncate w-full">{{ $iconName }}</span>
                </div>
            @endforeach
        </div>
    </x-card>
</x-section>

@endsection
