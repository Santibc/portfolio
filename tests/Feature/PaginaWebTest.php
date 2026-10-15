<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\TextoLanding;
use App\Models\LandingCifra;
use App\Models\LandingFoto;
use App\Models\LandingHorario;
use App\Models\LandingProducto;
use App\Models\User;
use App\Services\ImagenLandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaginaWebTest extends TestCase
{
    use RefreshDatabase;

    /** Archivos creados en public/uploads/landing durante el test (se borran al final). */
    private array $archivos = [];

    protected function tearDown(): void
    {
        $imagenes = new ImagenLandingService();
        foreach ($this->archivos as $nombre) {
            $imagenes->eliminar($nombre);
        }

        parent::tearDown();
    }

    private function conRol(?string $rol): User
    {
        $user = User::factory()->create();
        if ($rol !== null) {
            Role::findOrCreate($rol, 'web');
            $user->assignRole($rol);
        }

        return $user;
    }

    private function datosNegocio(array $cambios = []): array
    {
        return $cambios + [
            'nombre' => 'El Pan Dorado DC',
            'nombre_largo' => 'Panadería y Pastelería El Pan Dorado DC',
            'lema' => 'Calidad & Servicio',
            'propietaria' => 'Dorahaliz Corzo Quijano',
            'propietaria_cargo' => 'Fundadora',
            'telefono' => '318 865 5792',
            'whatsapp' => '57 318 865 5792',
            'whatsapp_mensaje' => 'Hola, quiero hacer un pedido.',
            'direccion' => 'Carrera 41A # 4-42',
            'barrio' => 'Primavera',
            'ciudad' => 'Bogotá D.C.',
            'instagram_url' => 'https://instagram.com/elpandoradodc',
            'horarios' => [['dias' => 'Todos los días', 'horas' => '5:30 a.m. – 9:00 p.m.']],
        ];
    }

    public function test_admin_ve_todas_las_pantallas_del_modulo(): void
    {
        $admin = $this->conRol('admin');

        foreach ([
            'pagina-web.negocio.edit', 'pagina-web.textos.edit', 'pagina-web.fotos.index',
            'pagina-web.productos.index', 'pagina-web.productos.create',
            'pagina-web.proceso.index', 'pagina-web.proceso.create',
            'pagina-web.galeria.index', 'pagina-web.galeria.create',
        ] as $ruta) {
            $this->actingAs($admin)->get(route($ruta))->assertOk();
        }
    }

    public function test_solo_el_admin_puede_editar_la_pagina(): void
    {
        $this->actingAs($this->conRol(null))->get(route('pagina-web.negocio.edit'))->assertForbidden();
        $this->actingAs($this->conRol('ventas'))->get(route('pagina-web.negocio.edit'))->assertRedirect(route('caja.index'));
        $this->actingAs($this->conRol(null))->put(route('pagina-web.negocio.update'), $this->datosNegocio())->assertForbidden();
    }

    public function test_actualizar_negocio_cambia_la_landing(): void
    {
        $this->actingAs($this->conRol('admin'))
            ->put(route('pagina-web.negocio.update'), $this->datosNegocio(['telefono' => '601 555 1234']))
            ->assertRedirect(route('pagina-web.negocio.edit'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('landing_ajustes', ['telefono' => '6015551234', 'whatsapp' => '573188655792', 'propietaria_cargo' => 'Fundadora']);
        $this->assertSame(1, LandingHorario::count());

        $this->get('/')
            ->assertOk()
            ->assertSee('601 555 1234')
            ->assertSee('5:30 a.m. – 9:00 p.m.')
            ->assertSee('https://instagram.com/elpandoradodc', false);
    }

    public function test_actualizar_textos_con_cursiva_cifras_y_tarjetas(): void
    {
        $textos = [];
        foreach (TextoLanding::cases() as $clave) {
            $textos[$clave->value] = $clave->porDefecto();
        }
        $textos[TextoLanding::HeroTitulo->value] = 'El mejor pan *de Bogotá*';

        $this->actingAs($this->conRol('admin'))
            ->put(route('pagina-web.textos.update'), [
                'textos' => $textos,
                'cifras' => [['valor' => 25, 'prefijo' => '+', 'sufijo' => 'años', 'texto' => 'horneando en el barrio']],
                'valores' => [['icono' => 'heart', 'titulo' => 'Cariño', 'texto' => 'Todo se hace con amor.']],
            ])
            ->assertRedirect(route('pagina-web.textos.edit'))
            ->assertSessionHasNoErrors();

        $this->assertSame(1, LandingCifra::count());

        $this->get('/')
            ->assertOk()
            ->assertSee('El mejor pan <em>de Bogotá</em>', false)
            ->assertSee('horneando en el barrio')
            ->assertSee('Cariño');
    }

    public function test_un_icono_fuera_de_la_lista_se_rechaza(): void
    {
        $textos = [];
        foreach (TextoLanding::cases() as $clave) {
            $textos[$clave->value] = $clave->porDefecto();
        }

        $this->actingAs($this->conRol('admin'))
            ->put(route('pagina-web.textos.update'), [
                'textos' => $textos,
                'valores' => [['icono' => 'bomba', 'titulo' => 'X', 'texto' => 'Y']],
            ])
            ->assertSessionHasErrors('valores.0.icono');
    }

    public function test_producto_con_foto_se_crea_reemplaza_y_elimina_con_su_archivo(): void
    {
        $admin = $this->conRol('admin');

        $this->actingAs($admin)->post(route('pagina-web.productos.store'), [
            'titulo' => 'Almojábanas',
            'descripcion' => 'Recién salidas del horno.',
            'activo' => '1',
            'imagen' => UploadedFile::fake()->image('almojabanas.jpg', 2400, 1800),
        ])->assertRedirect(route('pagina-web.productos.index'))->assertSessionHasNoErrors();

        $producto = LandingProducto::firstOrFail();
        $this->archivos[] = $primera = $producto->imagen;

        $ruta = public_path(ImagenLandingService::CARPETA.'/'.$primera);
        $this->assertStringEndsWith('.webp', $primera);
        $this->assertFileExists($ruta);
        $this->assertSame(1600, getimagesize($ruta)[0], 'Se reduce a 1600 px de ancho');
        $this->assertFileExists(public_path(ImagenLandingService::CARPETA.'/'.ImagenLandingService::nombreVariante($primera)));

        $this->get('/')->assertSee('Almojábanas')->assertSee($producto->imagen_url, false);

        sleep(1); // el nombre lleva time(): otra foto en el mismo segundo tendría el mismo nombre
        $this->actingAs($admin)->put(route('pagina-web.productos.update', $producto), [
            'titulo' => 'Almojábanas',
            'descripcion' => 'Recién salidas del horno.',
            'activo' => '0',
            'imagen' => UploadedFile::fake()->image('nueva.png', 900, 900),
        ])->assertSessionHasNoErrors();

        $producto->refresh();
        $this->archivos[] = $producto->imagen;
        $this->assertNotSame($primera, $producto->imagen);
        $this->assertFileDoesNotExist($ruta, 'La foto anterior se borra');
        $this->assertFalse($producto->activo);
        $this->get('/')->assertDontSee('Almojábanas');

        $rutaNueva = public_path(ImagenLandingService::CARPETA.'/'.$producto->imagen);
        $this->actingAs($admin)->delete(route('pagina-web.productos.destroy', $producto))->assertRedirect();
        $this->assertDatabaseCount('landing_productos', 0);
        $this->assertFileDoesNotExist($rutaNueva);
    }

    public function test_foto_fija_se_sube_y_se_quita(): void
    {
        $admin = $this->conRol('admin');
        $foto = LandingFoto::where('slot', 'local')->firstOrFail();

        $this->actingAs($admin)->put(route('pagina-web.fotos.update', $foto), [
            'imagen' => UploadedFile::fake()->image('fachada.jpg', 1000, 1250),
            'alt' => 'Fachada de la panadería',
        ])->assertRedirect(route('pagina-web.fotos.index'))->assertSessionHasNoErrors();

        $foto->refresh();
        $this->archivos[] = $foto->imagen;
        $this->assertTrue($foto->hasImagen());
        $this->get('/')->assertSee('Fachada de la panadería');

        $ruta = public_path(ImagenLandingService::CARPETA.'/'.$foto->imagen);
        $this->actingAs($admin)->delete(route('pagina-web.fotos.destroy', $foto))->assertRedirect();
        $this->assertNull($foto->refresh()->imagen);
        $this->assertFileDoesNotExist($ruta);
    }

    public function test_un_archivo_que_no_es_imagen_se_rechaza_en_su_propia_bolsa_de_errores(): void
    {
        $foto = LandingFoto::where('slot', 'hero')->firstOrFail();

        $this->actingAs($this->conRol('admin'))
            ->from(route('pagina-web.fotos.index'))
            ->put(route('pagina-web.fotos.update', $foto), [
                'imagen' => UploadedFile::fake()->create('menu.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrorsIn('foto_hero', 'imagen');

        $this->assertNull($foto->refresh()->imagen);
    }

    public function test_galeria_sin_foto_se_muestra_como_espacio_reservado(): void
    {
        $this->actingAs($this->conRol('admin'))->post(route('pagina-web.galeria.store'), [
            'titulo' => 'Nuestro horno',
        ])->assertSessionHasNoErrors();

        $this->get('/')->assertSee('Nuestro horno')->assertSee('Próximamente');
    }

    public function test_seeder_de_la_landing_no_pisa_lo_editado(): void
    {
        File::ensureDirectoryExists(public_path(ImagenLandingService::CARPETA));
        LandingHorario::create(['dias' => 'Solo sábados', 'horas' => '8 a 12', 'orden' => 0]);

        $this->seed(\Database\Seeders\LandingSeeder::class);

        foreach ([...\App\Models\LandingProducto::pluck('imagen'), ...\App\Models\LandingPaso::pluck('imagen'),
                  ...\App\Models\LandingGaleriaFoto::pluck('imagen'), ...LandingFoto::pluck('imagen')] as $nombre) {
            if ($nombre) {
                $this->archivos[] = $nombre;
            }
        }

        $this->assertSame(['Solo sábados'], LandingHorario::pluck('dias')->all());
        $this->assertSame(6, LandingProducto::count());
        $this->assertNull(LandingFoto::where('slot', 'local')->value('imagen'));
    }
}
