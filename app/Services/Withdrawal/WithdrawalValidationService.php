<?php

namespace App\Services\Withdrawal;

use App\Models\ConfiguracionSistema;
use App\Models\Retiro;
use App\Models\User;

class WithdrawalValidationService
{
    /**
     * Obtiene el monto mínimo de retiro desde configuración
     */
    public function getMinimumAmount(): float
    {
        return (float) ConfiguracionSistema::obtenerValor('retiro_minimo', 50000);
    }

    /**
     * Obtiene el límite diario de retiros desde configuración
     */
    public function getDailyLimit(): float
    {
        return (float) ConfiguracionSistema::obtenerValor('limite_diario_retiros', 5000000);
    }

    /**
     * Valida que el monto sea mayor o igual al mínimo
     */
    public function validateMinimumAmount(float $monto): bool
    {
        return $monto >= $this->getMinimumAmount();
    }

    /**
     * Valida que el usuario tenga saldo disponible suficiente
     */
    public function validateAvailableBalance(User $user, float $monto): bool
    {
        $billetera = $user->billetera;

        if (!$billetera) {
            return false;
        }

        return $billetera->saldo_disponible >= $monto;
    }

    /**
     * Valida el límite diario de retiros
     */
    public function validateDailyLimit(User $user, float $monto): bool
    {
        $retirosDiarios = Retiro::where('usuario_id', $user->id)
            ->whereDate('fecha_solicitud', today())
            ->whereNotIn('estado', ['rechazado', 'cancelado'])
            ->sum('monto_solicitado');

        return ($retirosDiarios + $monto) <= $this->getDailyLimit();
    }

    /**
     * Valida que no haya retiros pendientes
     */
    public function validateNoPendingWithdrawals(User $user): bool
    {
        return !Retiro::where('usuario_id', $user->id)
            ->whereIn('estado', ['pendiente', 'en_revision', 'aprobado'])
            ->exists();
    }

    /**
     * Obtiene todos los errores de validación
     */
    public function getValidationErrors(User $user, float $monto): array
    {
        $errors = [];

        if (!$this->validateMinimumAmount($monto)) {
            $minimo = number_format($this->getMinimumAmount(), 0, ',', '.');
            $errors['monto'] = "El monto mínimo de retiro es \${$minimo} COP";
        }

        if (!$this->validateAvailableBalance($user, $monto)) {
            $disponible = $user->billetera
                ? number_format($user->billetera->saldo_disponible, 0, ',', '.')
                : '0';
            $errors['saldo'] = "Saldo disponible insuficiente. Tienes \${$disponible} COP";
        }

        if (!$this->validateDailyLimit($user, $monto)) {
            $limite = number_format($this->getDailyLimit(), 0, ',', '.');
            $errors['limite_diario'] = "Has excedido el límite diario de retiros (\${$limite} COP)";
        }

        if (!$this->validateNoPendingWithdrawals($user)) {
            $errors['pendiente'] = "Ya tienes un retiro pendiente. Debes esperar a que se procese antes de solicitar otro.";
        }

        return $errors;
    }

    /**
     * Valida si el usuario puede realizar el retiro
     */
    public function canWithdraw(User $user, float $monto): bool
    {
        return empty($this->getValidationErrors($user, $monto));
    }
}
