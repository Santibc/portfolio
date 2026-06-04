<x-app-layout>
    <x-slot name="header">
        Datos de la Empresa
    </x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <p class="text-muted">
                    Estos datos identifican a tu empresa (la que emite las cotizaciones) y se muestran en el
                    <strong>encabezado del PDF</strong>. No se confunden con los datos del cliente que solicita
                    la cotización (esos se gestionan en el módulo <strong>Clientes</strong>).
                </p>

                <form method="POST" action="{{ route('empresa.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        {{-- Razón social --}}
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">Razón social <span class="text-danger">*</span></label>
                            <input name="razon_social" type="text"
                                   class="form-control @error('razon_social') is-invalid @enderror"
                                   value="{{ old('razon_social', $empresa['razon_social']) }}"
                                   placeholder="Ej: INVERSIONES VADISA LATINOAMERICA, S.A.">
                            @error('razon_social') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- RUC / Cédula --}}
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">RUC / Cédula</label>
                            <input name="ruc" type="text"
                                   class="form-control @error('ruc') is-invalid @enderror"
                                   value="{{ old('ruc', $empresa['ruc']) }}"
                                   placeholder="Ej: 55499-38-333705 DV 85">
                            @error('ruc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Dirección --}}
                        <div class="col-12 mb-3">
                            <label class="form-label">Dirección</label>
                            <input name="direccion" type="text"
                                   class="form-control @error('direccion') is-invalid @enderror"
                                   value="{{ old('direccion', $empresa['direccion']) }}"
                                   placeholder="Ej: 1a Avenida, Cuarta Calle, France Field, Colón, Rep. de Panamá">
                            @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Teléfonos --}}
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">Teléfono(s)</label>
                            <input name="telefonos" type="text"
                                   class="form-control @error('telefonos') is-invalid @enderror"
                                   value="{{ old('telefonos', $empresa['telefonos']) }}"
                                   placeholder="Ej: +507 6403 5170">
                            @error('telefonos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">Correo</label>
                            <input name="email" type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $empresa['email']) }}"
                                   placeholder="Ej: ventas@empresa.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Sitio web --}}
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">Sitio web</label>
                            <input name="sitio_web" type="text"
                                   class="form-control @error('sitio_web') is-invalid @enderror"
                                   value="{{ old('sitio_web', $empresa['sitio_web']) }}"
                                   placeholder="Ej: www.empresa.com">
                            @error('sitio_web') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Logo --}}
                        <div class="col-12 mb-3">
                            <label class="form-label">Logo</label>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:120px; height:70px; border:1px solid #dee2e6; border-radius:4px; display:flex; align-items:center; justify-content:center; background:#fff;">
                                    @if($logoUrl)
                                        <img src="{{ $logoUrl }}" alt="Logo actual" style="max-width:110px; max-height:60px; object-fit:contain;">
                                    @else
                                        <span class="text-muted small">Sin logo</span>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <input name="logo" type="file" accept="image/png,image/jpeg,image/webp"
                                           class="form-control @error('logo') is-invalid @enderror">
                                    <small class="text-muted">PNG, JPG o WebP (máx 2MB). Si subes uno nuevo, reemplaza el actual en el PDF y el menú.</small>
                                    @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
