<?php
// app/Http/Controllers/SalesController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function form(Lead $lead)
    {
        $sale = new Sale(); // Para un nuevo formulario

        return view('sales.form', compact('lead', 'sale'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'apellido_cliente' => 'required|string|max:255',
            'email_cliente' => 'required|email|max:255',
            'telefono_cliente' => 'required|string|max:50',
            'identificacion_personal' => 'nullable|string|max:100',
            'domicilio' => 'required|string|max:255',
            'metodo_pago' => 'required|string|max:100',
            'comprobante_pago' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tipo_acuerdo' => 'required|string|max:100',
            'comentarios' => 'nullable|string',
        ]);

        $path = $request->file('comprobante_pago')->store('comprobantes', 'public');
        $lead = lead::findOrFail($request->lead_id);

        Sale::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(), // O $llamada->user_id si el closer asignado es el que cierra
            'nombre_cliente' => $validated['nombre_cliente'],
            'apellido_cliente' => $validated['apellido_cliente'],
            'email_cliente' => $validated['email_cliente'],
            'telefono_cliente' => $validated['telefono_cliente'],
            'identificacion_personal' => $validated['identificacion_personal'],
            'domicilio' => $validated['domicilio'],
            'metodo_pago' => $validated['metodo_pago'],
            'comprobante_pago_path' => $path,
            'tipo_acuerdo' => $validated['tipo_acuerdo'],
            'comentarios' => $validated['comentarios'],
        ]);

        return redirect()->route('leads')->with('success', 'Venta registrada correctamente.');
    }
}