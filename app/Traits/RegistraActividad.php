<?php

namespace App\Traits;

use App\Models\RegistroActividad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait RegistraActividad
{
    /**
     * Campos que nunca se incluyen en el snapshot de cambios.
     */
    private array $camposExcluidosLog = [
        'password',
        'remember_token',
        'updated_at',
        'created_at',
    ];

    /**
     * Registra una actividad simple (sin captura automatica de cambios).
     * Mantenido para retrocompatibilidad y casos donde no aplica un modelo.
     */
    protected function registrarActividad(
        string $accion,
        string $descripcion,
        ?int $ordenId = null,
        ?array $datosExtra = null
    ): RegistroActividad {
        return RegistroActividad::create([
            'usuario_id' => Auth::id(),
            'orden_id' => $ordenId,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'datos_extra' => $datosExtra,
        ]);
    }

    /**
     * Registra la creacion de un modelo. Captura el snapshot completo
     * del registro recien creado como conjunto de cambios "antes=null".
     */
    protected function registrarCreacion(
        string $accion,
        string $descripcion,
        Model $modelo,
        ?int $ordenId = null,
        array $extra = []
    ): RegistroActividad {
        $atributos = $this->extraerAtributosLimpios($modelo->getAttributes());
        $cambios = [];
        foreach ($atributos as $campo => $valor) {
            $cambios[$campo] = ['antes' => null, 'despues' => $valor];
        }

        $payload = array_merge([
            'tipo_cambio' => 'create',
            'modelo' => class_basename($modelo),
            'modelo_id' => $modelo->getKey(),
            'cambios' => $cambios,
        ], $extra);

        return $this->registrarActividad($accion, $descripcion, $ordenId, $payload);
    }

    /**
     * Registra la actualizacion de un modelo. Compara los valores originales
     * (snapshot tomado antes de fill/save) contra los actuales y guarda
     * unicamente los campos que cambiaron.
     */
    protected function registrarActualizacion(
        string $accion,
        string $descripcion,
        Model $modelo,
        array $valoresOriginales,
        ?int $ordenId = null,
        array $extra = []
    ): RegistroActividad {
        $actuales = $this->extraerAtributosLimpios($modelo->getAttributes());
        $originales = $this->extraerAtributosLimpios($valoresOriginales);

        $cambios = [];
        foreach ($actuales as $campo => $valorNuevo) {
            $valorViejo = $originales[$campo] ?? null;
            if ($this->valoresSonDiferentes($valorViejo, $valorNuevo)) {
                $cambios[$campo] = [
                    'antes' => $valorViejo,
                    'despues' => $valorNuevo,
                ];
            }
        }

        $payload = array_merge([
            'tipo_cambio' => 'update',
            'modelo' => class_basename($modelo),
            'modelo_id' => $modelo->getKey(),
            'cambios' => $cambios,
        ], $extra);

        return $this->registrarActividad($accion, $descripcion, $ordenId, $payload);
    }

    /**
     * Registra la eliminacion de un modelo capturando un snapshot completo
     * del registro borrado.
     */
    protected function registrarEliminacion(
        string $accion,
        string $descripcion,
        Model $modelo,
        ?int $ordenId = null,
        array $extra = []
    ): RegistroActividad {
        $atributos = $this->extraerAtributosLimpios($modelo->getAttributes());
        $cambios = [];
        foreach ($atributos as $campo => $valor) {
            $cambios[$campo] = ['antes' => $valor, 'despues' => null];
        }

        $payload = array_merge([
            'tipo_cambio' => 'delete',
            'modelo' => class_basename($modelo),
            'modelo_id' => $modelo->getKey(),
            'cambios' => $cambios,
        ], $extra);

        return $this->registrarActividad($accion, $descripcion, $ordenId, $payload);
    }

    /**
     * Filtra atributos sensibles y normaliza valores no escalares
     * para que puedan serializarse a JSON sin perder informacion util.
     */
    private function extraerAtributosLimpios(array $atributos): array
    {
        $limpio = [];
        foreach ($atributos as $campo => $valor) {
            if (in_array($campo, $this->camposExcluidosLog, true)) {
                continue;
            }
            if (is_object($valor)) {
                if (method_exists($valor, '__toString')) {
                    $valor = (string) $valor;
                } elseif (method_exists($valor, 'toArray')) {
                    $valor = $valor->toArray();
                } else {
                    $valor = json_decode(json_encode($valor), true);
                }
            }
            $limpio[$campo] = $valor;
        }
        return $limpio;
    }

    /**
     * Compara dos valores tolerando diferencias de tipo trivial
     * (string vs int, null vs '') para evitar registrar cambios falsos.
     */
    private function valoresSonDiferentes($a, $b): bool
    {
        if ($a === $b) {
            return false;
        }
        if (($a === null || $a === '') && ($b === null || $b === '')) {
            return false;
        }
        if (is_scalar($a) && is_scalar($b)) {
            return (string) $a !== (string) $b;
        }
        return $a != $b;
    }
}
