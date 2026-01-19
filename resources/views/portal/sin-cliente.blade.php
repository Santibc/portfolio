<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-circle fs-4"></i>
            <span>Mi Portal</span>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid px-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="mb-3">Cuenta sin cliente asociado</h4>
                            <p class="text-muted mb-4">
                                {{ $mensaje ?? 'Su cuenta de usuario no tiene un cliente asociado. Por favor, contacte al administrador para vincular su cuenta.' }}
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="bi bi-box-arrow-left me-1"></i> Cerrar Sesion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
