<?php

namespace App\Http\Controllers;

use App\Models\LandingConfiguracion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingConfiguracionController extends Controller
{
    public function edit()
    {
        $config = LandingConfiguracion::first() ?? new LandingConfiguracion();
        return view('admin.landing.configuracion.edit', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
            'footer_texto' => 'nullable|string|max:500',
            'terminos_url' => 'nullable|string|max:500',
            'whatsapp_numero' => 'nullable|string|max:20',
            'whatsapp_texto' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|string|max:500',
            'tiktok_url' => 'nullable|string|max:500',
            'instagram_url' => 'nullable|string|max:500',
            'linkedin_url' => 'nullable|string|max:500',
            'contacto_email' => 'nullable|email|max:255',
        ]);

        $config = LandingConfiguracion::first();

        if (!$config) {
            $config = new LandingConfiguracion();
        }

        // Procesar imágenes
        if ($request->hasFile('logo')) {
            $config->logo = $this->uploadImage($request->file('logo'), 'images/landing/', $config->logo);
        }

        if ($request->hasFile('favicon')) {
            $config->favicon = $this->uploadImage($request->file('favicon'), 'images/landing/', $config->favicon);
        }

        // Campos de texto
        $config->footer_texto = $request->footer_texto;
        $config->terminos_url = $request->terminos_url;
        $config->whatsapp_numero = $request->whatsapp_numero;
        $config->whatsapp_texto = $request->whatsapp_texto;
        $config->facebook_url = $request->facebook_url;
        $config->tiktok_url = $request->tiktok_url;
        $config->instagram_url = $request->instagram_url;
        $config->linkedin_url = $request->linkedin_url;
        $config->contacto_email = $request->contacto_email;

        $config->save();

        return redirect()->route('admin.landing.configuracion.edit')
                        ->with('success', 'Configuración actualizada correctamente');
    }

    private function uploadImage($file, $directory, $oldPath = null)
    {
        // Eliminar imagen anterior si existe
        if ($oldPath && file_exists(public_path($oldPath))) {
            unlink(public_path($oldPath));
        }

        // Crear directorio si no existe
        if (!file_exists(public_path($directory))) {
            mkdir(public_path($directory), 0755, true);
        }

        // Subir nueva imagen
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $filename);

        return $directory . $filename;
    }
}
