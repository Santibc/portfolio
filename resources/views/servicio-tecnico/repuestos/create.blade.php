<x-app-layout>
    <x-slot name="header">{{ isset($repuesto) ? 'Editar Repuesto' : 'Nuevo Repuesto' }}</x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row mb-4">
                <div class="col-12">
                    <p class="text-muted">{{ isset($repuesto) ? 'Actualizar información del repuesto' : 'Registrar nuevo repuesto en inventario' }}</p>
                </div>
            </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ isset($repuesto) ? route('st.repuestos.update', $repuesto->id) : route('st.repuestos.store') }}"
                  method="POST" id="repuestoForm">
                @csrf
                @if(isset($repuesto))
                    @method('PUT')
                @endif

                {{-- Información Básica --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información del Repuesto</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text" name="codigo" id="codigo"
                                       class="form-control @error('codigo') is-invalid @enderror"
                                       value="{{ old('codigo', $repuesto->codigo ?? '') }}" required
                                       placeholder="Ej: REP-001">
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="nombre" class="form-label">Nombre del Repuesto <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre"
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre', $repuesto->nombre ?? '') }}" required
                                       placeholder="Ej: Lente varifocal 2.8-12mm">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                                <select name="categoria" id="categoria"
                                        class="form-select @error('categoria') is-invalid @enderror" required>
                                    <option value="">Seleccione una categoría</option>
                                    <option value="Lente" {{ old('categoria', $repuesto->categoria ?? '') == 'Lente' ? 'selected' : '' }}>Lente</option>
                                    <option value="Sensor" {{ old('categoria', $repuesto->categoria ?? '') == 'Sensor' ? 'selected' : '' }}>Sensor</option>
                                    <option value="Fuente de poder" {{ old('categoria', $repuesto->categoria ?? '') == 'Fuente de poder' ? 'selected' : '' }}>Fuente de poder</option>
                                    <option value="Cable" {{ old('categoria', $repuesto->categoria ?? '') == 'Cable' ? 'selected' : '' }}>Cable</option>
                                    <option value="Conector" {{ old('categoria', $repuesto->categoria ?? '') == 'Conector' ? 'selected' : '' }}>Conector</option>
                                    <option value="Disco duro" {{ old('categoria', $repuesto->categoria ?? '') == 'Disco duro' ? 'selected' : '' }}>Disco duro</option>
                                    <option value="Carcasa" {{ old('categoria', $repuesto->categoria ?? '') == 'Carcasa' ? 'selected' : '' }}>Carcasa</option>
                                    <option value="LED IR" {{ old('categoria', $repuesto->categoria ?? '') == 'LED IR' ? 'selected' : '' }}>LED IR</option>
                                    <option value="Módulo de red" {{ old('categoria', $repuesto->categoria ?? '') == 'Módulo de red' ? 'selected' : '' }}>Módulo de red</option>
                                    <option value="Placa electrónica" {{ old('categoria', $repuesto->categoria ?? '') == 'Placa electrónica' ? 'selected' : '' }}>Placa electrónica</option>
                                    <option value="Otro" {{ old('categoria', $repuesto->categoria ?? '') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('categoria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="marca_compatible" class="form-label">Marca Compatible</label>
                                <input type="text" name="marca_compatible" id="marca_compatible"
                                       class="form-control @error('marca_compatible') is-invalid @enderror"
                                       value="{{ old('marca_compatible', $repuesto->marca_compatible ?? '') }}"
                                       placeholder="Ej: Hikvision, Dahua, Universal">
                                @error('marca_compatible')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="modelo_compatible" class="form-label">Modelo Compatible</label>
                                <input type="text" name="modelo_compatible" id="modelo_compatible"
                                       class="form-control @error('modelo_compatible') is-invalid @enderror"
                                       value="{{ old('modelo_compatible', $repuesto->modelo_compatible ?? '') }}"
                                       placeholder="Ej: DS-2CD2043, Todos">
                                @error('modelo_compatible')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" rows="3"
                                          class="form-control @error('descripcion') is-invalid @enderror"
                                          placeholder="Descripción detallada del repuesto, características, compatibilidad, etc.">{{ old('descripcion', $repuesto->descripcion ?? '') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Inventario --}}
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-boxes me-2"></i>Control de Inventario</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="stock_actual" class="form-label">Stock Actual <span class="text-danger">*</span></label>
                                <input type="number" name="stock_actual" id="stock_actual"
                                       class="form-control @error('stock_actual') is-invalid @enderror"
                                       value="{{ old('stock_actual', $repuesto->stock_actual ?? 0) }}" min="0" required>
                                @error('stock_actual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="stock_minimo" class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                                <input type="number" name="stock_minimo" id="stock_minimo"
                                       class="form-control @error('stock_minimo') is-invalid @enderror"
                                       value="{{ old('stock_minimo', $repuesto->stock_minimo ?? 5) }}" min="0" required>
                                <small class="text-muted">Se generará alerta cuando el stock sea menor o igual a este valor</small>
                                @error('stock_minimo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12" id="stock_alert" style="display: none;">
                                <div class="alert alert-warning" role="alert">
                                    <i class="bi bi-exclamation-triangle"></i> <strong>Advertencia:</strong> El stock actual está por debajo del stock mínimo.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Precios --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Precios</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="precio_compra" class="form-label">Precio de Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio_compra" id="precio_compra"
                                           class="form-control @error('precio_compra') is-invalid @enderror"
                                           value="{{ old('precio_compra', $repuesto->precio_compra ?? '') }}"
                                           step="0.01" min="0" placeholder="0.00">
                                    @error('precio_compra')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="precio_venta" class="form-label">Precio de Venta</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio_venta" id="precio_venta"
                                           class="form-control @error('precio_venta') is-invalid @enderror"
                                           value="{{ old('precio_venta', $repuesto->precio_venta ?? '') }}"
                                           step="0.01" min="0" placeholder="0.00">
                                    @error('precio_venta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12" id="margen_alert" style="display: none;">
                                <div class="alert alert-info" role="alert">
                                    <i class="bi bi-info-circle"></i> <strong>Margen de ganancia:</strong> <span id="margen_valor"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Proveedor --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-building me-2"></i>Proveedor</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="proveedor" class="form-label">Nombre del Proveedor</label>
                                <input type="text" name="proveedor" id="proveedor"
                                       class="form-control @error('proveedor') is-invalid @enderror"
                                       value="{{ old('proveedor', $repuesto->proveedor ?? '') }}"
                                       placeholder="Nombre de la empresa proveedora">
                                @error('proveedor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Estado --}}
                @if(isset($repuesto))
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-toggle-on me-2"></i>Estado</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="activo" class="form-label">Estado del Repuesto</label>
                                <select name="activo" id="activo" class="form-select @error('activo') is-invalid @enderror">
                                    <option value="1" {{ old('activo', $repuesto->activo ?? true) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('activo', $repuesto->activo ?? true) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('activo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Botones de Acción --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('st.repuestos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> {{ isset($repuesto) ? 'Actualizar' : 'Registrar' }} Repuesto
                    </button>
                </div>
            </form>
            </div>
        </div>
    </div>

@push('scripts')
<script>
$(document).ready(function() {
    // Verificar stock bajo
    function verificarStock() {
        const stockActual = parseInt($('#stock_actual').val()) || 0;
        const stockMinimo = parseInt($('#stock_minimo').val()) || 0;

        if (stockActual <= stockMinimo) {
            $('#stock_alert').slideDown();
        } else {
            $('#stock_alert').slideUp();
        }
    }

    // Calcular margen de ganancia
    function calcularMargen() {
        const precioCompra = parseFloat($('#precio_compra').val()) || 0;
        const precioVenta = parseFloat($('#precio_venta').val()) || 0;

        if (precioCompra > 0 && precioVenta > 0) {
            const margen = ((precioVenta - precioCompra) / precioCompra * 100).toFixed(2);
            const ganancia = precioVenta - precioCompra;

            $('#margen_valor').html(`${margen}% ($${ganancia.toFixed(2)})`);
            $('#margen_alert').slideDown();
        } else {
            $('#margen_alert').slideUp();
        }
    }

    $('#stock_actual, #stock_minimo').on('input', verificarStock);
    $('#precio_compra, #precio_venta').on('input', calcularMargen);

    // Inicializar
    verificarStock();
    calcularMargen();

    // Validación del formulario
    $('#repuestoForm').on('submit', function(e) {
        let valid = true;
        let mensaje = '';

        if ($('#codigo').val().trim() === '') {
            valid = false;
            mensaje += '- El código es obligatorio\n';
        }

        if ($('#nombre').val().trim() === '') {
            valid = false;
            mensaje += '- El nombre del repuesto es obligatorio\n';
        }

        if ($('#categoria').val() === '') {
            valid = false;
            mensaje += '- Debe seleccionar una categoría\n';
        }

        if (!valid) {
            e.preventDefault();
            Swal.fire({
                title: 'Campos Requeridos',
                text: mensaje,
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
        }
    });
});
</script>
@endpush
</x-app-layout>
