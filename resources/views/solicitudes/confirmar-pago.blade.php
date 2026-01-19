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
              <small class="text-muted d-block">Monto Total</small>
              <h4 class="text-primary mb-0">$ {{ number_format($solicitud->monto_total, 0, ',', '.') }}</h4>
            </div>
            <div class="col-md-4 text-center border-end">
              <small class="text-muted d-block">Ya Pagado</small>
              <h4 class="text-success mb-0">$ {{ number_format($solicitud->monto_pagado, 0, ',', '.') }}</h4>
            </div>
            <div class="col-md-4 text-center">
              <small class="text-muted d-block">Saldo Pendiente</small>
              <h4 class="text-danger mb-0">$ {{ number_format($solicitud->saldo_pendiente, 0, ',', '.') }}</h4>
            </div>
          </div>
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
              <div class="col-md-6">
                <label for="monto" class="form-label">Monto a Registrar <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="number" class="form-control" id="monto" name="monto"
                         value="{{ $solicitud->saldo_pendiente }}"
                         max="{{ $solicitud->saldo_pendiente }}"
                         min="0.01" step="0.01" required>
                </div>
                <small class="text-muted">Máximo: $ {{ number_format($solicitud->saldo_pendiente, 0, ',', '.') }}</small>
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

      {{-- Historial de pagos (si hay pago parcial) --}}
      @if($solicitud->monto_pagado > 0)
      <div class="bg-white shadow-sm rounded-lg overflow-hidden mt-4">
        <div class="p-4 border-bottom">
          <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Información de Pago Anterior</h5>
        </div>
        <div class="p-4">
          <div class="row">
            <div class="col-md-6">
              <p class="mb-1"><strong>Método:</strong> {{ $metodosPago[$solicitud->metodo_pago] ?? '-' }}</p>
              <p class="mb-1"><strong>Monto pagado:</strong> $ {{ number_format($solicitud->monto_pagado, 0, ',', '.') }}</p>
            </div>
            <div class="col-md-6">
              @if($solicitud->verificadoPor)
                <p class="mb-1"><strong>Verificado por:</strong> {{ $solicitud->verificadoPor->name }}</p>
              @endif
              @if($solicitud->verificado_en)
                <p class="mb-1"><strong>Fecha:</strong> {{ $solicitud->verificado_en->format('d/m/Y H:i') }}</p>
              @endif
            </div>
          </div>
          @if($solicitud->comprobante_pago)
            <a href="{{ route('pagos.comprobante', $solicitud) }}" class="btn btn-sm btn-outline-primary mt-2" target="_blank">
              <i class="bi bi-file-earmark-pdf me-1"></i> Ver Comprobante
            </a>
          @endif
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
