<?php

namespace App\DTOs;

class CreateWithdrawalDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly float $monto,
        public readonly string $metodoPago,
        public readonly array $datosPago,
        public readonly ?string $notas = null
    ) {}

    public static function fromRequest(array $data, int $userId): self
    {
        $datosPago = [
            'titular' => $data['titular'],
            'numero_cuenta' => $data['numero_cuenta'],
        ];

        if ($data['metodo_pago'] === 'transferencia_bancaria') {
            $datosPago['banco'] = $data['banco'];
            $datosPago['tipo_cuenta'] = $data['tipo_cuenta'];
        }

        return new self(
            userId: $userId,
            monto: (float) $data['monto'],
            metodoPago: $data['metodo_pago'],
            datosPago: $datosPago,
            notas: $data['notas'] ?? null
        );
    }
}
