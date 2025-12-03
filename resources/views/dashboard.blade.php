<x-app-layout>
    <div style="padding: 2rem; width: 100%;">
        <div class="card" style="padding: 3rem; text-align: center;">
            <i class="fas fa-leaf" style="font-size: 4rem; color: #4A7C59; margin-bottom: 1.5rem;"></i>
            <h1 style="font-size: 2.5rem; font-weight: 700; color: #2D5A27; margin-bottom: 1rem;">
                Bienvenido a AGROMARKET
            </h1>
            <p style="color: #6C757D; font-size: 1.125rem; margin-bottom: 2rem;">
                Hola, <strong>{{ Auth::user()->name }}</strong>
            </p>
            <p style="color: #6C757D; max-width: 600px; margin: 0 auto;">
                Esta es tu vista de inicio. Los módulos funcionales se irán agregando progresivamente.
            </p>
        </div>
    </div>
</x-app-layout>
