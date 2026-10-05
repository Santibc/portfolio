<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MenuItem;
use App\Models\MenuItemComponente;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MenuItemService
{
    private const UPLOAD_PATH = 'uploads/menu-items';

    public function crear(array $data, ?UploadedFile $imagen = null): MenuItem
    {
        $item = DB::transaction(function () use ($data) {
            $item = MenuItem::create([
                'nombre'  => $data['nombre'],
                'precio'  => (int) $data['precio'],
                'tipo_id' => (int) $data['tipo_id'],
                'activo'  => (bool) ($data['activo'] ?? true),
                'orden'   => (int) ($data['orden'] ?? 0),
            ]);

            $this->sincronizarComponentes($item, $data['componentes'] ?? []);

            return $item;
        });

        if ($imagen !== null) {
            $item->imagen = $this->guardarImagen($imagen, $item->id);
            $item->save();
        }

        return $item->fresh();
    }

    public function actualizar(MenuItem $item, array $data, ?UploadedFile $imagen = null): MenuItem
    {
        $item->fill([
            'nombre'  => $data['nombre'],
            'precio'  => (int) $data['precio'],
            'tipo_id' => (int) $data['tipo_id'],
            'activo'  => (bool) ($data['activo'] ?? false),
            'orden'   => (int) ($data['orden'] ?? $item->orden),
        ]);

        if ($imagen !== null) {
            $this->eliminarImagenAnterior($item);
            $item->imagen = $this->guardarImagen($imagen, $item->id);
        }

        DB::transaction(function () use ($item, $data) {
            $item->save();
            $this->sincronizarComponentes($item, $data['componentes'] ?? []);
        });

        return $item->fresh();
    }

    /**
     * Reemplaza los componentes de inventario del item. Las ventas ya hechas no dependen de esto
     * (sus descuentos quedaron en el kardex), así que recrearlos es seguro.
     *
     * @param  array<int,array{nombre:string,cantidad:numeric,productos:array<int,int>}>  $componentes
     */
    private function sincronizarComponentes(MenuItem $item, array $componentes): void
    {
        MenuItemComponente::where('menu_item_id', $item->id)->delete();

        foreach (array_values($componentes) as $orden => $datos) {
            $productos = array_values(array_unique(array_map('intval', $datos['productos'] ?? [])));
            if ($productos === []) {
                continue;
            }

            $componente = $item->componentes()->create([
                'nombre'   => trim((string) $datos['nombre']),
                'cantidad' => round((float) $datos['cantidad'], 2),
                'orden'    => $orden,
            ]);
            $componente->opciones()->sync($productos);
        }
    }

    public function eliminar(MenuItem $item): void
    {
        $item->delete();
    }

    private function guardarImagen(UploadedFile $file, int $itemId): string
    {
        $path = public_path(self::UPLOAD_PATH);
        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $nombre = 'menu_item_' . $itemId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $nombre);

        return $nombre;
    }

    private function eliminarImagenAnterior(MenuItem $item): void
    {
        if (! $item->imagen) {
            return;
        }

        $path = public_path(self::UPLOAD_PATH . '/' . $item->imagen);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
