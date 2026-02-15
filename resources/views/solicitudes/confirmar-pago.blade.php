<x-app-layout>
  <x-slot name="header">
    Confirmar Pago - {{ $solicitud->numero_solicitud }}
  </x-slot>

  <div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      {{-- Información de la cotización --}}
      <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
        <div class="bg-gradient-primary text-white p-4">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h5 class="mb-1">Cotización {{ $solicitud->numero_solicitud }}</h5>
              <p class="mb-0 opacity-75">{{ $solicitud->cliente->nombre_contacto }}</p>
            </div>
            <div class="col-md-4 text-end">
              <span class="badge bg-{{ $solicitud->color_estado_pago }} fs-6">
                {{ $solicitud->etiqueta_estado_pago }}
              </span>
            </div>
          </div>
        </div>

        <div class="p-4">
          <div class="row">
            <div class="col-md-4 text-center border-end">
              <small class="text-muted d-block">Monto Total (con IVA)</small>
              <h4 class="text-primary mb-0">$ {{ number_format($solicitud->monto_total_con_iva, 0, ',', '.') }}</h4>
              @if($solicitud->valor_iva > 0)
                <small class="text-muted">(IVA {{ number_format($solicitud->porcentaje_iva, 0) }}%: ${{ number_format($solicitud->valor_iva, 0, ',', '.') }})</small>
              @endif
            </div>
            <div class="col-md-4 text-center border-end">
              <small class="text-muted d-block">Pagado (aprobado)</small>
              <h4 class="text-success mb-0">$ {{ number_format($solicitud->monto_pagado, 0, ',', '.') }}</h4>
            </div>
            <div class="col-md-4 text-center">
              <small class="text-muted d-block">Saldo Pendiente</small>
              <h4 class="text-danger mb-0">$ {{ number_format($solicitud->saldo_pendiente, 0, ',', '.') }}</h4>
              @php
                $montoEnEspera = $solicitud->pagosPendientes()->sum('monto');
              @endphp
              @if($montoEnEspera > 0)
                <small class="text-warning d-block mt-1">
                  <i class="bi bi-clock me-1"></i>
                  $ {{ number_format($montoEnEspera, 0, ',', '.') }} en espera de aprobación
                </small>
              @endif
            </div>
          </div>

          @if($solicitud->forma_pago_factura || $solicitud->fecha_vencimiento)
          <div class="row mt-3 pt-3 border-top">
            @if($solicitud->forma_pago_factura)
            <div class="col-md-6 text-center">
              <small class="text-muted d-block">Forma de Pago</small>
              <strong>{{ $solicitud->forma_pago_factura }}</strong>
            </div>
            @endif
            @if($solicitud->fecha_vencimiento)
            <div class="col-md-6 text-center">
              <small class="text-muted d-block">Fecha de Vencimiento</small>
              <strong>{{ $solicitud->fecha_vencimiento->format('d/m/Y') }}</strong>
            </div>
            @endif
          </div>
          @endif
        </div>
      </div>

      {{-- Formulario de pago --}}
      <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-4 border-bottom">
          <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Registrar Pago</h5>
        </div>

        <form id="formPago" enctype="multipart/form-data">
          @csrf
          <div class="p-4">
            <div class="row g-3">
              {{-- Monto --}}
              @php
                $montoPendienteAprobacion = $solicitud->pagosPendientes()->sum('monto');
                $maxPermitido = max(0.01, $solicitud->saldo_pendiente - $montoPendienteAprobacion);
              @endphp
              <div class="col-md-6">
                <label for="monto" class="form-label">Monto a Registrar <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="number" class="form-control" id="monto" name="monto"
                         value="{{ $maxPermitido }}"
                         max="{{ $maxPermitido }}"
                         min="0.01" step="0.01" required>
                </div>
                <small class="text-muted">Máximo disponible: $ {{ number_format($maxPermitido, 0, ',', '.') }}</small>
              </div>

              {{-- Método de pago --}}
              <div class="col-md-6">
                <label for="metodo_pago" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                  <option value="">Seleccione...</option>
                  @foreach($metodosPago as $valor => $etiqueta)
                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                  @endforeach
                </select>
              </div>

              {{-- Comprobante --}}
              <div class="col-12">
                <label for="comprobante" class="form-label">Comprobante de Pago</label>
                <input type="file" class="form-control" id="comprobante" name="comprobante"
                       accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Formatos: PDF, JPG, PNG. Máximo 5MB.</small>
              </div>

              {{-- Notas --}}
              <div class="col-12">
                <label for="notas_pago" class="form-label">Notas / Observaciones</label>
                <textarea class="form-control" id="notas_pago" name="notas_pago" rows="3"
                          placeholder="Información adicional sobre el pago..."></textarea>
              </div>
            </div>
          </div>

          <div class="p-4 bg-light border-top">
            <div class="d-flex justify-content-between">
              <a href="{{ route('solicitudes') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver
              </a>
              <button type="submit" class="btn btn-success" id="btnGuardar">
                <i class="bi bi-check-circle me-1"></i> Registrar Pago
              </button>
            </div>
          </div>
        </form>
      </div>

      {{-- Historial de pagos --}}
      @if($solicitud->pagos->count() > 0)
      <div class="bg-white shadow-sm rounded-lg overflow-hidden mt-4">
        <div class="p-4 border-bottom">
          <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Pagos ({{ $solicitud->pagos->count() }})</h5>
        </div>
        <div class="p-4">
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Fecha</th>
                  <th>Monto</th>
                  <th>Método</th>
                  <th>Registrado por</th>
                  <th>Estado</th>
                  <th>Notas</th>
                  <th>Comprobante</th>
                </tr>
              </thead>
              <tbody>
                @foreach($solicitud->pagos->sortBy('created_at') as $index => $pago)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                  <td><strong>$ {{ number_format($pago->monto, 0, ',', '.') }}</strong></td>
                  <td>{{ $metodosPago[$pago->metodo_pago] ?? $pago->metodo_pago }}</td>
                  <td>{{ $pago->registradoPor?->name ?? '-' }}</td>
                  <td>
                    <span class="badge bg-{{ $pago->color_estado }}">{{ $pago->etiqueta_estado }}</span>
                    @if($pago->estaAprobado() && $pago->aprobadoPor)
                      <br><small class="text-muted">por {{ $pago->aprobadoPor->name }}</small>
                    @endif
                    @if($pago->estaRechazado() && $pago->aprobadoPor)
                      <br><small class="text-muted">por {{ $pago->aprobadoPor->name }}</small>
                    @endif
                  </td>
                  <td>{{ $pago->notas ?? '-' }}</td>
                  <td>
                    @if($pago->comprobante)
                      <a href="{{ url('/solicitudes/' . $solicitud->id . '/pagos/' . $pago->id . '/comprobante') }}"
                         class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="bi bi-download me-1"></i> Descargar
                      </a>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>

  @push('scripts')
  <script>
    document.getElementById('formPago').addEventListener('submit', function(e) {
      e.preventDefault();

      const btn = document.getElementById('btnGuardar');
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';

      const formData = new FormData(this);

      fetch('{{ route("pagos.store", $solicitud) }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Pago Registrado',
            text: data.mensaje,
            confirmButtonColor: '#BCA9F5'
          }).then(() => {
            window.location.href = '{{ route("solicitudes") }}';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.mensaje,
            confirmButtonColor: '#BCA9F5'
          });
          btn.disabled = false;
          btn.innerHTML = originalText;
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Ocurrió un error al procesar el pago',
          confirmButtonColor: '#BCA9F5'
        });
        btn.disabled = false;
        btn.innerHTML = originalText;
      });
    });
  </script>
  @endpush
</x-app-layout>
