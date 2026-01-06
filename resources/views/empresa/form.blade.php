<x-app-layout>
  <x-slot name="header">
    {{ $empresa->exists ? 'Editar Mi Empresa' : 'Crear Mi Empresa' }}
  </x-slot>

  <div class="container py-4">
    {{-- Mostrar errores de validación general --}}
    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <strong>Por favor corrija los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- Mensajes de éxito/error --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <form method="POST" action="{{ route('empresa.guardar') }}" enctype="multipart/form-data" id="empresaForm">
      @csrf
      
      {{-- Información Básica --}}
      <div class="card shadow mb-4">
        <div class="card-header">
          <h5 class="mb-0">Información Básica</h5>
        </div>
        <div class="card-body">
          <div class="row">
            {{-- Nombre --}}
            <div class="col-md-12 mb-3">
              <label class="form-label">Nombre de la Empresa <span class="text-danger">*</span></label>
              <input name="nombre" type="text"
                     class="form-control @error('nombre') is-invalid @enderror"
                     value="{{ old('nombre', $empresa->nombre) }}"
                     placeholder="Ej: Mi Tienda Online"
                     required>
              @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Descripción --}}
            <div class="col-md-12 mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" rows="3"
                        class="form-control @error('descripcion') is-invalid @enderror"
                        placeholder="Describe tu empresa o negocio..."
                        maxlength="500">{{ old('descripcion', $empresa->descripcion) }}</textarea>
              @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="text-muted">Máximo 500 caracteres</small>
            </div>
          </div>
        </div>
      </div>

      {{-- Información de Contacto --}}
      <div class="card shadow mb-4">
        <div class="card-header">
          <h5 class="mb-0">Información de Contacto</h5>
        </div>
        <div class="card-body">
          <div class="row">
            {{-- Email --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">Correo Electrónico</label>
              <input name="email" type="email"
                     class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email', $empresa->email) }}"
                     placeholder="contacto@miempresa.com">
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Teléfono --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">Teléfono</label>
              <input name="telefono" type="text"
                     class="form-control @error('telefono') is-invalid @enderror"
                     value="{{ old('telefono', $empresa->telefono) }}"
                     placeholder="+57 300 123 4567">
              @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Dirección --}}
            <div class="col-md-12 mb-3">
              <label class="form-label">Dirección</label>
              <input name="direccion" type="text"
                     class="form-control @error('direccion') is-invalid @enderror"
                     value="{{ old('direccion', $empresa->direccion) }}"
                     placeholder="Calle 123 #45-67, Bogotá">
              @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- WhatsApp --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">WhatsApp</label>
              <input name="whatsapp" type="text"
                     class="form-control @error('whatsapp') is-invalid @enderror"
                     value="{{ old('whatsapp', $empresa->whatsapp) }}"
                     placeholder="+57 300 123 4567">
              @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="text-muted">Número con código de país</small>
            </div>
          </div>
        </div>
      </div>

      {{-- Redes Sociales --}}
      <div class="card shadow mb-4">
        <div class="card-header">
          <h5 class="mb-0">Redes Sociales</h5>
        </div>
        <div class="card-body">
          <div class="row">
            {{-- Facebook --}}
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="bi bi-facebook text-primary"></i> Facebook</label>
              <input name="facebook_url" type="url"
                     class="form-control @error('facebook_url') is-invalid @enderror"
                     value="{{ old('facebook_url', $empresa->facebook_url) }}"
                     placeholder="https://facebook.com/miempresa">
              @error('facebook_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Instagram --}}
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="bi bi-instagram text-danger"></i> Instagram</label>
              <input name="instagram_url" type="url"
                     class="form-control @error('instagram_url') is-invalid @enderror"
                     value="{{ old('instagram_url', $empresa->instagram_url) }}"
                     placeholder="https://instagram.com/miempresa">
              @error('instagram_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- TikTok --}}
            <div class="col-md-6 mb-3">
              <label class="form-label"><i class="bi bi-tiktok"></i> TikTok</label>
              <input name="tiktok_url" type="url"
                     class="form-control @error('tiktok_url') is-invalid @enderror"
                     value="{{ old('tiktok_url', $empresa->tiktok_url) }}"
                     placeholder="https://tiktok.com/@miempresa">
              @error('tiktok_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Imágenes --}}
      <div class="card shadow mb-4">
        <div class="card-header">
          <h5 class="mb-0">Imágenes de la Empresa</h5>
        </div>
        <div class="card-body">
          <div class="row">
            {{-- Logo --}}
            <div class="col-md-6 mb-4">
              <label class="form-label">Logo de la Empresa</label>
              <input type="file" name="logo" 
                     class="form-control @error('logo') is-invalid @enderror"
                     accept="image/jpeg,image/jpg,image/png,image/webp">
              @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="text-muted d-block mt-1">
                Formatos: JPG, PNG, WebP. Tamaño máximo: 2MB. Recomendado: 500x500px
              </small>
              
              @if($empresa->logo)
                <div class="mt-3">
                  <img src="{{ $empresa->logo_url }}" 
                       alt="Logo actual" 
                       class="img-thumbnail"
                       style="max-width: 150px;">
                  <p class="text-muted mt-1">Logo actual</p>
                </div>
              @endif
            </div>

            {{-- Imagen de Portada --}}
            <div class="col-md-6 mb-4">
              <label class="form-label">Imagen de Portada</label>
              <input type="file" name="imagen_portada" 
                     class="form-control @error('imagen_portada') is-invalid @enderror"
                     accept="image/jpeg,image/jpg,image/png,image/webp">
              @error('imagen_portada') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="text-muted d-block mt-1">
                Formatos: JPG, PNG, WebP. Tamaño máximo: 4MB. Recomendado: 1920x400px
              </small>
              
              @if($empresa->imagen_portada)
                <div class="mt-3">
                  <img src="{{ $empresa->imagen_portada_url }}" 
                       alt="Portada actual" 
                       class="img-thumbnail"
                       style="max-width: 300px;">
                  <p class="text-muted mt-1">Portada actual</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Marketing y Analytics --}}
      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Marketing y Analytics</h5>
        </div>
        <div class="card-body">
          <p class="text-muted mb-4">
            Configura los códigos de seguimiento para medir el rendimiento de tu tienda y crear campañas de remarketing.
          </p>

          <div class="row">
            {{-- Google Analytics 4 --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">
                <i class="bi bi-google text-danger me-1"></i> Google Analytics 4 (GA4)
              </label>
              <input name="ga4_measurement_id" type="text"
                     class="form-control @error('ga4_measurement_id') is-invalid @enderror"
                     value="{{ old('ga4_measurement_id', $empresa->ga4_measurement_id) }}"
                     placeholder="G-XXXXXXXXXX">
              @error('ga4_measurement_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="text-muted">ID de medición de Google Analytics 4 (ej: G-ABC123XYZ)</small>
            </div>

            {{-- Google Tag Manager --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">
                <i class="bi bi-tag text-warning me-1"></i> Google Tag Manager
              </label>
              <input name="gtm_container_id" type="text"
                     class="form-control @error('gtm_container_id') is-invalid @enderror"
                     value="{{ old('gtm_container_id', $empresa->gtm_container_id) }}"
                     placeholder="GTM-XXXXXXX">
              @error('gtm_container_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="text-muted">ID de contenedor de GTM (ej: GTM-ABC123)</small>
            </div>

            {{-- Facebook/Meta Pixel --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">
                <i class="bi bi-facebook text-primary me-1"></i> Meta/Facebook Pixel
              </label>
              <input name="fb_pixel_id" type="text"
                     class="form-control @error('fb_pixel_id') is-invalid @enderror"
                     value="{{ old('fb_pixel_id', $empresa->fb_pixel_id) }}"
                     placeholder="123456789012345">
              @error('fb_pixel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="text-muted">ID del Pixel de Facebook/Meta (ej: 123456789012345)</small>
            </div>

            {{-- Scripts Personalizados --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">
                <i class="bi bi-code-slash text-success me-1"></i> Scripts Adicionales (Head)
              </label>
              <textarea name="custom_scripts_head" rows="3"
                        class="form-control @error('custom_scripts_head') is-invalid @enderror"
                        placeholder="<!-- Otros scripts de tracking -->"
              >{{ old('custom_scripts_head', $empresa->custom_scripts_head) }}</textarea>
              @error('custom_scripts_head') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="text-muted">Scripts adicionales para el &lt;head&gt; (Google Ads, TikTok Pixel, etc.)</small>
            </div>
          </div>

          {{-- Información adicional --}}
          <div class="alert alert-info mt-3 mb-0">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Nota:</strong> Los scripts de tracking solo se cargarán en producción.
            Para obtener tus códigos:
            <ul class="mb-0 mt-2">
              <li><a href="https://analytics.google.com/" target="_blank" rel="noopener">Google Analytics 4</a> - Administración → Flujos de datos → ID de medición</li>
              <li><a href="https://tagmanager.google.com/" target="_blank" rel="noopener">Google Tag Manager</a> - ID del contenedor</li>
              <li><a href="https://business.facebook.com/events_manager" target="_blank" rel="noopener">Meta Business Suite</a> - Administrador de eventos → Orígenes de datos</li>
            </ul>
          </div>
        </div>
      </div>

      {{-- Configuración de Pagos (Transbank/WebPay) --}}
      <div class="card shadow mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-credit-card-2-front me-2"></i>Pasarela de Pagos (WebPay)</h5>
        </div>
        <div class="card-body">
          @php
            $configPasarela = \App\Models\ConfiguracionPasarela::obtenerConfiguracionActiva('transbank');
          @endphp

          <p class="text-muted mb-4">
            Configura las credenciales de WebPay Plus para procesar pagos con tarjetas de crédito y débito.
          </p>

          <div class="row">
            {{-- Modo de operación --}}
            <div class="col-md-12 mb-3">
              <div class="form-check form-switch">
                <input type="hidden" name="transbank_modo_prueba" value="1">
                <input type="checkbox"
                       name="transbank_modo_prueba"
                       id="transbankModoPrueba"
                       class="form-check-input"
                       value="0"
                       {{ old('transbank_modo_prueba', $configPasarela->modo_prueba ?? true) ? '' : 'checked' }}>
                <label class="form-check-label" for="transbankModoPrueba">
                  <strong>Modo Producción</strong>
                  <span class="text-muted ms-2">(Desactivado = Modo Integración/Pruebas)</span>
                </label>
              </div>
              <small class="text-muted">
                En modo Integración, se usan las credenciales de prueba de Transbank automáticamente.
              </small>
            </div>

            {{-- Código de Comercio --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">
                <i class="bi bi-shop me-1"></i> Código de Comercio
              </label>
              <input name="transbank_commerce_code" type="text"
                     class="form-control @error('transbank_commerce_code') is-invalid @enderror"
                     value="{{ old('transbank_commerce_code', $configPasarela->configuracion_adicional['commerce_code'] ?? '') }}"
                     placeholder="597XXXXXXXXX">
              @error('transbank_commerce_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="text-muted">Código de comercio asignado por Transbank (solo para producción)</small>
            </div>

            {{-- API Key (Private Key) --}}
            <div class="col-md-6 mb-3">
              <label class="form-label">
                <i class="bi bi-key me-1"></i> API Key Secret
              </label>
              <input name="transbank_api_key" type="password"
                     class="form-control @error('transbank_api_key') is-invalid @enderror"
                     value=""
                     placeholder="{{ $configPasarela && $configPasarela->private_key ? '••••••••••••••••' : 'API Key de Transbank' }}">
              @error('transbank_api_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
              <small class="text-muted">Deja vacío para mantener la clave actual (solo para producción)</small>
            </div>
          </div>

          {{-- Información adicional --}}
          <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-shield-lock me-2"></i>
            <strong>Seguridad:</strong> Las credenciales de producción solo deben usarse cuando la tienda esté lista para vender.
            <ul class="mb-0 mt-2">
              <li><strong>Modo Integración:</strong> Usa tarjetas de prueba (4051 8856 0044 6623 / CVV: 123 / Exp: cualquier fecha futura)</li>
              <li><strong>Modo Producción:</strong> Requiere contrato con Transbank y credenciales reales</li>
              <li><a href="https://www.transbankdevelopers.cl/" target="_blank" rel="noopener">Documentación de Transbank Developers</a></li>
            </ul>
          </div>
        </div>
      </div>

      {{-- Horario de Atención --}}
      <div class="card shadow mb-4">
        <div class="card-header">
          <h5 class="mb-0">Horario de Atención</h5>
        </div>
        <div class="card-body">
          @php
            $dias = [
              'lunes' => 'Lunes',
              'martes' => 'Martes',
              'miercoles' => 'Miércoles',
              'jueves' => 'Jueves',
              'viernes' => 'Viernes',
              'sabado' => 'Sábado',
              'domingo' => 'Domingo'
            ];
            
            $horarioDefault = [
              'apertura' => '09:00',
              'cierre' => '18:00',
              'cerrado' => false
            ];
          @endphp
          
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Día</th>
                  <th>Hora Apertura</th>
                  <th>Hora Cierre</th>
                  <th>Cerrado</th>
                </tr>
              </thead>
              <tbody>
                @foreach($dias as $key => $dia)
                  @php
                    $horario = $empresa->horario_atencion[$key] ?? $horarioDefault;
                  @endphp
                  <tr>
                    <td>{{ $dia }}</td>
                    <td>
                      <input type="time" 
                             name="horario_atencion[{{ $key }}][apertura]" 
                             class="form-control form-control-sm horario-input"
                             value="{{ old("horario_atencion.$key.apertura", $horario['apertura'] ?? '09:00') }}"
                             data-dia="{{ $key }}">
                    </td>
                    <td>
                      <input type="time" 
                             name="horario_atencion[{{ $key }}][cierre]" 
                             class="form-control form-control-sm horario-input"
                             value="{{ old("horario_atencion.$key.cierre", $horario['cierre'] ?? '18:00') }}"
                             data-dia="{{ $key }}">
                    </td>
                    <td>
                        <div class="form-check">
                            <input type="hidden" name="horario_atencion[{{ $key }}][cerrado]" value="0">
                            <input type="checkbox" 
                                name="horario_atencion[{{ $key }}][cerrado]" 
                                class="form-check-input cerrado-check"
                                value="1"
                                data-dia="{{ $key }}"
                                {{ old("horario_atencion.$key.cerrado", $horario['cerrado'] ?? false) ? 'checked' : '' }}>
                        </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Botones --}}
      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> {{ $empresa->exists ? 'Actualizar' : 'Crear' }} Empresa
        </button>
        <a href="{{ $empresa->exists ? route('empresa.index') : route('dashboard') }}" 
           class="btn btn-outline-secondary">
          <i class="bi bi-x-circle"></i> Cancelar
        </a>
      </div>
    </form>
  </div>

  @push('styles')
  <style>
    .horario-input:disabled {
      background-color: #e9ecef;
      cursor: not-allowed;
    }
  </style>
  @endpush

  @push('scripts')
  <script>
    $(document).ready(function() {
      // Manejar horarios - deshabilitar inputs cuando está cerrado
      $('.cerrado-check').change(function() {
        const dia = $(this).data('dia');
        const cerrado = $(this).is(':checked');
        
        $(`input[name="horario_atencion[${dia}][apertura]"]`).prop('disabled', cerrado);
        $(`input[name="horario_atencion[${dia}][cierre]"]`).prop('disabled', cerrado);
      });
      
      // Ejecutar al cargar para establecer estado inicial
      $('.cerrado-check').trigger('change');
      
      // Validación del formulario
      $('#empresaForm').submit(function(e) {
        let isValid = true;
        
        // Validar que al menos tenga nombre
        const nombre = $('input[name="nombre"]').val();
        if (!nombre || nombre.trim() === '') {
          e.preventDefault();
          alert('El nombre de la empresa es obligatorio.');
          isValid = false;
        }
        
        return isValid;
      });
    });
  </script>
  @endpush
</x-app-layout>