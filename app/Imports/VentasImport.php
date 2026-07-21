<?php

namespace App\Imports;

use App\Models\Almacen;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VentasImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings, WithCalculatedFormulas
{
    protected int $createdBy;
    public int $procesadas = 0;
    public array $errores = [];

    public function __construct(int $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => '"',
            'escape_character' => '\\',
            'input_encoding' => 'UTF-8',
        ];
    }

    public function collection(Collection $rows): void
    {
        $usuariosPorEmail = User::role('vendedor')->pluck('id', 'email');
        $almacenesPorCodigo = Almacen::activos()->pluck('id', 'codigo');
        $almacenPorUser = User::role('vendedor')->pluck('almacen_id', 'id');

        foreach ($rows as $i => $row) {
            $filaNum = $i + 2;
            $data = $this->normalizar($row->toArray());

            $email = $data['vendedor_email'] ?? null;
            $fechaRaw = $data['fecha'] ?? null;
            $monto = $data['monto'] ?? null;
            $descripcion = $data['descripcion'] ?? null;
            $codigoAlmacen = $data['almacen_codigo'] ?? null;

            if (!$email) {
                $this->errores[] = "Fila {$filaNum}: falta el correo del vendedor.";
                continue;
            }

            $userId = $usuariosPorEmail[strtolower(trim($email))] ?? $usuariosPorEmail[$email] ?? null;
            if (!$userId) {
                $this->errores[] = "Fila {$filaNum}: no se encontró vendedor con correo '{$email}'.";
                continue;
            }

            try {
                $fecha = is_numeric($fechaRaw)
                    ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $fechaRaw))
                    : Carbon::parse((string) $fechaRaw);
            } catch (\Throwable $e) {
                $this->errores[] = "Fila {$filaNum}: fecha inválida '{$fechaRaw}'.";
                continue;
            }

            if ($fecha->isFuture()) {
                $this->errores[] = "Fila {$filaNum}: la fecha no puede ser futura.";
                continue;
            }

            $montoLimpio = is_string($monto) ? str_replace([',', ' ', '$'], ['.', '', ''], $monto) : $monto;
            if (!is_numeric($montoLimpio) || (float) $montoLimpio <= 0) {
                $this->errores[] = "Fila {$filaNum}: monto inválido '{$monto}'.";
                continue;
            }

            $almacenId = null;
            if ($codigoAlmacen) {
                $almacenId = $almacenesPorCodigo[$codigoAlmacen] ?? $almacenesPorCodigo[strtoupper(trim($codigoAlmacen))] ?? null;
                if (!$almacenId) {
                    $this->errores[] = "Fila {$filaNum}: no se encontró almacén con código '{$codigoAlmacen}'.";
                    continue;
                }
            } else {
                $almacenId = $almacenPorUser[$userId] ?? null;
            }

            Venta::create([
                'user_id' => $userId,
                'almacen_id' => $almacenId,
                'fecha' => $fecha->toDateString(),
                'monto' => (float) $montoLimpio,
                'descripcion' => $descripcion ? substr((string) $descripcion, 0, 255) : null,
                'created_by' => $this->createdBy,
            ]);

            $this->procesadas++;
        }
    }

    protected function normalizar(array $row): array
    {
        $norm = [];
        foreach ($row as $k => $v) {
            $key = strtolower(trim(str_replace([' ', '-'], '_', (string) $k)));
            $norm[$key] = is_string($v) ? trim($v) : $v;
        }
        return $norm;
    }
}
