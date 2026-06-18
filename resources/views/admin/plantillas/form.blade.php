@extends('layouts.app')

@section('title', $plantilla->exists ? 'Editar plantilla' : 'Nueva plantilla')

@push('styles')
    <style>
        /* Dark mode para el editor TinyMCE */
        html.dark .tox-tinymce { border-color: #3f3f46 !important; }
        html.dark .tox .tox-toolbar,
        html.dark .tox .tox-toolbar__overflow,
        html.dark .tox .tox-toolbar__primary,
        html.dark .tox .tox-menubar,
        html.dark .tox .tox-edit-area__iframe { background-color: #18181b !important; }
        html.dark .tox .tox-tbtn { color: #e5e7eb !important; }
        html.dark .tox .tox-tbtn:hover { background-color: #3f3f46 !important; }
        html.dark .tox .tox-statusbar { background-color: #18181b !important; color: #a1a1aa !important; border-top-color: #3f3f46 !important; }

        /* Panel avanzado de CSS */
        details > summary { cursor: pointer; user-select: none; list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
        details > summary::before { content: '▸'; display: inline-block; margin-right: 6px; transition: transform 0.15s; }
        details[open] > summary::before { transform: rotate(90deg); }
    </style>
@endpush

@section('content')
    <x-manzer.page-header
        :title="$plantilla->exists ? 'Editar ' . $plantilla->nombre : 'Nueva plantilla'"
        description="Editor tipo Word — escribe y formatea tu factura. Los colores y estilos que ves son los mismos del PDF final."
    >
        <x-slot name="actions">
            <x-manzer.button variant="ghost" icon="arrow-left" href="{{ route('admin.plantillas.index') }}">
                Volver
            </x-manzer.button>
            <x-manzer.button type="button" variant="outline" icon="eye" onclick="previsualizarPlantilla()">
                Previsualizar con datos reales
            </x-manzer.button>
        </x-slot>
    </x-manzer.page-header>

    {{-- Mensajes flash y errores de validación se renderizan globalmente vía <x-flash-messages /> en el layout. --}}

    <form
        action="{{ $plantilla->exists ? route('admin.plantillas.update', $plantilla) : route('admin.plantillas.store') }}"
        method="POST"
        class="space-y-5"
        id="plantilla-form"
    >
        @csrf
        @if ($plantilla->exists)
            @method('PUT')
        @endif

        <div class="card space-y-5">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-manzer.form-group
                    label="Nombre"
                    name="nombre"
                    type="text"
                    :value="old('nombre', $plantilla->nombre)"
                    :required="true"
                    icon="tag"
                    placeholder="Plantilla estándar"
                />
                <x-manzer.form-group
                    label="Descripción"
                    name="descripcion"
                    type="text"
                    :value="old('descripcion', $plantilla->descripcion)"
                    icon="card-text"
                    placeholder="Uso interno (opcional)"
                />
            </div>
        </div>

        <div class="card space-y-4">
            <div>
                <div class="flex items-start justify-between gap-4 mb-2">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            <i class="bi bi-file-earmark-text mr-1"></i>
                            Editor de factura (tipo Word)
                            <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Usa los botones de la barra superior para dar formato. Para insertar datos dinámicos (nombre del cliente, totales, etc.), haz click en <strong>"Insertar campo"</strong>.
                        </p>
                    </div>
                    <button type="button" onclick="restaurarDisenoPorDefecto()"
                        class="shrink-0 inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-800 hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-300 dark:hover:bg-amber-900/40">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Restaurar diseño por defecto
                    </button>
                </div>
                <textarea id="html_content" name="html_content">{{ old('html_content', $plantilla->html_content ?? '') }}</textarea>
                @error('html_content')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <details class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-800">
                <summary class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Estilos CSS avanzados
                    <span class="ml-1 text-xs font-normal text-zinc-500">(opcional — solo si sabes CSS)</span>
                </summary>
                <div class="mt-3 space-y-2">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        Estos estilos se aplican tanto en el editor como en el PDF final. Define aquí colores corporativos, bordes, fuentes, etc.
                    </p>
                    <textarea
                        id="css_content"
                        name="css_content"
                        rows="10"
                        class="w-full rounded-lg border border-zinc-300 bg-zinc-50 p-3 font-mono text-xs text-zinc-700 focus:border-primary-500 focus:ring-primary-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300"
                        placeholder="body { font-family: Arial; } .factura { max-width: 900px; } ..."
                    >{{ old('css_content', $plantilla->css_content ?? $cssDefault) }}</textarea>
                    @error('css_content')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </details>
        </div>

        <div class="card space-y-4">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                Configuración
            </h3>

            <div class="max-w-sm">
                <x-manzer.form-group
                    label="Tipo de factura"
                    name="tipo"
                    type="select"
                    :value="old('tipo', $plantilla->tipo ?? 'nacional')"
                    :options="['nacional' => 'Nacional', 'internacional' => 'Internacional (exportación)']"
                    icon="globe"
                    :required="true"
                    help="Las facturas que usen esta plantilla se emitirán a Siigo como nacionales o de exportación." />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-8">
                <label class="inline-flex items-start gap-2">
                    <input type="hidden" name="es_default" value="0">
                    <input type="checkbox" id="es_default" name="es_default" value="1"
                        @checked(old('es_default', $plantilla->es_default ?? false))
                        class="mt-0.5 h-4 w-4 rounded border-zinc-300 text-primary-600 focus:ring-primary-500 dark:border-zinc-600 dark:bg-zinc-800">
                    <span class="text-sm">
                        <span class="font-medium">Marcar como predeterminada</span>
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">Sustituye a la actual predeterminada.</span>
                    </span>
                </label>

                <label class="inline-flex items-start gap-2">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" id="activo" name="activo" value="1"
                        @checked(old('activo', $plantilla->exists ? $plantilla->activo : true))
                        class="mt-0.5 h-4 w-4 rounded border-zinc-300 text-primary-600 focus:ring-primary-500 dark:border-zinc-600 dark:bg-zinc-800">
                    <span class="text-sm">
                        <span class="font-medium">Activa</span>
                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">Disponible para emitir facturas.</span>
                    </span>
                </label>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <x-manzer.button variant="ghost" href="{{ route('admin.plantillas.index') }}">
                    Cancelar
                </x-manzer.button>
                <x-manzer.button type="submit" variant="primary" icon="check-lg">
                    Guardar plantilla
                </x-manzer.button>
            </div>
        </div>
    </form>

    {{-- Modal vista previa --}}
    <div
        x-data="{ open: false }"
        x-on:open-modal.window="$event.detail === 'modal-preview' ? open = true : null"
        x-on:close-modal.window="$event.detail === 'modal-preview' ? open = false : null"
        x-on:keydown.escape.window="open = false"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
        role="dialog"
        aria-modal="true"
    >
        <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-zinc-900/60 backdrop-blur-sm"></div>
        <div x-show="open" x-transition class="relative w-full max-w-5xl rounded-2xl bg-white shadow-xl dark:bg-zinc-900 dark:ring-1 dark:ring-zinc-800">
            <div class="flex items-center justify-between border-b border-zinc-200 p-4 dark:border-zinc-800">
                <h2 class="text-lg font-semibold tracking-tight">
                    <i class="bi bi-eye mr-1"></i>
                    Vista previa con datos reales
                </h2>
                <button type="button" @click="open = false" class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-200">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-4">
                <div id="preview-loader" class="hidden items-center justify-center py-24 text-sm text-zinc-500">
                    <i class="bi bi-arrow-clockwise animate-spin mr-2"></i>
                    Generando vista previa...
                </div>
                <iframe id="preview-frame" class="w-full h-[600px] rounded-lg border border-zinc-200 bg-white dark:border-zinc-800" sandbox="allow-same-origin" title="Vista previa"></iframe>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        // ============================
        // Plantillas por defecto (CSS + HTML base con diseño naranja compatible con DomPDF)
        // ============================
        const cssDefault = @json($cssDefault);
        const htmlDefault = @json($htmlDefault);

        // CSS actual de la plantilla → se inyecta en el editor para ver el resultado real
        const cssEditor = document.getElementById('css_content').value;

        // ============================
        // Catálogo de merge tags agrupados
        // ============================
        @verbatim
        const mergeTags = [
            { group: 'Empresa', tags: [
                { label: 'Razón social',      value: '{{empresa.razon_social}}' },
                { label: 'NIT',               value: '{{empresa.nit}}' },
                { label: 'Dirección',         value: '{{empresa.direccion}}' },
                { label: 'Teléfono',          value: '{{empresa.telefono}}' },
                { label: 'Email',             value: '{{empresa.email}}' },
                { label: 'Sitio web',         value: '{{empresa.sitio_web}}' },
                { label: 'Logo (imagen)',     value: '{{empresa.logo}}' },
                { label: 'Régimen (leyenda)', value: '{{empresa.regimen}}' },
                { label: 'Resolución CLC',    value: '{{empresa.resolucion_clc}}' },
                { label: 'Resolución FV',     value: '{{empresa.resolucion_fv}}' },
            ]},
            { group: 'Cliente', tags: [
                { label: 'Nombre',                  value: '{{cliente.nombre}}' },
                { label: 'Identificación',          value: '{{cliente.identificacion}}' },
                { label: 'Dirección facturación',   value: '{{cliente.direccion_facturacion}}' },
                { label: 'Dirección envío',         value: '{{cliente.direccion_envio}}' },
                { label: 'Email',                   value: '{{cliente.email}}' },
                { label: 'Teléfono',                value: '{{cliente.telefono}}' },
                { label: 'Incoterm',                value: '{{cliente.incoterm}}' },
                { label: 'Puerto',                  value: '{{cliente.puerto}}' },
                { label: 'Origen',                  value: '{{cliente.origen}}' },
                { label: 'Destino',                 value: '{{cliente.destino}}' },
            ]},
            { group: 'Factura', tags: [
                { label: 'Número',              value: '{{factura.numero}}' },
                { label: 'Fecha',               value: '{{factura.fecha}}' },
                { label: 'Vencimiento',         value: '{{factura.vencimiento}}' },
                { label: 'Moneda',              value: '{{factura.moneda}}' },
                { label: 'Símbolo',             value: '{{factura.simbolo}}' },
                { label: 'CUFE',                value: '{{factura.cufe}}' },
                { label: 'Observaciones',       value: '{{factura.observaciones}}' },
                { label: 'Tasa de cambio',      value: '{{factura.tasa_cambio}}' },
                { label: 'QR Siigo (HTML)',     value: '{{factura.qr_html}}' },
                { label: 'PO#',                 value: '{{factura.po}}' },
                { label: 'AWB',                 value: '{{factura.awb}}' },
                { label: 'Shipper',             value: '{{factura.shipper}}' },
                { label: 'Cod',                 value: '{{factura.cod}}' },
                { label: 'Payment Terms',       value: '{{factura.payment_terms}}' },
                { label: 'Versión plantilla',   value: '{{factura.version}}' },
            ]},
            { group: 'Totales', tags: [
                { label: 'Subtotal',    value: '{{totales.subtotal}}' },
                { label: 'IVA',         value: '{{totales.iva}}' },
                { label: 'Flete',       value: '{{totales.flete}}' },
                { label: 'Seguro',      value: '{{totales.seguro}}' },
                { label: 'Descuento',   value: '{{totales.descuento}}' },
                { label: 'Total',       value: '{{totales.total}}' },
                { label: 'Total COP',   value: '{{totales.total_cop}}' },
            ]},
            { group: 'Banco', tags: [
                { label: 'Nombre',          value: '{{banco.nombre}}' },
                { label: 'País',            value: '{{banco.pais}}' },
                { label: 'Dirección',       value: '{{banco.direccion}}' },
                { label: 'SWIFT/BIC',       value: '{{banco.swift}}' },
                { label: 'Número cuenta',   value: '{{banco.numero_cuenta}}' },
                { label: 'Titular',         value: '{{banco.titular}}' },
                { label: 'Moneda',          value: '{{banco.moneda}}' },
            ]},
            { group: 'Contacto', tags: [
                { label: 'Nombre',      value: '{{contacto.nombre}}' },
                { label: 'Email',       value: '{{contacto.email}}' },
                { label: 'Teléfono',    value: '{{contacto.telefono}}' },
            ]},
            { group: 'Línea de factura (dentro de filas con data-loop="items")', tags: [
                { label: 'Referencia',          value: '{{referencia}}' },
                { label: 'Descripción',         value: '{{descripcion}}' },
                { label: 'Color',               value: '{{color}}' },
                { label: 'Talla (size)',        value: '{{size}}' },
                { label: 'Composición',         value: '{{composition}}' },
                { label: 'Código arancelario',  value: '{{codigo_pa}}' },
                { label: 'Cantidad',            value: '{{cantidad}}' },
                { label: 'Precio unitario',     value: '{{precio_unitario}}' },
                { label: 'Descuento línea',     value: '{{descuento}}' },
                { label: 'Total línea',         value: '{{total}}' },
                { label: 'Índice (#fila)',      value: '{{@index}}' },
            ]},
        ];
        @endverbatim

        // ============================
        // Inicialización TinyMCE
        // ============================
        tinymce.init({
            selector: 'textarea#html_content',
            license_key: 'gpl',
            height: 780,
            menubar: 'file edit view insert format table tools help',
            promotion: false,
            branding: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                'preview', 'anchor', 'searchreplace', 'visualblocks',
                'code', 'fullscreen', 'insertdatetime', 'media', 'table',
                'help', 'wordcount',
            ],
            toolbar: [
                'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor',
                'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table hr | mergetags | removeformat | fullscreen preview code',
            ],
            font_family_formats: 'Arial=arial,helvetica,sans-serif; Helvetica=helvetica,arial,sans-serif; Times New Roman=times new roman,times,serif; Georgia=georgia,serif; Courier New=courier new,courier,monospace; Verdana=verdana,geneva,sans-serif',
            font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 22pt 26pt 32pt',
            block_formats: 'Párrafo=p; Encabezado 1=h1; Encabezado 2=h2; Encabezado 3=h3; Encabezado 4=h4; Preformateado=pre',

            // CRÍTICO: permitir atributos data-* (data-loop usado por el TemplateRenderer para iterar items).
            // Y permitir HTML completamente libre — el backend sanitiza script/on*/javascript:
            valid_elements: '*[*]',
            valid_children: '+body[style],+body[script]',
            extended_valid_elements: 'tr[data-loop|class|style],td[colspan|rowspan|class|style],th[colspan|rowspan|class|style],div[data-loop|class|style|id],span[data-tag|class|style],img[src|alt|width|height|style|class]',

            // No convertir URLs a absolutas/relativas automáticamente
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,

            // Preservar texto/celdas con merge tags — TinyMCE no debe limpiarlas como "vacías"
            forced_root_block: 'p',
            keep_styles: true,

            // Pegar de Word / Google Docs — integrado en core de TinyMCE 7
            paste_as_text: false,

            // Estilos que se ven DENTRO del editor (iframe interno de TinyMCE)
            // Así el usuario ve la factura tal cual será el PDF — con líneas naranjas.
            content_style: cssEditor + `
                /* Hints visuales solo dentro del editor — no afectan el PDF */
                tr[data-loop]::before {
                    content: '↻ ' attr(data-loop);
                    position: absolute;
                    background: #fb923c;
                    color: white;
                    font-size: 9px;
                    padding: 1px 5px;
                    border-radius: 3px;
                    margin-left: -60px;
                }
                body { padding: 20px; }
            `,

            // Toolbar button custom → Insertar campo (merge tags)
            setup: function(editor) {
                editor.ui.registry.addMenuButton('mergetags', {
                    text: 'Insertar campo',
                    tooltip: 'Inserta un dato dinámico de la factura (cliente, totales, etc.)',
                    icon: 'code-sample',
                    fetch: function(callback) {
                        const items = mergeTags.map(group => ({
                            type: 'nestedmenuitem',
                            text: group.group,
                            getSubmenuItems: () => group.tags.map(t => ({
                                type: 'menuitem',
                                text: t.label + '  —  ' + t.value,
                                onAction: () => editor.insertContent(t.value)
                            }))
                        }));
                        callback(items);
                    }
                });

                // Auto-sync textarea → útil si algún validador externo lee el textarea antes del submit
                editor.on('change input keyup', function() {
                    editor.save();
                });
            },
        });

        // ============================
        // Submit → sincronizar TinyMCE con textarea antes de enviar
        // ============================
        document.getElementById('plantilla-form').addEventListener('submit', function() {
            if (window.tinymce) {
                tinymce.triggerSave();
            }
        });

        // ============================
        // Restaurar diseño por defecto
        // Reemplaza el HTML y el CSS con las plantillas base (layout con tables → DomPDF safe)
        // ============================
        function restaurarDisenoPorDefecto() {
            window.Swal.fire({
                title: '¿Restaurar diseño por defecto?',
                text: 'Se reemplazará todo el contenido del editor y los estilos CSS con el diseño naranja por defecto. Esta acción no se puede deshacer (pero aún no se guardará hasta que hagas click en "Guardar plantilla").',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#f97316',
            }).then((result) => {
                if (!result.isConfirmed) return;

                // Actualiza el HTML del editor
                if (window.tinymce && tinymce.activeEditor) {
                    tinymce.activeEditor.setContent(htmlDefault);
                } else {
                    document.getElementById('html_content').value = htmlDefault;
                }

                // Actualiza el textarea de CSS
                document.getElementById('css_content').value = cssDefault;

                window.Swal.fire({
                    icon: 'success',
                    title: 'Diseño restaurado',
                    text: 'Los estilos CSS también se actualizaron. Recarga la página para ver los nuevos estilos dentro del editor, o guarda y vuelve a entrar.',
                    timer: 3500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end',
                });
            });
        }

        // ============================
        // Vista previa con datos reales
        // ============================
        async function previsualizarPlantilla() {
            if (window.tinymce) {
                tinymce.triggerSave();
            }
            const htmlContent = document.getElementById('html_content').value;
            const cssContent = document.getElementById('css_content').value;
            const iframe = document.getElementById('preview-frame');
            const loader = document.getElementById('preview-loader');

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-preview' }));
            iframe.classList.add('hidden');
            loader.classList.remove('hidden');
            loader.classList.add('flex');

            try {
                const response = await fetch("{{ route('admin.plantillas.previsualizar') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ html_content: htmlContent, css_content: cssContent }),
                });
                if (!response.ok) throw new Error('HTTP ' + response.status);
                const data = await response.json();
                iframe.srcdoc = data.html || '<p style="padding:2rem;font-family:sans-serif;color:#6b7280">Sin contenido.</p>';
            } catch (err) {
                iframe.srcdoc = '<div style="padding:2rem;font-family:sans-serif;color:#b91c1c"><strong>Error al generar la vista previa.</strong><br>' + err.message + '</div>';
            } finally {
                loader.classList.add('hidden');
                loader.classList.remove('flex');
                iframe.classList.remove('hidden');
            }
        }
    </script>
@endpush
