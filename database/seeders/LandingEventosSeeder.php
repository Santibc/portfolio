<?php

namespace Database\Seeders;

use App\Models\LandingEvento;
use Illuminate\Database\Seeder;

class LandingEventosSeeder extends Seeder
{
    public function run()
    {
        LandingEvento::create([
            'slug' => 'festival-emprendedores-bogota-2025',
            'titulo' => 'Festival de Emprendedores Bogotá 2025',
            'extracto' => 'El evento más grande para emprendedores en la capital. Conecta con miles de visitantes y potenciales clientes.',
            'contenido' => '<p>El <strong>Festival de Emprendedores Bogotá 2025</strong> es el evento más esperado del año para la comunidad emprendedora de Colombia.</p>
<p>Durante tres días, tendrás la oportunidad de:</p>
<ul>
    <li>Exhibir tus productos a más de 10,000 visitantes</li>
    <li>Conectar con otros emprendedores y fundaciones</li>
    <li>Participar en talleres y conferencias</li>
    <li>Recibir mentoría de expertos en negocios</li>
</ul>
<p>¡No te pierdas esta oportunidad única de hacer crecer tu negocio!</p>',
            'imagen' => 'images/bg01.jpg',
            'fecha' => '15-17 Marzo, 2025',
            'hora' => '9:00 AM - 8:00 PM',
            'ubicacion' => 'Corferias, Bogotá',
            'precio' => 'Desde $150.000 COP',
            'estado' => 'proximo',
            'incluye' => [
                'Stand de 3x3 metros',
                'Mesa y 2 sillas',
                'Conexión eléctrica',
                'Wifi gratuito',
                'Publicidad en redes sociales',
                'Acceso a conferencias'
            ],
            'galeria' => [
                'images/imagen1.png',
                'images/imagen2.png',
                'images/imagen3.png'
            ],
            'orden' => 1,
            'activo' => true,
        ]);

        LandingEvento::create([
            'slug' => 'feria-moda-sostenible-medellin',
            'titulo' => 'Feria de Moda Sostenible Medellín',
            'extracto' => 'Un espacio dedicado a la moda consciente y sostenible. Ideal para marcas de ropa, accesorios y belleza eco-friendly.',
            'contenido' => '<p>La <strong>Feria de Moda Sostenible</strong> reúne a los mejores emprendedores del sector textil y de accesorios comprometidos con el medio ambiente.</p>
<p>Este evento está diseñado para:</p>
<ul>
    <li>Marcas de moda sostenible y slow fashion</li>
    <li>Artesanos y diseñadores independientes</li>
    <li>Emprendedores de cosméticos naturales</li>
    <li>Proyectos de economía circular</li>
</ul>
<p>Conecta con un público consciente que valora la calidad y el impacto positivo.</p>',
            'imagen' => 'images/bg02.jpg',
            'fecha' => '22-23 Abril, 2025',
            'hora' => '10:00 AM - 7:00 PM',
            'ubicacion' => 'Plaza Mayor, Medellín',
            'precio' => 'Desde $120.000 COP',
            'estado' => 'proximo',
            'incluye' => [
                'Stand personalizable',
                'Iluminación especial',
                'Material promocional',
                'Fotografía profesional',
                'Networking con compradores'
            ],
            'galeria' => [
                'images/bg01.jpg',
                'images/bg03.jpg'
            ],
            'orden' => 2,
            'activo' => true,
        ]);

        LandingEvento::create([
            'slug' => 'mercado-artesanal-cartagena',
            'titulo' => 'Mercado Artesanal Cartagena',
            'extracto' => 'Celebra la cultura y tradición colombiana en el corazón de la ciudad amurallada con productos artesanales únicos.',
            'contenido' => '<p>El <strong>Mercado Artesanal de Cartagena</strong> es un evento que celebra la riqueza cultural y artesanal de Colombia.</p>
<p>Perfecto para:</p>
<ul>
    <li>Artesanos y productores locales</li>
    <li>Emprendedores de gastronomía tradicional</li>
    <li>Marcas de productos típicos colombianos</li>
    <li>Fundaciones que trabajan con comunidades</li>
</ul>
<p>Un evento que atrae tanto a turistas nacionales como internacionales.</p>',
            'imagen' => 'images/bg03.jpg',
            'fecha' => '10-12 Mayo, 2025',
            'hora' => '11:00 AM - 9:00 PM',
            'ubicacion' => 'Centro Histórico, Cartagena',
            'precio' => 'Desde $100.000 COP',
            'estado' => 'proximo',
            'incluye' => [
                'Espacio en zona peatonal',
                'Carpa con branding',
                'Seguridad 24/7',
                'Promoción turística',
                'Acceso a público internacional'
            ],
            'galeria' => [],
            'orden' => 3,
            'activo' => true,
        ]);

        LandingEvento::create([
            'slug' => 'expo-tecnologia-startups-cali',
            'titulo' => 'Expo Tecnología y Startups Cali',
            'extracto' => 'El punto de encuentro para startups tecnológicas, innovadores y emprendedores digitales del suroccidente colombiano.',
            'contenido' => '<p>La <strong>Expo Tecnología y Startups</strong> es el evento líder en innovación y emprendimiento digital en el Valle del Cauca.</p>
<p>Diseñado para:</p>
<ul>
    <li>Startups de base tecnológica</li>
    <li>Desarrolladores de apps y software</li>
    <li>Emprendedores fintech y edtech</li>
    <li>Innovadores en inteligencia artificial</li>
</ul>
<p>Conecta con inversionistas, mentores y clientes potenciales del sector tech.</p>',
            'imagen' => 'images/imagen1.png',
            'fecha' => '5-6 Junio, 2025',
            'hora' => '8:00 AM - 6:00 PM',
            'ubicacion' => 'Centro de Eventos Valle del Pacífico, Cali',
            'precio' => 'Desde $200.000 COP',
            'estado' => 'proximo',
            'incluye' => [
                'Booth tecnológico equipado',
                'Pantalla LED incluida',
                'Pitch ante inversionistas',
                'Networking exclusivo',
                'Cobertura de medios tech'
            ],
            'galeria' => [],
            'orden' => 4,
            'activo' => true,
        ]);

        LandingEvento::create([
            'slug' => 'festival-navidad-emprendedora-2024',
            'titulo' => 'Festival Navidad Emprendedora 2024',
            'extracto' => 'El evento perfecto para cerrar el año vendiendo tus productos en la temporada de mayor demanda.',
            'contenido' => '<p>El <strong>Festival Navidad Emprendedora</strong> fue un éxito rotundo con más de 15,000 visitantes durante el fin de semana.</p>
<p>Participaron más de 200 emprendedores de diferentes categorías y fue una excelente oportunidad para generar ventas de fin de año.</p>
<p>¡Gracias a todos los que participaron! Nos vemos en la edición 2025.</p>',
            'imagen' => 'images/imagen2.png',
            'fecha' => '14-15 Diciembre, 2024',
            'hora' => '10:00 AM - 10:00 PM',
            'ubicacion' => 'Parque de la 93, Bogotá',
            'precio' => null,
            'estado' => 'finalizado',
            'incluye' => [
                'Stand navideño decorado',
                'Música en vivo',
                'Actividades para niños',
                'Food trucks'
            ],
            'galeria' => [],
            'orden' => 5,
            'activo' => true,
        ]);
    }
}
