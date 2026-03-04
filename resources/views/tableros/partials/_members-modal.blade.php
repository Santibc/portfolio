{{-- Members Modal --}}
<div class="modal fade" id="miembrosModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-people me-2"></i>Miembros del tablero</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($tablero->miembros as $miembro)
                <div class="miembro-item d-flex align-items-center gap-3 p-2">
                    <div class="miembro-avatar">
                        @if($miembro->hasProfilePhoto())
                            <img src="{{ $miembro->profile_photo_url }}" alt="{{ $miembro->initials }}"
                                 style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                        @else
                            {{ $miembro->initials }}
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $miembro->name }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ ucfirst($miembro->pivot->rol) }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
