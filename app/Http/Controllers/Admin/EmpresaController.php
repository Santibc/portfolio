<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmpresaRequest;
use App\Services\Settings\ConfigService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    public function __construct(private readonly ConfigService $config) {}

    public function edit(): View
    {
        return view('admin.empresa.edit', [
            'empresa' => $this->config->group('empresa'),
            'dian' => $this->config->group('dian'),
            'banco' => $this->config->group('banco'),
            'contacto' => $this->config->group('contacto'),
        ]);
    }

    public function update(EmpresaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->config->set('empresa.razon_social', $data['razon_social'], 'string', 'empresa');
        $this->config->set('empresa.nit', $data['nit'], 'string', 'empresa');
        $this->config->set('empresa.direccion', $data['direccion'], 'string', 'empresa');
        $this->config->set('empresa.telefono', $data['telefono'], 'string', 'empresa');
        $this->config->set('empresa.email', $data['email'], 'string', 'empresa');
        $this->config->set('empresa.sitio_web', $data['sitio_web'] ?? '', 'string', 'empresa');
        $this->config->set('empresa.regimen_tributario', $data['regimen_tributario'] ?? '', 'text', 'empresa');

        if ($request->hasFile('logo')) {
            $logoPath = $this->saveLogo($request->file('logo'));
            $this->config->set('empresa.logo_path', $logoPath, 'string', 'empresa');
        }

        $this->config->set('dian.resolucion_texto_clc', $data['dian_resolucion_clc'] ?? '', 'text', 'dian');
        $this->config->set('dian.resolucion_texto_fv', $data['dian_resolucion_fv'] ?? '', 'text', 'dian');

        $this->config->set('banco.nombre', $data['banco_nombre'], 'string', 'banco');
        $this->config->set('banco.pais', $data['banco_pais'], 'string', 'banco');
        $this->config->set('banco.direccion', $data['banco_direccion'], 'string', 'banco');
        $this->config->set('banco.titular', $data['banco_titular'], 'string', 'banco');
        $this->config->set('banco.moneda', $data['banco_moneda'], 'string', 'banco');
        $this->config->set('banco.swift', $data['banco_swift'], 'string', 'banco');
        $this->config->set('banco.numero_cuenta', $data['banco_numero_cuenta'], 'string', 'banco');

        $this->config->set('contacto_financiero.nombre', $data['contacto_nombre'], 'string', 'contacto');
        $this->config->set('contacto_financiero.email', $data['contacto_email'], 'string', 'contacto');
        $this->config->set('contacto_financiero.telefono', $data['contacto_telefono'], 'string', 'contacto');

        return redirect()->route('admin.empresa.edit')->with('success', 'Datos de empresa actualizados.');
    }

    private function saveLogo(UploadedFile $file): string
    {
        $directorio = public_path('uploads/empresa');

        if (! File::isDirectory($directorio)) {
            File::makeDirectory($directorio, 0755, true);
        }

        $extension = strtolower((string) $file->extension());
        $extensionesValidas = ['png', 'jpg', 'jpeg', 'webp'];

        if (! in_array($extension, $extensionesValidas, true)) {
            abort(422, 'Formato de imagen no soportado.');
        }

        $previo = $this->config->get('empresa.logo_path');
        if (is_string($previo) && $previo !== '') {
            $previoAbs = public_path($previo);
            if (File::exists($previoAbs)) {
                File::delete($previoAbs);
            }
        }

        $nombre = 'logo-'.Str::uuid()->toString().'.'.$extension;
        $file->move($directorio, $nombre);

        return 'uploads/empresa/'.$nombre;
    }
}
