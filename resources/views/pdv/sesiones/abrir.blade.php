<x-app-layout>
    @section('title', 'Abrir Caja')

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('pdv.dashboard') }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h4 class="fw-bold mb-0"><i class="bi bi-unlock me-2"></i>Abrir Caja</h4>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-cash-stack fs-3 me-3" style="color: var(--miracle-pink);"></i>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $caja->nombre }}</h5>
                                <small class="text-muted">{{ $caja->ubicacion->nombre ?? '' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pdv.sesiones.abrir') }}" method="POST">
                            @csrf
                            <input type="hidden" name="caja_id" value="{{ $caja->id }}">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Responsable</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Fecha y Hora</label>
                                <input type="text" class="form-control" value="{{ now()->format('d/m/Y h:i A') }}" disabled>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Monto Inicial en Efectivo (Base) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="monto_apertura" class="form-control @error('monto_apertura') is-invalid @enderror"
                                           step="0.01" min="0" value="{{ old('monto_apertura', 0) }}" required autofocus>
                                </div>
                                @error('monto_apertura') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                <small class="text-muted">Ingrese el monto de efectivo con el que inicia el turno</small>
                            </div>

                            <button type="submit" class="btn btn-lg w-100 text-white" style="background: var(--miracle-pink);">
                                <i class="bi bi-unlock me-2"></i>Abrir Caja
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
