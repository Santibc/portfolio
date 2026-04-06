<?php

namespace App\Http\Controllers\Pdv;

use App\Http\Controllers\Controller;
use App\Services\Siigo\SiigoApiClient;
use App\Services\Siigo\SiigoConfigService;
use Illuminate\Http\Request;

class SiigoConfigController extends Controller
{
    private SiigoConfigService $configService;

    public function __construct(SiigoConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function index()
    {
        $config = $this->configService->obtenerConfiguracionActual();
        return view('pdv.siigo.configuracion', compact('config'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'siigo_username' => 'nullable|email|max:255',
            'siigo_access_key' => 'nullable|string|max:500',
            'siigo_username_test' => 'nullable|email|max:255',
            'siigo_access_key_test' => 'nullable|string|max:500',
            'siigo_partner_id' => 'nullable|string|max:100',
            'siigo_document_type_id' => 'nullable|integer',
            'siigo_credit_note_type_id' => 'nullable|integer',
            'siigo_payment_type_efectivo_id' => 'nullable|integer',
            'siigo_payment_type_transferencia_id' => 'nullable|integer',
            'siigo_tax_id' => 'nullable|integer',
            'siigo_seller_id' => 'nullable|integer',
            'siigo_consumidor_final_nit' => 'nullable|string|max:20',
            'siigo_max_reintentos' => 'nullable|integer|min:1|max:10',
        ]);

        $data = $request->only([
            'siigo_username', 'siigo_access_key',
            'siigo_username_test', 'siigo_access_key_test',
            'siigo_partner_id',
            'siigo_document_type_id', 'siigo_credit_note_type_id',
            'siigo_payment_type_efectivo_id', 'siigo_payment_type_transferencia_id',
            'siigo_tax_id', 'siigo_seller_id',
            'siigo_consumidor_final_nit', 'siigo_max_reintentos',
        ]);

        $data['siigo_activo'] = $request->boolean('siigo_activo') ? 'true' : 'false';
        $data['siigo_modo'] = $request->input('siigo_modo', 'test');
        $data['siigo_facturar_siempre'] = $request->boolean('siigo_facturar_siempre') ? 'true' : 'false';

        $this->configService->guardarConfiguracion($data);

        return redirect()->route('pdv.siigo.config')
            ->with('success', 'Configuración de SIIGO guardada exitosamente.');
    }

    public function testConexion()
    {
        $resultado = $this->configService->testConexion();
        return response()->json($resultado);
    }

    public function cargarCatalogos()
    {
        $api = app(SiigoApiClient::class);
        if (!$api->estaConfigurado()) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Configure las credenciales de SIIGO primero.',
            ]);
        }

        return response()->json([
            'exito' => true,
            'document_types' => $this->configService->obtenerDocumentTypes(),
            'credit_note_types' => $this->configService->obtenerCreditNoteTypes(),
            'payment_types' => $this->configService->obtenerPaymentTypes(),
            'taxes' => $this->configService->obtenerTaxes(),
            'sellers' => $this->configService->obtenerSellers(),
        ]);
    }
}
