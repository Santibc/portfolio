<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveKycRequest;
use App\Http\Requests\Admin\RejectKycRequest;
use App\Models\User;
use App\Services\Kyc\KycService;

class KycReviewController extends Controller
{
    public function __construct(
        private KycService $kycService
    ) {}

    /**
     * Lista de KYC pendientes de revisión
     */
    public function index()
    {
        $pendientes = $this->kycService->getUsersPendingReview();

        return view('admin.kyc.index', compact('pendientes'));
    }

    /**
     * Ver documentos de un usuario específico
     */
    public function show(User $user)
    {
        $documentos = $user->documentosKyc;

        return view('admin.kyc.show', compact('user', 'documentos'));
    }

    /**
     * Aprobar KYC de un usuario
     */
    public function approve(User $user, ApproveKycRequest $request)
    {
        $this->kycService->approveKyc($user, auth()->user());

        return redirect()->route('admin.kyc.index')
            ->with('success', "KYC de {$user->name} aprobado exitosamente.");
    }

    /**
     * Rechazar KYC de un usuario
     */
    public function reject(User $user, RejectKycRequest $request)
    {
        $this->kycService->rejectKyc(
            $user,
            auth()->user(),
            $request->input('motivo')
        );

        return redirect()->route('admin.kyc.index')
            ->with('success', "KYC de {$user->name} rechazado.");
    }
}
