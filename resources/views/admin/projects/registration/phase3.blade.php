<x-app-layout>
    <x-agromarket.page-header
        title="Evaluación Financiera"
        description="Fase 3 de 3: {{ $proyecto->nombre }}"
    >
        <x-slot name="actions">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-arrow-left"
                onclick="window.location.href='{{ route('admin.projects.registration.show', $proyecto) }}'"
            >
                Volver
            </x-agromarket.button>
        </x-slot>
    </x-agromarket.page-header>

    <!-- Indicador de Pasos -->
    <div style="background: white; padding: 1.5rem 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 0;">
            <!-- Paso 1 Completado -->
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #28a745; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    <i class="fas fa-check"></i>
                </div>
                <div style="margin-left: 0.75rem;">
                    <div style="font-weight: 600; color: #28a745;">Datos Básicos</div>
                    <div style="font-size: 0.8rem; color: #6c757d;">Completado</div>
                </div>
            </div>
            <div style="flex: 1; max-width: 100px; height: 2px; background: #28a745; margin: 0 1rem;"></div>
            <!-- Paso 2 Completado -->
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #28a745; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    <i class="fas fa-check"></i>
                </div>
                <div style="margin-left: 0.75rem;">
                    <div style="font-weight: 600; color: #28a745;">Evaluación Técnica</div>
                    <div style="font-size: 0.8rem; color: #6c757d;">Completado</div>
                </div>
            </div>
            <div style="flex: 1; max-width: 100px; height: 2px; background: #28a745; margin: 0 1rem;"></div>
            <!-- Paso 3 Actual -->
            <div style="display: flex; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #4A7C59; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                    3
                </div>
                <div style="margin-left: 0.75rem;">
                    <div style="font-weight: 600; color: #4A7C59;">Evaluación Financiera</div>
                    <div style="font-size: 0.8rem; color: #6c757d;">En progreso</div>
                </div>
            </div>
        </div>
    </div>

    @php
        $datosFinancieros = $proyecto->datos_financieros ?? [];
        $inversionSolicitada = $datosFinancieros['inversion_solicitada'] ?? [];
        $proyecciones = $datosFinancieros['proyecciones'] ?? [];
        $riesgos = $datosFinancieros['riesgos'] ?? [];
        $datosEarn = $proyecto->datos_earn ?? [];
        $datosFuturos = $proyecto->datos_futuros ?? [];
        $datosFarming = $proyecto->datos_farming ?? [];
    @endphp

    <!-- Formulario -->
    <form action="{{ route('admin.projects.registration.phase3.store', $proyecto) }}" method="POST" id="phase3Form">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Columna Izquierda -->
            <div>
                <!-- Desglose de Inversión -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-dollar-sign"></i> Desglose de Inversión Solicitada
                    </h3>

                    <p style="color: #6c757d; margin-bottom: 1.5rem; font-size: 0.875rem;">
                        Meta de financiamiento: <strong style="color: #2D5A27;">${{ number_format($proyecto->monto_objetivo, 0) }}</strong>
                    </p>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-agromarket.form-group
                            label="Insumos ($)"
                            name="inversion_insumos"
                            type="number"
                            icon="fas fa-box"
                            placeholder="0"
                            min="0"
                            :value="$inversionSolicitada['insumos'] ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Mano de Obra ($)"
                            name="inversion_mano_obra"
                            type="number"
                            icon="fas fa-users"
                            placeholder="0"
                            min="0"
                            :value="$inversionSolicitada['mano_obra'] ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Equipos ($)"
                            name="inversion_equipos"
                            type="number"
                            icon="fas fa-tools"
                            placeholder="0"
                            min="0"
                            :value="$inversionSolicitada['equipos'] ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Transporte ($)"
                            name="inversion_transporte"
                            type="number"
                            icon="fas fa-truck"
                            placeholder="0"
                            min="0"
                            :value="$inversionSolicitada['transporte'] ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Certificaciones ($)"
                            name="inversion_certificaciones"
                            type="number"
                            icon="fas fa-certificate"
                            placeholder="0"
                            min="0"
                            :value="$inversionSolicitada['certificaciones'] ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Empaques ($)"
                            name="inversion_empaques"
                            type="number"
                            icon="fas fa-box-open"
                            placeholder="0"
                            min="0"
                            :value="$inversionSolicitada['empaques'] ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Marketing ($)"
                            name="inversion_marketing"
                            type="number"
                            icon="fas fa-bullhorn"
                            placeholder="0"
                            min="0"
                            :value="$inversionSolicitada['marketing'] ?? ''"
                        ></x-agromarket.form-group>
                    </div>
                </div>

                <!-- Proyecciones -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-chart-line"></i> Proyecciones
                    </h3>

                    <x-agromarket.form-group
                        label="Producción Estimada"
                        name="produccion_estimada"
                        icon="fas fa-seedling"
                        placeholder="Ej: 100 cargas de café"
                        :value="$proyecciones['produccion_estimada'] ?? ''"
                    ></x-agromarket.form-group>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-agromarket.form-group
                            label="Precio Venta Estimado ($)"
                            name="precio_venta_estimado"
                            type="number"
                            icon="fas fa-tag"
                            placeholder="0"
                            min="0"
                            :value="$proyecciones['precio_venta_estimado'] ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Margen de Ganancia (%)"
                            name="margen_ganancia"
                            type="number"
                            icon="fas fa-percentage"
                            placeholder="25"
                            min="0"
                            max="100"
                            step="0.1"
                            :value="$proyecciones['margen_ganancia'] ?? ''"
                        ></x-agromarket.form-group>
                    </div>

                    <x-agromarket.form-group
                        label="Canales de Venta Actuales"
                        name="canales_venta_actuales"
                        type="textarea"
                        icon="fas fa-store"
                        placeholder="¿Dónde vende actualmente?"
                        :value="$proyecciones['canales_venta_actuales'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Canales de Venta Deseados"
                        name="canales_venta_deseados"
                        type="textarea"
                        icon="fas fa-globe"
                        placeholder="¿A qué mercados desea llegar?"
                        :value="$proyecciones['canales_venta_deseados'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Proyección de Ingresos"
                        name="proyeccion_ingresos"
                        type="textarea"
                        icon="fas fa-chart-bar"
                        placeholder="Ingresos esperados por período..."
                        :value="$proyecciones['proyeccion_ingresos'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Punto de Equilibrio"
                        name="punto_equilibrio"
                        icon="fas fa-balance-scale"
                        placeholder="¿Cuándo se recupera la inversión?"
                        :value="$proyecciones['punto_equilibrio'] ?? ''"
                    ></x-agromarket.form-group>
                </div>
            </div>

            <!-- Columna Derecha -->
            <div>
                <!-- Análisis de Riesgos -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #2D5A27; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-exclamation-triangle"></i> Análisis de Riesgos
                    </h3>

                    <x-agromarket.form-group
                        label="Riesgos por Plagas"
                        name="riesgo_plagas"
                        type="textarea"
                        icon="fas fa-bug"
                        placeholder="Plagas comunes y plan de manejo..."
                        :value="$riesgos['plagas'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Riesgos Climáticos"
                        name="riesgo_clima"
                        type="textarea"
                        icon="fas fa-cloud-rain"
                        placeholder="Fenómenos climáticos y mitigación..."
                        :value="$riesgos['clima'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Riesgos de Competencia"
                        name="riesgo_competencia"
                        type="textarea"
                        icon="fas fa-users"
                        placeholder="Competidores y diferenciación..."
                        :value="$riesgos['competencia'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Acceso a Mercados"
                        name="riesgo_acceso_mercados"
                        type="textarea"
                        icon="fas fa-store-alt"
                        placeholder="Barreras de entrada al mercado..."
                        :value="$riesgos['acceso_mercados'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Riesgos Regulatorios"
                        name="riesgo_regulaciones"
                        type="textarea"
                        icon="fas fa-gavel"
                        placeholder="Normativas y permisos requeridos..."
                        :value="$riesgos['regulaciones'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>
                </div>

                @if($proyecto->categoria && $proyecto->categoria->codigo === 'EAR')
                <!-- Datos específicos EAR -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #D4AF37; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-coins"></i> Datos EAR (Retiro Anticipado)
                    </h3>

                    <x-agromarket.form-group
                        label="Estado del Empaque"
                        name="earn_estado_empaque"
                        icon="fas fa-box"
                        placeholder="Estado actual del empaque del producto"
                        :value="$datosEarn['estado_empaque'] ?? ''"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Capacidad de Producción"
                        name="earn_capacidad_produccion"
                        icon="fas fa-industry"
                        placeholder="Capacidad máxima de producción"
                        :value="$datosEarn['capacidad_produccion'] ?? ''"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Costos por Unidad ($)"
                        name="earn_costos_por_unidad"
                        type="number"
                        icon="fas fa-calculator"
                        placeholder="0"
                        min="0"
                        step="0.01"
                        :value="$datosEarn['costos_por_unidad'] ?? ''"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Inventario Disponible"
                        name="earn_inventario_disponible"
                        icon="fas fa-warehouse"
                        placeholder="Stock actual disponible"
                        :value="$datosEarn['inventario_disponible'] ?? ''"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Necesidades para Escalar"
                        name="earn_necesidades_escalar"
                        type="textarea"
                        icon="fas fa-expand-arrows-alt"
                        placeholder="¿Qué necesita para aumentar producción?"
                        :value="$datosEarn['necesidades_escalar'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>
                </div>
                @endif

                @if($proyecto->categoria && $proyecto->categoria->codigo === 'FUTUROS')
                <!-- Datos específicos FUTUROS -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #17a2b8; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-rocket"></i> Datos FUTUROS (Contratos a Futuro)
                    </h3>

                    <x-agromarket.form-group
                        label="Plan de Expansión"
                        name="futuros_plan_expansion"
                        type="textarea"
                        icon="fas fa-expand"
                        placeholder="Planes de crecimiento..."
                        :value="$datosFuturos['plan_expansion'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Infraestructura Requerida"
                        name="futuros_infraestructura_requerida"
                        type="textarea"
                        icon="fas fa-building"
                        placeholder="Infraestructura necesaria..."
                        :value="$datosFuturos['infraestructura_requerida'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Proyección a 3 años"
                        name="futuros_proyeccion_3_anos"
                        type="textarea"
                        icon="fas fa-chart-line"
                        placeholder="¿Dónde estará en 3 años?"
                        :value="$datosFuturos['proyeccion_3_anos'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Proyección a 5 años"
                        name="futuros_proyeccion_5_anos"
                        type="textarea"
                        icon="fas fa-chart-area"
                        placeholder="¿Dónde estará en 5 años?"
                        :value="$datosFuturos['proyeccion_5_anos'] ?? ''"
                        rows="2"
                    ></x-agromarket.form-group>
                </div>
                @endif

                @if($proyecto->categoria && $proyecto->categoria->codigo === 'FARMING')
                <!-- Datos específicos FARMING -->
                <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    <h3 style="margin: 0 0 1.5rem 0; color: #28a745; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.75rem;">
                        <i class="fas fa-hands-helping"></i> Datos FARMING (Asociación Agrícola)
                    </h3>

                    <x-agromarket.form-group
                        label="Tipo de Asociación"
                        name="farming_tipo_asociacion"
                        icon="fas fa-sitemap"
                        placeholder="Cooperativa, Asociación, etc."
                        :value="$datosFarming['tipo_asociacion'] ?? ''"
                    ></x-agromarket.form-group>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-agromarket.form-group
                            label="Número de Asociados"
                            name="farming_numero_asociados"
                            type="number"
                            icon="fas fa-users"
                            placeholder="50"
                            min="1"
                            :value="$datosFarming['numero_asociados'] ?? ''"
                        ></x-agromarket.form-group>

                        <x-agromarket.form-group
                            label="Hectáreas Totales"
                            name="farming_hectareas_totales"
                            type="number"
                            icon="fas fa-ruler-combined"
                            placeholder="500"
                            min="0"
                            step="0.1"
                            :value="$datosFarming['hectareas_totales'] ?? ''"
                        ></x-agromarket.form-group>
                    </div>

                    <x-agromarket.form-group
                        label="Destino de Exportación"
                        name="farming_destino_exportacion"
                        icon="fas fa-globe-americas"
                        placeholder="USA, Europa, Asia..."
                        :value="$datosFarming['destino_exportacion'] ?? ''"
                    ></x-agromarket.form-group>

                    <x-agromarket.form-group
                        label="Proyección de Dividendos"
                        name="farming_proyeccion_dividendos"
                        type="textarea"
                        icon="fas fa-money-bill-wave"
                        placeholder="Dividendos trimestrales esperados..."
                        :value="$datosFarming['proyeccion_dividendos'] ?? ''"
                        rows="3"
                    ></x-agromarket.form-group>
                </div>
                @endif
            </div>
        </div>

        <!-- Botones de Acción -->
        <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
            <x-agromarket.button
                variant="secondary"
                icon="fas fa-arrow-left"
                type="button"
                onclick="window.location.href='{{ route('admin.projects.registration.phase2', $proyecto) }}'"
            >
                Volver a Fase 2
            </x-agromarket.button>

            <x-agromarket.button
                variant="primary"
                icon="fas fa-check"
                type="submit"
            >
                Completar Registro
            </x-agromarket.button>
        </div>
    </form>

    @push('scripts')
    <script>
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error en el formulario',
                html: @json(implode('<br>', $errors->all())),
                confirmButtonColor: '#4A7C59'
            });
        @endif

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Excelente',
                text: @json(session('success')),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error')),
                confirmButtonColor: '#4A7C59'
            });
        @endif
    </script>
    @endpush

    @push('styles')
    <style>
        div[style*="grid-template-columns: 1fr 1fr;"] {
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)) !important;
        }
    </style>
    @endpush
</x-app-layout>
