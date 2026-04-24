<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiigoConfigRequest;
use App\Models\SiigoCatalogo;
use App\Models\SiigoConfig;
use App\Services\Siigo\SiigoClient;
use App\Services\Siigo\SiigoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SiigoController extends Controller
{
    public function edit(): View
    {
        $config = SiigoConfig::current();
        $catalogos = [
            'document-types' => SiigoCatalogo::tipo('document-types')->count(),
            'taxes' => SiigoCatalogo::tipo('taxes')->count(),
            'payment-types' => SiigoCatalogo::tipo('payment-types')->count(),
        ];

        return view('admin.siigo.edit', compact('config', 'catalogos'));
    }

    public function update(SiigoConfigRequest $request): RedirectResponse
    {
        $config = SiigoConfig::current();
        $data = $request->validated();

        if (empty($data['access_key'])) {
            unset($data['access_key']);
        }

        $data['token_cache'] = null;
        $data['token_expires_at'] = null;

        $config->fill($data)->save();

        return redirect()->route('admin.siigo.edit')->with('success', 'Configuración Siigo guardada.');
    }

    public function probarConexion(): JsonResponse
    {
        $config = SiigoConfig::current();
        $client = new SiigoClient($config);
        $resultado = $client->probarConexion();

        return response()->json($resultado, $resultado['ok'] ? 200 : 422);
    }

    public function sincronizarCatalogos(): RedirectResponse
    {
        try {
            $config = SiigoConfig::current();
            $client = new SiigoClient($config);
            $service = new SiigoService($client);

            $resumen = $service->sincronizarCatalogos();
            $total = array_sum($resumen);

            return redirect()->route('admin.siigo.edit')
                ->with('success', "Sincronización completada: {$total} registros traídos de Siigo.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::channel('siigo')->error('Sincronización catálogos falló', [
                'mensaje' => $e->getMessage(),
                'clase' => $e::class,
            ]);

            return redirect()->route('admin.siigo.edit')
                ->with('error', 'Error sincronizando con Siigo. Revisa los logs para el detalle.');
        }
    }
}
