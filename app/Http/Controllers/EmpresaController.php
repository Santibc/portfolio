<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametros;
use Illuminate\Support\Facades\File;

/**
 * Módulo de configuración de la EMPRESA DUEÑA (emisora de las cotizaciones).
 * Estos datos alimentan el encabezado del PDF de cotización (razón social, RUC,
 * dirección, teléfonos, etc.) y se guardan en la tabla `parametros` (claves empresa_*).
 *
 * No confundir con los datos del cliente que pide la cotización: esos viven en el
 * registro de cada Cliente y se muestran en el bloque "Cliente" del PDF.
 */
class EmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $this->soloAdmin();

        $empresa = Parametros::empresa();
        $logoUrl = File::exists(public_path('images/logo.png')) ? asset('images/logo.png') : null;

        return view('empresa.form', compact('empresa', 'logoUrl'));
    }

    public function update(Request $request)
    {
        $this->soloAdmin();

        $data = $request->validate([
            'razon_social' => ['required', 'string', 'max:255'],
            'ruc'          => ['nullable', 'string', 'max:100'],
            'direccion'    => ['nullable', 'string', 'max:255'],
            'telefonos'    => ['nullable', 'string', 'max:255'],
            'email'        => ['nullable', 'email', 'max:255'],
            'sitio_web'    => ['nullable', 'string', 'max:255'],
            'logo'         => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ], [
            'razon_social.required' => 'La razón social es obligatoria.',
            'email.email'           => 'El correo no es válido.',
            'logo.image'            => 'El logo debe ser una imagen.',
            'logo.mimes'            => 'El logo debe ser PNG, JPG o WebP.',
            'logo.max'              => 'El logo no debe superar 2MB.',
        ]);

        $map = [
            'empresa_razon_social' => $data['razon_social'],
            'empresa_ruc'          => $data['ruc'] ?? '',
            'empresa_direccion'    => $data['direccion'] ?? '',
            'empresa_telefonos'    => $data['telefonos'] ?? '',
            'empresa_email'        => $data['email'] ?? '',
            'empresa_sitio_web'    => $data['sitio_web'] ?? '',
        ];

        foreach ($map as $nombre => $valor) {
            Parametros::updateOrCreate(
                ['nombre_parametro' => $nombre],
                ['valor_parametro' => $valor, 'estado' => true]
            );
        }

        // El logo se guarda como images/logo.png: tanto el PDF como el menú lo leen de ahí.
        if ($request->hasFile('logo')) {
            $dir = public_path('images');
            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $request->file('logo')->move($dir, 'logo.png');
        }

        return redirect()->route('empresa.edit')->with('success', 'Datos de la empresa actualizados correctamente.');
    }

    private function soloAdmin(): void
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(403, 'Solo un administrador puede configurar los datos de la empresa.');
        }
    }
}
