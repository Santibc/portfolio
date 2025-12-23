<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService)
    {
    }

    /**
     * Vista principal - resumen de billetera
     */
    public function index()
    {
        $user = auth()->user();
        $billetera = $this->walletService->getOrCreateWallet($user);
        $summary = $this->walletService->getBalanceSummary($user);
        $recentTransactions = $this->walletService->getRecentTransactions($user, 5);

        return view('investor.wallet.index', compact('billetera', 'summary', 'recentTransactions'));
    }

    /**
     * Historial completo de transacciones
     */
    public function transactions(Request $request)
    {
        $user = auth()->user();
        $filters = $request->only(['tipo', 'fecha_desde', 'fecha_hasta', 'naturaleza']);
        $transactions = $this->walletService->getTransactionHistory($user, $filters);

        // Tipos de transacción para el filtro
        $tiposTransaccion = [
            'deposito' => 'Depósito',
            'retiro' => 'Retiro',
            'inversion' => 'Inversión',
            'dividendo' => 'Dividendo',
            'retorno_capital' => 'Retorno de Capital',
            'venta_trading' => 'Venta Trading',
            'compra_trading' => 'Compra Trading',
            'comision' => 'Comisión',
            'reversa' => 'Reversa',
            'ajuste' => 'Ajuste',
        ];

        return view('investor.wallet.transactions', compact('transactions', 'filters', 'tiposTransaccion'));
    }
}
