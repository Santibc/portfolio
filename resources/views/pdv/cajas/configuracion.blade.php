<x-app-layout>
    @section('title', 'Configuracion PdV')

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('pdv.cajas.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h4 class="fw-bold mb-0"><i class="bi bi-gear me-2"></i>Configuracion General PdV</h4>
                </div>

                <div class="row">
                    {{-- Columna izquierda: Parametros generales --}}
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="fw-bold mb-0"><i class="bi bi-sliders me-2"></i>Parametros Generales</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('pdv.cajas.configuracion.guardar') }}" method="POST">
                                    @csrf
                                    @foreach($configuraciones as $config)
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">{{ $config->descripcion ?? $config->clave }}</label>
                                            @if(in_array($config->valor, ['true', 'false']))
                                                <select name="{{ $config->clave }}" class="form-select">
                                                    <option value="true" {{ $config->valor === 'true' ? 'selected' : '' }}>Si</option>
                                                    <option value="false" {{ $config->valor === 'false' ? 'selected' : '' }}>No</option>
                                                </select>
                                            @else
                                                <input type="text" name="{{ $config->clave }}" class="form-control" value="{{ $config->valor }}">
                                            @endif
                                            <small class="text-muted">Clave: {{ $config->clave }}</small>
                                        </div>
                                    @endforeach

                                    <div class="mt-4 d-flex justify-content-end">
                                        <button type="submit" class="btn text-white" style="background: var(--miracle-pink);">
                                            <i class="bi bi-check-lg me-1"></i>Guardar Configuracion
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Columna derecha: Gestion de PINes --}}
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2"></i>PIN de Autorizacion</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    El PIN de 4 digitos se usa para autorizar descuentos, cambios de precio y otras operaciones sensibles en el punto de venta.
                                </p>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Usuario</th>
                                                <th class="text-center">PIN</th>
                                                <th class="text-center" style="width: 140px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($usuariosPin as $usuario)
                                            <tr id="pin-row-{{ $usuario->id }}">
                                                <td>
                                                    <div class="fw-semibold">{{ $usuario->name }}</div>
                                                    <small class="text-muted">{{ $usuario->email }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($usuario->pin_pdv)
                                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Configurado</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Sin PIN</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="modalPin({{ $usuario->id }}, '{{ $usuario->name }}')" title="Asignar/Cambiar PIN">
                                                        <i class="bi bi-key"></i>
                                                    </button>
                                                    @if($usuario->pin_pdv)
                                                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarPin({{ $usuario->id }}, '{{ $usuario->name }}')" title="Eliminar PIN">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">
                                                    No hay usuarios con rol admin o cajero principal
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal PIN --}}
    <div class="modal fade" id="modalPin" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold"><i class="bi bi-key me-2"></i>Asignar PIN</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="text-muted mb-3" id="pinUserName"></p>
                    <input type="hidden" id="pinUserId">
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <input type="password" class="form-control text-center fw-bold" id="pinDigit1" maxlength="1" style="width: 50px; height: 50px; font-size: 1.5rem;" inputmode="numeric" pattern="[0-9]">
                        <input type="password" class="form-control text-center fw-bold" id="pinDigit2" maxlength="1" style="width: 50px; height: 50px; font-size: 1.5rem;" inputmode="numeric" pattern="[0-9]">
                        <input type="password" class="form-control text-center fw-bold" id="pinDigit3" maxlength="1" style="width: 50px; height: 50px; font-size: 1.5rem;" inputmode="numeric" pattern="[0-9]">
                        <input type="password" class="form-control text-center fw-bold" id="pinDigit4" maxlength="1" style="width: 50px; height: 50px; font-size: 1.5rem;" inputmode="numeric" pattern="[0-9]">
                    </div>
                    <small class="text-muted">Ingrese 4 digitos numericos</small>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm text-white" style="background: var(--miracle-pink);" onclick="guardarPin()">
                        <i class="bi bi-check-lg me-1"></i>Guardar PIN
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-advance entre digitos del PIN
        document.querySelectorAll('[id^="pinDigit"]').forEach((input, idx, inputs) => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 1 && idx < inputs.length - 1) {
                    inputs[idx + 1].focus();
                }
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && idx > 0) {
                    inputs[idx - 1].focus();
                }
            });
        });

        function modalPin(userId, userName) {
            document.getElementById('pinUserId').value = userId;
            document.getElementById('pinUserName').textContent = userName;
            document.querySelectorAll('[id^="pinDigit"]').forEach(i => i.value = '');
            new bootstrap.Modal(document.getElementById('modalPin')).show();
            setTimeout(() => document.getElementById('pinDigit1').focus(), 300);
        }

        function guardarPin() {
            const pin = ['pinDigit1','pinDigit2','pinDigit3','pinDigit4']
                .map(id => document.getElementById(id).value).join('');

            if (pin.length !== 4 || !/^\d{4}$/.test(pin)) {
                Swal.fire('Error', 'El PIN debe ser de 4 digitos numericos', 'error');
                return;
            }

            const userId = document.getElementById('pinUserId').value;

            fetch('{{ route("pdv.cajas.pin.guardar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ user_id: userId, pin: pin })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalPin')).hide();
                    Swal.fire('Listo', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Error al guardar', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Error de conexion', 'error'));
        }

        function eliminarPin(userId, userName) {
            Swal.fire({
                title: 'Eliminar PIN',
                text: 'Eliminar el PIN de ' + userName + '? Ya no podra autorizar operaciones.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Si, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('{{ route("pdv.cajas.pin.eliminar") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ user_id: userId })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Eliminado', data.message, 'success').then(() => location.reload());
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Error de conexion', 'error'));
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
