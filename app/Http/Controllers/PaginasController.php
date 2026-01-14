<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Carrito;

class PaginasController extends Controller
{
    /**
     * Mostrar página de Términos y Condiciones
     */
    public function terminos()
    {
        // Obtener la primera empresa (monoempresa)
        $empresa = Empresa::orderBy('id')->first();

        // Obtener o crear carrito del usuario
        $carrito = Carrito::obtenerOCrear(session()->getId(), $empresa->id);

        return view('tienda.paginas.terminos', compact('empresa', 'carrito'));
    }

    /**
     * Mostrar página de Políticas de Privacidad
     */
    public function privacidad()
    {
        // Obtener la primera empresa (monoempresa)
        $empresa = Empresa::orderBy('id')->first();

        // Obtener o crear carrito del usuario
        $carrito = Carrito::obtenerOCrear(session()->getId(), $empresa->id);

        return view('tienda.paginas.privacidad', compact('empresa', 'carrito'));
    }
}
