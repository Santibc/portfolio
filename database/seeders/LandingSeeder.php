<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\IconoLanding;
use App\Models\LandingCifra;
use App\Models\LandingFoto;
use App\Models\LandingGaleriaFoto;
use App\Models\LandingHorario;
use App\Models\LandingPaso;
use App\Models\LandingProducto;
use App\Models\LandingValor;
use App\Services\ImagenLandingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Contenido inicial (genérico) de la landing y sus fotos provisionales de Unsplash
 * (public/images/landing → public/uploads/landing). Solo llena lo que esté vacío:
 * se puede correr de nuevo sin pisar lo que el cliente ya editó en "Página web".
 */
class LandingSeeder extends Seeder
{
    private const FOTOS_STOCK = 'images/landing/';

    public function __construct(private ImagenLandingService $imagenes)
    {
    }

    public function run(): void
    {
        $this->sembrarLista(LandingHorario::class, [
            ['dias' => 'Lunes a sábado', 'horas' => '6:00 a.m. – 8:00 p.m.'],
            ['dias' => 'Domingos y festivos', 'horas' => '7:00 a.m. – 2:00 p.m.'],
        ]);

        $this->sembrarLista(LandingCifra::class, [
            ['valor' => 40, 'prefijo' => '+', 'sufijo' => null, 'texto' => 'productos horneados a diario'],
            ['valor' => 100, 'prefijo' => null, 'sufijo' => '%', 'texto' => 'hecho en nuestro horno'],
            ['valor' => 6, 'prefijo' => null, 'sufijo' => 'a.m.', 'texto' => 'sale la primera horneada'],
        ]);

        $this->sembrarLista(LandingValor::class, [
            ['icono' => IconoLanding::Medalla, 'titulo' => 'Calidad', 'texto' => 'Ingredientes seleccionados y recetas cuidadas en cada horneada.'],
            ['icono' => IconoLanding::Apreton, 'titulo' => 'Servicio', 'texto' => 'Atención cálida y cercana, como en casa.'],
            ['icono' => IconoLanding::Amanecer, 'titulo' => 'Frescura', 'texto' => 'Horneamos varias veces al día para que todo llegue fresco.'],
            ['icono' => IconoLanding::Trigo, 'titulo' => 'Tradición', 'texto' => 'El sabor del pan de barrio de siempre, hecho con paciencia.'],
        ]);

        $this->sembrarLista(LandingProducto::class, [
            ['titulo' => 'Pan artesanal', 'descripcion' => 'Masas de fermentación lenta, corteza crujiente y miga suave.', 'foto' => 'pan-artesanal-1000.webp'],
            ['titulo' => 'Pan tradicional', 'descripcion' => 'Pan de sal, mogollas, roscones y los clásicos de la mañana.', 'foto' => 'pan-tradicional-1000.webp'],
            ['titulo' => 'Pastelería', 'descripcion' => 'Hojaldres, croissants y pasteles recién salidos del horno.', 'foto' => 'pasteleria-1000.webp'],
            ['titulo' => 'Tortas por encargo', 'descripcion' => 'Cumpleaños, bautizos y celebraciones, a tu gusto.', 'foto' => 'tortas-1000.webp'],
            ['titulo' => 'Desayunos y café', 'descripcion' => 'Un buen café con algo recién horneado para empezar el día.', 'foto' => 'desayunos-1000.webp'],
            ['titulo' => 'Galletas y postres', 'descripcion' => 'Antojos dulces para compartir o llevar de regalo.', 'foto' => 'galletas-1000.webp'],
        ]);

        $this->sembrarLista(LandingPaso::class, [
            ['titulo' => 'Ingredientes seleccionados', 'texto' => 'Harinas de calidad, mantequilla, huevos frescos y nada de afanes. Todo empieza por lo que ponemos en la mesa de trabajo.', 'foto' => 'paso-ingredientes-1000.webp'],
            ['titulo' => 'Amasado a mano', 'texto' => 'Cada masa se trabaja con paciencia y respeta su tiempo de reposo, para lograr una miga suave y aireada.', 'foto' => 'paso-amasado-1000.webp'],
            ['titulo' => 'Horneado lento', 'texto' => 'Temperatura y tiempo justos para esa corteza dorada que cruje al partirla. Horneamos varias veces al día.', 'foto' => 'paso-horneado-1000.webp'],
            ['titulo' => 'Directo a tu mesa', 'texto' => 'Del horno a la vitrina y de la vitrina a tu casa, todavía calientico. Así de sencillo, así de rico.', 'foto' => 'paso-mesa-1000.webp'],
        ]);

        // Sin foto = espacio reservado para las fotos reales del local.
        $this->sembrarLista(LandingGaleriaFoto::class, [
            ['titulo' => 'Panadería con estantes de baguettes y canastas de pan', 'foto' => 'galeria-local-1200.webp'],
            ['titulo' => 'Fachada del local', 'foto' => null],
            ['titulo' => 'Vitrina con hojaldres y panes dorados', 'foto' => 'galeria-vitrina-1200.webp'],
            ['titulo' => 'Rollos de canela recién horneados', 'foto' => 'galeria-canela-900.webp'],
            ['titulo' => 'Nuestro equipo', 'foto' => null],
            ['titulo' => 'Baguettes enharinadas sobre una mesa oscura', 'foto' => 'galeria-baguettes-1200.webp'],
            ['titulo' => 'Pan artesanal tajado sobre una tabla de madera', 'foto' => 'galeria-tajado-1200.webp'],
            ['titulo' => 'Interior del local', 'foto' => null],
        ]);

        $this->sembrarFotosFijas([
            'hero' => 'hero-1200.webp',
            'hero_mini' => 'hero-croissant-640.webp',
            'historia' => 'historia-1100.webp',
            'encargos' => 'encargos-1600.webp',
            // 'local' queda vacío: es la foto real que debe mandar el cliente.
        ]);
    }

    /**
     * @param class-string<Model> $modelo
     * @param list<array<string, mixed>> $filas  (`foto` = archivo en public/images/landing)
     */
    private function sembrarLista(string $modelo, array $filas): void
    {
        if ($modelo::query()->exists()) {
            return;
        }

        foreach ($filas as $orden => $fila) {
            $foto = $fila['foto'] ?? null;
            unset($fila['foto']);
            $elemento = $modelo::create($fila + ['orden' => $orden]);

            if ($foto !== null) {
                $this->asignarFoto($elemento, $foto);
            }
        }
    }

    /** @param array<string, string> $archivos slot => archivo */
    private function sembrarFotosFijas(array $archivos): void
    {
        foreach (LandingFoto::all() as $foto) {
            $archivo = $archivos[$foto->slot->value] ?? null;
            if ($archivo !== null && ! $foto->hasImagen()) {
                $this->asignarFoto($foto, $archivo);
            }
        }
    }

    private function asignarFoto(Model $elemento, string $archivo): void
    {
        $ruta = public_path(self::FOTOS_STOCK.$archivo);
        if (! File::exists($ruta)) {
            $this->command?->warn("No existe {$ruta}; se deja sin foto.");

            return;
        }

        $elemento->update([
            'imagen' => $this->imagenes->guardarDesdeRuta($ruta, $elemento->prefijoImagen(), (int) $elemento->getKey()),
        ]);
    }
}
