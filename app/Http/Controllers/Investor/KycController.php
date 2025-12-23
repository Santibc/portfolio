<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\StoreKycRequest;
use App\Services\Kyc\KycService;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function __construct(
        private KycService $kycService
    ) {}

    /**
     * Ver estado del KYC del usuario
     */
    public function index()
    {
        $user = auth()->user();
        $documentos = $user->documentosKyc;

        return view('investor.kyc.index', compact('user', 'documentos'));
    }

    /**
     * Formulario para subir documentos KYC
     */
    public function create()
    {
        $user = auth()->user();

        // Si ya subió documentos y están en revisión o aprobados, redirigir
        if (in_array($user->kyc_status, ['en_revision', 'aprobado'])) {
            return redirect()->route('inversionista.kyc.index')
                ->with('info', 'Ya has subido tus documentos KYC.');
        }

        // Si está rechazado o pendiente, permitir subir documentos
        return view('investor.kyc.create');
    }

    /**
     * Guardar documentos KYC
     */
    public function store(StoreKycRequest $request)
    {
        $this->kycService->submitKyc(
            auth()->user(),
            $request->validated()
        );

        return redirect()->route('inversionista.kyc.index')
            ->with('success', '¡Documentos enviados! Ya puedes empezar a invertir mientras revisamos tu información.');
    }
}
