<?php

namespace App\Services\Catalogo;

use App\Models\Producto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductoService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function crear(array $data, ?UploadedFile $imagen = null): Producto
    {
        if ($imagen) {
            $data['imagen_path'] = $this->guardarImagen($imagen);
        }

        return Producto::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function actualizar(Producto $producto, array $data, ?UploadedFile $imagen = null): Producto
    {
        if ($imagen) {
            $this->borrarImagenPrevia($producto);
            $data['imagen_path'] = $this->guardarImagen($imagen);
        }

        $producto->update($data);

        return $producto->refresh();
    }

    public function eliminar(Producto $producto): void
    {
        $this->borrarImagenPrevia($producto);
        $producto->delete();
    }

    private function guardarImagen(UploadedFile $imagen): string
    {
        $directorio = public_path('uploads/productos');

        if (! File::isDirectory($directorio)) {
            File::makeDirectory($directorio, 0755, true);
        }

        $extension = strtolower((string) $imagen->extension());
        $validas = ['png', 'jpg', 'jpeg', 'webp'];

        if (! in_array($extension, $validas, true)) {
            abort(422, 'Formato de imagen no soportado.');
        }

        $nombre = 'prod-'.Str::uuid()->toString().'.'.$extension;
        $imagen->move($directorio, $nombre);

        return 'uploads/productos/'.$nombre;
    }

    private function borrarImagenPrevia(Producto $producto): void
    {
        if (! $producto->imagen_path) {
            return;
        }

        $absoluta = public_path($producto->imagen_path);
        if (File::exists($absoluta)) {
            File::delete($absoluta);
        }
    }
}
