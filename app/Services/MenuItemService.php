<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class MenuItemService
{
    private const UPLOAD_PATH = 'uploads/menu-items';

    public function crear(array $data, ?UploadedFile $imagen = null): MenuItem
    {
        $item = MenuItem::create([
            'nombre'  => $data['nombre'],
            'precio'  => (int) $data['precio'],
            'tipo_id' => (int) $data['tipo_id'],
            'activo'  => (bool) ($data['activo'] ?? true),
            'orden'   => (int) ($data['orden'] ?? 0),
        ]);

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

        $item->save();

        return $item->fresh();
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
