<?php

namespace Database\Seeders;

use App\Models\LandingPagina;
use Illuminate\Database\Seeder;

class LandingPaginasSeeder extends Seeder
{
    public function run()
    {
        // Página Inicio
        LandingPagina::create([
            'slug' => 'inicio',
            'titulo' => 'Inicio',
            'contenido' => [
                'hero_title' => '¡Hola Colombia!<br>Tu Negocio, <span>Nuestra causa.</span>',
                'hero_subtitle' => 'Te abrimos la puerta a un ecosistema de <strong>crecimiento sin límites.</strong>',
                'hero_benefits' => '🛒 Tienda Online al Instante|🚚 Pasarela de pago y Logística integrada|🎉 Festivales Exclusivos para nuestros miembros',
                'hero_btn_primary' => 'Ver Nuestros Planes',
                'hero_btn_secondary' => 'Regístrate ahora',
                'offer_subtitle' => '¡ACTIVA TU MARCA EN LO DIGITAL Y PRESENCIAL!',
                'offer_title' => '¿Emprendes o lideras una fundación?',
                'offer_description' => 'Con nosotros no solo accedes a una plataforma…<br />¡Abres la puerta a un <strong>ecosistema completo</strong> que se enfoca en atraer visitantes y potenciales clientes para ti.',
                'card1_icon' => 'bi bi-laptop',
                'card1_title' => 'Tu Tienda Online ¡Lista para Vender!',
                'card1_description' => 'Lánzala en minutos y gestiona pagos seguros, envíos y estadísticas para el control total de tus ventas. 🧾',
                'card2_icon' => 'fa-solid fa-handshake',
                'card2_title' => 'Tu Marca Siempre Visible.',
                'card2_description' => '¡No solo vendes, tu marca conecta! ❤️ Creamos contenido audiovisual en nuestras redes que cuenta la historia de algunos de nuestros miembros, impulsando su reconocimiento. <strong>Somos tu aliado para hacerte conocer.</strong>',
                'card3_icon' => 'bi bi-star-fill',
                'card3_title' => '¡Brilla en nuestros eventos exclusivos!',
                'card3_description' => 'Lleva tu marca al siguiente nivel en festivales comerciales. Tu tienda digital y física se fusionan para que solo te preocupes por vender, nosotros nos encargamos del resto y, <strong>¡nosotros ponemos la infraestructura!</strong> 🏠',
                'stats_title' => '¡Únete a la <strong>primera membresía</strong> en Colombia para Emprendedores y <strong>Fundaciones</strong>!',
                'stats_subtitle' => 'Juntos, ya impactamos a más de:',
                'stats_count' => 134,
                'stats_label' => 'VISITANTES TOTALES',
                'imagen_seccion_principal' => 'images/imagen1.png',
                'features_subtitle' => 'UN ECOSISTEMA COMPLETO Y LISTO PARA TI',
                'features_title' => 'Sencillo, Rápido y Poderoso',
                'features_intro' => 'Tu tienda,<br />Tus eventos, tu momento<br />Inscríbete en la lista de espera 🚨',
                'step1_color' => 'pink',
                'step1_icon' => 'bi bi-person-plus',
                'step1_title' => 'Regístrate',
                'step1_description' => 'Da el primer paso para impulsar tu marca. Crea tu cuenta <strong>sin costo</strong> y configúrala en minutos.',
                'step2_color' => 'blue',
                'step2_icon' => 'bi bi-check-lg',
                'step2_title' => 'Activa',
                'step2_description' => 'Elige tu <strong>membresía</strong> activála ✅ y accede a múltiples beneficios. <strong>¡Así podrás enfocarte solo en vender!</strong>',
                'step3_color' => 'pink',
                'step3_icon' => 'bi bi-calendar',
                'step3_title' => 'Agéndate',
                'step3_description' => '<strong>Alquila tu espacio</strong> en nuestros festivales de comercio. ¡Tú solo vende, nosotros hacemos el resto!',
                'bt_subtitle' => 'Nacimos para transformar el emprendimiento y el impacto social en Colombia.',
                'bt_title' => '"Emprender no debe ser imposible, debe ser accesible."',
                'bt_description' => '<strong>Better Together:</strong> <em>Tu ecosistema único</em> y accesible <em>en Colombia.</em> Con nuestra <strong>plataforma y eventos exclusivos</strong>, te damos las herramientas para que solo te enfoques en tu negocio y vender.<br>¡Únete y transforma tu estrategia!',
                'imagen_better_together' => 'images/imagen2.png',
                'bt_footer' => 'Somos una inversión para tu marca',
                'access_subtitle' => 'Acceso exclusivo por inscripción anticipada',
                'access_title' => 'Todo lo que necesitas para crecer',
                'access_description' => 'El acceso a Better Together es limitado en esta fase inicial.<br><strong>¡Solo quienes se registren en la lista de espera</strong> podrán ser parte de este grupo!<br><strong>No te quedes por fuera y asegura tu lugar ahora.</strong>',
                'benefit1_title' => 'Lanza tu e-commerce en minutos',
                'benefit1_description' => 'gestiona tus pedidos, inventario y potencia tus ingresos',
                'benefit2_title' => 'Pagos rápidos y directos',
                'benefit2_description' => 'Pagos ágiles y seguros usando nuestra plataforma en lo digital y en festivales.',
                'benefit3_title' => 'Logística Optimizada para ti',
                'benefit3_description' => 'Cotiza y gestiona tus despachos con las transportadoras, ¡todo desde nuestra plataforma!',
                'benefit4_title' => 'Presencia en eventos físicos',
                'benefit4_description' => 'Participa en festivales de comercio con afluencia de público. Conecta con nuevos clientes e impulsa tus ventas.',
                'imagen_beneficios' => 'images/imagen3.png',
                'benefits_cta_text' => '¡VER PLANES!',
                'cta_title' => '¿Listo para llevar tu negocio al siguiente Nivel?',
                'cta_description' => 'Forma parte de nuestra comunidad y prepárate para crecer con nosotros.',
            ],
            'activo' => true,
        ]);

        // Página Planes
        LandingPagina::create([
            'slug' => 'planes',
            'titulo' => 'Planes',
            'contenido' => [
                'hero_subtitle' => 'NUESTROS PLANES',
                'hero_title' => 'Elige el plan perfecto para tu negocio',
                'hero_description' => 'Desde emprendedores que inician hasta marcas consolidadas, tenemos el plan ideal para ti.',
            ],
            'activo' => true,
        ]);

        // Página Contacto
        LandingPagina::create([
            'slug' => 'contacto',
            'titulo' => 'Contacto',
            'contenido' => [
                'hero_title' => '¿Listo para llevar tu negocio al siguiente Nivel?',
                'hero_description' => 'Forma parte de nuestra comunidad. <span class="highlight3">Regístrate en la lista de espera hoy y prepárate para crecer</span> con nosotros.',
            ],
            'activo' => true,
        ]);

        // Página Sobre Nosotros
        LandingPagina::create([
            'slug' => 'nosotros',
            'titulo' => 'Sobre Nosotros',
            'contenido' => [
                'hero_subtitle' => 'NUESTRA HISTORIA',
                'hero_title' => 'Sobre Nosotros',
                'hero_description' => 'Conoce la historia detrás de <strong>Better Together</strong> y nuestra misión de transformar el emprendimiento en Colombia.',
                'mision_titulo' => 'Nuestra Misión',
                'mision_texto' => '<p>En <strong>Better Together</strong>, creemos que emprender no debe ser imposible, debe ser accesible. Nuestra misión es proporcionar a emprendedores y fundaciones las herramientas necesarias para crecer y prosperar en el mercado colombiano.</p><p>Nacimos con la convicción de que juntos somos más fuertes. Por eso, hemos creado un ecosistema completo que combina tecnología, comunidad y oportunidades de negocio para que nuestros miembros puedan enfocarse en lo que mejor saben hacer: vender.</p>',
                'mision_imagen' => 'images/imagen1.png',
                'vision_titulo' => 'Nuestra Visión',
                'vision_texto' => '<p>Ser la plataforma líder en Colombia para el desarrollo y crecimiento de emprendimientos y fundaciones, creando un impacto positivo en la economía local y en la vida de miles de familias colombianas.</p><p>Visualizamos un futuro donde cada emprendedor tenga acceso a las mismas oportunidades que las grandes empresas: tecnología de punta, visibilidad en eventos comerciales y una comunidad que los respalde.</p>',
                'vision_imagen' => 'images/imagen2.png',
                'valores_titulo' => 'Nuestros Valores',
                'cta_titulo' => '¿Listo para ser parte de nuestra comunidad?',
                'cta_descripcion' => 'Únete a miles de emprendedores que ya están creciendo con Better Together',
                'cta_btn1_texto' => 'Ver Planes',
                'cta_btn2_texto' => 'Contáctanos',
            ],
            'activo' => true,
        ]);

        // Página Eventos
        LandingPagina::create([
            'slug' => 'eventos',
            'titulo' => 'Eventos',
            'contenido' => [
                'hero_subtitle' => 'NUESTROS EVENTOS',
                'hero_title' => 'Festivales y Ferias',
                'hero_description' => 'Participa en nuestros eventos exclusivos y conecta con miles de visitantes potenciales.',
            ],
            'activo' => true,
        ]);
    }
}
