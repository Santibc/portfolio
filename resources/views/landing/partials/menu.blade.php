{{-- Menú como tabla nutricional: filas = productos, columnas = qué lleva, tamaños y cómo pedir --}}
<section id="menu" class="relative overflow-x-clip py-20 lg:py-28" aria-labelledby="titulo-menu">
    {{-- En celular el paquete sale de cuadro mientras se lee la tabla --}}
    <div class="pointer-events-none absolute right-[-75vw] top-[30%] h-[40vh] w-[30vw] lg:hidden" data-bag-anchor="menu-movil" data-bag-mode="park" aria-hidden="true"></div>

    <div class="contenedor">
        <div class="grid items-end gap-6 lg:grid-cols-12">
            <div class="sobre-3d lg:col-span-8">
                <h2 id="titulo-menu" class="titulo-seccion">Elige tu dorilokos</h2>
                <p class="texto-suave mt-5 max-w-[60ch] text-lg">Todos se arman al momento, en paquete personal o familiar. Toca «Pedir» y te escribimos por WhatsApp para confirmar.</p>
            </div>
            {{-- El paquete descansa sobre el borde del panel (mt-10 = 40 px; translate-y-6 deja aire para su vaivén) y se va con él al bajar --}}
            <div class="hidden h-[24vh] lg:col-span-4 lg:block lg:translate-y-6" data-bag-anchor="menu" data-bag-mode="park" data-bag-rot="0.1,0.55,0.14"></div>
        </div>

        <div class="panel-nutri mt-10 p-5 sm:p-7 lg:p-8">
            <table class="tabla-menu w-full border-collapse text-left">
                <caption class="sr-only">Dorilokos de {{ $negocio['nombre'] }}: precios en paquete personal y familiar</caption>
                <thead class="rotulo text-[0.8rem]">
                    <tr class="border-b-[10px] border-[color:var(--regla)]">
                        <th scope="col" class="pb-2.5 pr-6">Dorilokos</th>
                        <th scope="col" class="pb-2.5 pr-6">Qué lleva</th>
                        <th scope="col" class="pb-2.5 pr-6 md:text-right">Personal</th>
                        <th scope="col" class="pb-2.5 pr-6 md:text-right">Familiar</th>
                        <th scope="col" class="pb-2.5"><span class="sr-only">Pedir</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($menu['dorilokos'] as $item)
                        <tr class="border-b border-primary-950/[0.22] last:border-b-0">
                            <th scope="row" class="py-4 pr-6 align-middle font-normal">
                                <span class="block font-brand text-[1.75rem] uppercase leading-none">{{ $item['nombre'] }}</span>
                                @if ($item['etiqueta'])
                                    <span class="rotulo mt-1.5 inline-block rounded bg-accent-300 px-1.5 py-0.5 text-[0.7rem] text-primary-950">{{ $item['etiqueta'] }}</span>
                                @endif
                            </th>
                            <td class="texto-panel-tenue py-4 pr-6 text-[0.98rem] leading-snug md:max-w-[42ch]">{{ $item['descripcion'] }}</td>
                            <td class="py-4 pr-6 md:text-right" data-etiqueta="Personal">
                                <span class="text-2xl font-extrabold tracking-tight">{{ $item['personal_formateado'] }}</span>
                            </td>
                            <td class="py-4 pr-6 md:text-right" data-etiqueta="Familiar">
                                <span class="text-xl font-bold tracking-tight">{{ $item['familiar_formateado'] }}</span>
                            </td>
                            <td class="py-4 md:text-right">
                                <x-button variant="pedido" size="md" :href="$item['url_pedido']" target="_blank" rel="noopener"
                                          aria-label="Pedir {{ $item['pedido'] }} por WhatsApp" class="!rounded-full">
                                    <x-brand-icon name="whatsapp" class="h-4 w-4" />
                                    Pedir
                                </x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="regla-gruesa mt-2"></div>

            <div class="grid gap-x-10 gap-y-8 pt-6 lg:grid-cols-2">
                <div>
                    <h3 class="panel-nutri__titulo text-[1.6rem]">Combos</h3>
                    <ul class="mt-2">
                        @foreach ($menu['combos'] as $combo)
                            <li class="regla-fina flex flex-wrap items-center justify-between gap-x-6 gap-y-2 py-3 first:border-t-0">
                                <div>
                                    <p class="font-brand text-xl uppercase leading-none">{{ $combo['nombre'] }}</p>
                                    <p class="texto-panel-tenue mt-1 text-[0.95rem]">{{ $combo['descripcion'] }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-2xl font-extrabold tracking-tight">{{ $combo['precio_formateado'] }}</span>
                                    <x-button variant="pedido" size="md" :href="$combo['url_pedido']" target="_blank" rel="noopener"
                                              aria-label="Pedir {{ $combo['nombre'] }} por WhatsApp" class="!rounded-full">
                                        <x-brand-icon name="whatsapp" class="h-4 w-4" />
                                        Pedir
                                    </x-button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                    <div>
                        <h3 class="panel-nutri__titulo text-[1.6rem]">Bebidas</h3>
                        <p class="texto-panel-tenue mt-2 text-[0.95rem] leading-snug">{{ $menu['bebidas']['detalle'] }}</p>
                        <p class="mt-2 font-extrabold">De {{ $menu['bebidas']['desde_formateado'] }} a {{ $menu['bebidas']['hasta_formateado'] }}</p>
                    </div>
                    <div>
                        <h3 class="panel-nutri__titulo text-[1.6rem]">Adiciones</h3>
                        <dl class="mt-2">
                            @foreach ($menu['adiciones'] as $adicion)
                                <div class="regla-fina flex items-baseline justify-between gap-4 py-1.5 first:border-t-0">
                                    <dt>{{ $adicion['nombre'] }}</dt>
                                    <dd class="font-extrabold">{{ $adicion['precio_formateado'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>

            <div class="regla-media mt-6"></div>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-3 pt-5">
                <p class="rotulo text-sm">Elige la base</p>
                <ul class="flex flex-wrap gap-2">
                    @foreach ($bases as $base)
                        <li class="rounded-full border-2 border-[color:var(--regla)] px-3 py-1 text-sm font-bold">{{ $base }}</li>
                    @endforeach
                </ul>
            </div>
            <p class="letra-legal texto-panel-tenue mt-5">Precios del local. En apps de domicilio pueden variar.</p>
        </div>
    </div>
</section>
