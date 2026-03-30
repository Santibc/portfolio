<div class="tarjeta-card" data-tarjeta-id="{{ $tarjeta->id }}"
     data-posicion="{{ $tarjeta->posicion }}"
     data-prioridad="{{ $tarjeta->prioridad }}"
     data-estado-fecha="{{ $tarjeta->estado_vencimiento ?? 'sin_fecha' }}"
     data-titulo="{{ strtolower($tarjeta->titulo) }}"
     data-usuarios="{{ $tarjeta->usuarios->pluck('id')->join(',') }}"
     data-etiquetas="{{ $tarjeta->etiquetas->pluck('id')->join(',') }}"
     onclick="abrirTarjetaModal({{ $tarjeta->id }})">

    {{-- Image preview --}}
    @php
        $previewImage = $tarjeta->adjuntos->first(fn($a) => str_starts_with($a->mime_type ?? '', 'image/'));
    @endphp
    @if($previewImage)
    <div class="tarjeta-imagen-preview">
        <img src="{{ $previewImage->url }}" alt="" loading="lazy">
    </div>
    @endif

    {{-- Cover color --}}
    @if($tarjeta->color_portada)
    <div class="tarjeta-portada" style="background-color: {{ $tarjeta->color_portada }}"></div>
    @endif

    {{-- Labels --}}
    @if($tarjeta->etiquetas->isNotEmpty())
    <div class="tarjeta-etiquetas">
        @foreach($tarjeta->etiquetas as $etiqueta)
        <span class="etiqueta-chip" style="background-color: {{ $etiqueta->color }}"
              title="{{ $etiqueta->nombre }}">{{ $etiqueta->nombre }}</span>
        @endforeach
    </div>
    @endif

    {{-- Title --}}
    <div class="tarjeta-titulo">{{ $tarjeta->titulo }}</div>

    {{-- Badges --}}
    @php
        $estado = $tarjeta->estado_vencimiento;
        $progreso = $tarjeta->progreso_checklist;
        $comentariosCount = $tarjeta->comentarios->where('tipo', 'comentario')->count();
        $adjuntosCount = $tarjeta->adjuntos->count();
    @endphp
    @if($tarjeta->fecha_vencimiento || $tarjeta->descripcion || $comentariosCount > 0 || $progreso['total'] > 0 || $adjuntosCount > 0 || $tarjeta->prioridad === 'alta')
    <div class="tarjeta-badges">
        @if($tarjeta->fecha_vencimiento)
        <span class="tarjeta-badge badge-{{ $estado }}">
            <i class="bi bi-clock"></i>
            {{ $tarjeta->fecha_vencimiento->format('d M') }}
        </span>
        @endif

        @if($tarjeta->descripcion)
        <span class="tarjeta-badge"><i class="bi bi-text-left"></i></span>
        @endif

        @if($comentariosCount > 0)
        <span class="tarjeta-badge"><i class="bi bi-chat"></i> {{ $comentariosCount }}</span>
        @endif

        @if($progreso['total'] > 0)
        <span class="tarjeta-badge {{ $progreso['completados'] === $progreso['total'] ? 'badge-completado' : '' }}">
            <i class="bi bi-check2-square"></i> {{ $progreso['completados'] }}/{{ $progreso['total'] }}
        </span>
        @endif

        @if($adjuntosCount > 0)
        <span class="tarjeta-badge"><i class="bi bi-paperclip"></i> {{ $adjuntosCount }}</span>
        @endif

        @if($tarjeta->prioridad === 'alta')
        <span class="tarjeta-badge badge-prioridad-alta"><i class="bi bi-flag-fill"></i></span>
        @endif
    </div>
    @endif

    {{-- Assignees --}}
    @if($tarjeta->usuarios->isNotEmpty())
    <div class="tarjeta-asignados">
        @foreach($tarjeta->usuarios->take(3) as $usuario)
        <div class="avatar-mini" title="{{ $usuario->name }}">
            @if($usuario->hasProfilePhoto())
                <img src="{{ $usuario->profile_photo_url }}" alt="{{ $usuario->initials }}">
            @else
                {{ $usuario->initials }}
            @endif
        </div>
        @endforeach
        @if($tarjeta->usuarios->count() > 3)
        <div class="avatar-mini avatar-more">+{{ $tarjeta->usuarios->count() - 3 }}</div>
        @endif
    </div>
    @endif
</div>
