<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TextoLanding;
use App\Models\LandingAjuste;
use App\Models\LandingCifra;
use App\Models\LandingFoto;
use App\Models\LandingHorario;
use App\Models\LandingTexto;
use App\Models\LandingValor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/** Cambios al contenido de la landing desde el módulo "Página web". */
final class PaginaWebService
{
    public function __construct(private ImagenLandingService $imagenes)
    {
    }

    /**
     * @param array<string, mixed> $datos
     * @param list<array{dias: string, horas: string}> $horarios
     */
    public function actualizarNegocio(array $datos, array $horarios, int $usuarioId): void
    {
        DB::transaction(function () use ($datos, $horarios, $usuarioId) {
            LandingAjuste::actual()->update($datos + ['updated_by' => $usuarioId]);
            $this->reemplazarLista(LandingHorario::class, $horarios);
        });
    }

    /**
     * @param array<string, string> $textos clave => valor
     * @param list<array<string, mixed>> $cifras
     * @param list<array<string, mixed>> $valores
     */
    public function actualizarTextos(array $textos, array $cifras, array $valores, int $usuarioId): void
    {
        DB::transaction(function () use ($textos, $cifras, $valores, $usuarioId) {
            foreach (TextoLanding::cases() as $clave) {
                LandingTexto::updateOrCreate(
                    ['clave' => $clave->value],
                    ['valor' => trim($textos[$clave->value]), 'updated_by' => $usuarioId],
                );
            }
            $this->reemplazarLista(LandingCifra::class, $cifras);
            $this->reemplazarLista(LandingValor::class, $valores);
        });
    }

    public function actualizarFoto(LandingFoto $foto, ?UploadedFile $imagen, ?string $alt): void
    {
        if ($imagen !== null) {
            $this->reemplazarImagen($foto, $imagen);
        }
        $foto->alt = $alt;
        $foto->save();
    }

    public function quitarFoto(LandingFoto $foto): void
    {
        $this->imagenes->eliminar($foto->imagen);
        $foto->update(['imagen' => null]);
    }

    /**
     * Productos, pasos o fotos de galería: modelos con `orden` (SeOrdena) e `imagen` (TieneImagenLanding).
     *
     * @template T of Model
     * @param class-string<T> $modelo
     * @param array<string, mixed> $datos
     * @return T
     */
    public function crearElemento(string $modelo, array $datos, ?UploadedFile $imagen): Model
    {
        $datos['orden'] ??= $modelo::siguienteOrden();
        $elemento = $modelo::create($datos);

        if ($imagen !== null) {
            $this->reemplazarImagen($elemento, $imagen);
        }

        return $elemento;
    }

    /** @param array<string, mixed> $datos */
    public function actualizarElemento(Model $elemento, array $datos, ?UploadedFile $imagen, bool $quitarImagen): void
    {
        if (array_key_exists('orden', $datos) && $datos['orden'] === null) {
            unset($datos['orden']); // posición vacía = conserva la que tenía
        }

        if ($imagen !== null) {
            $this->reemplazarImagen($elemento, $imagen);
        } elseif ($quitarImagen) {
            $this->imagenes->eliminar($elemento->imagen);
            $datos['imagen'] = null;
        }

        $elemento->update($datos);
    }

    public function eliminarElemento(Model $elemento): void
    {
        $this->imagenes->eliminar($elemento->imagen);
        $elemento->delete();
    }

    /** Primero se guarda la nueva y luego se borra la anterior: si falla, no se pierde la que había. */
    private function reemplazarImagen(Model $elemento, UploadedFile $imagen): void
    {
        $anterior = $elemento->imagen;
        $elemento->imagen = $this->imagenes->guardar($imagen, $elemento->prefijoImagen(), (int) $elemento->getKey());
        $elemento->save();
        $this->imagenes->eliminar($anterior);
    }

    /**
     * Listas cortas que se editan completas en un formulario (horario, cifras, valores).
     *
     * @param class-string<Model> $modelo
     * @param list<array<string, mixed>> $filas
     */
    private function reemplazarLista(string $modelo, array $filas): void
    {
        $modelo::query()->delete();
        foreach (array_values($filas) as $orden => $fila) {
            $modelo::create($fila + ['orden' => $orden]);
        }
    }
}
