<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingService;
use App\Models\LandingGalleryImage;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Str;

class ManzerContentSeeder extends Seeder
{
    public function run(): void
    {
        // ========== SERVICES ==========
        $services = [
            [
                'icon_class' => 'bi bi-tree',
                'title' => 'Tala en Altura',
                'slug' => 'tala-en-altura',
                'description' => 'Talamos empleando sistemas de control de caida para evitar roturas y danos materiales por posibles caidas.',
                'short_description' => 'Sistemas profesionales de control de caida para tala segura de arboles en zonas urbanas y rurales.',
                'long_description' => '<h3>Tala profesional con maxima seguridad</h3>
<p>En Manzer Agroforestal realizamos trabajos de tala en altura empleando las tecnicas mas avanzadas del sector. Nuestro equipo de arboristas certificados utiliza <strong>sistemas de control de caida</strong> que garantizan la seguridad tanto del personal como del entorno.</p>
<h4>¿Cuando es necesaria la tala en altura?</h4>
<ul>
<li>Arboles que suponen un riesgo para edificaciones o infraestructuras</li>
<li>Arboles enfermos o muertos con peligro de caida</li>
<li>Arboles que interfieren con lineas electricas o de comunicaciones</li>
<li>Clareo de masas forestales para prevencion de incendios</li>
<li>Urbanizaciones y zonas residenciales con acceso limitado</li>
</ul>
<h4>Nuestro metodo de trabajo</h4>
<p>Utilizamos tecnicas de <strong>trepa con cuerda</strong> y equipos de corte profesionales. Cada operacion se planifica meticulosamente, evaluando la direccion de caida, los obstaculos del entorno y las condiciones meteorologicas.</p>
<p>Contamos con gruas y cestas elevadoras para los trabajos que lo requieran, garantizando siempre el minimo impacto en el entorno.</p>',
                'image_path' => 'images/gallery/tala-en-altura.jpg',
                'order' => 1,
            ],
            [
                'icon_class' => 'bi bi-scissors',
                'title' => 'Poda en Altura',
                'slug' => 'poda-en-altura',
                'description' => 'Mediante sistema de trepa. Donde las cestas y elevadoras no pueden acceder, empleamos sistemas de control de caida.',
                'short_description' => 'Poda tecnica mediante trepa con cuerda en arboles de gran porte y dificil acceso.',
                'long_description' => '<h3>Poda tecnica en altura</h3>
<p>La poda en altura es una de nuestras especialidades. Mediante <strong>sistema de trepa con cuerda</strong>, nuestros arboristas acceden a las copas de los arboles mas altos para realizar podas de formacion, mantenimiento y saneamiento.</p>
<h4>Tipos de poda que realizamos</h4>
<ul>
<li><strong>Poda de formacion:</strong> Para guiar el crecimiento del arbol desde su juventud</li>
<li><strong>Poda de mantenimiento:</strong> Eliminacion de ramas secas, cruzadas o mal orientadas</li>
<li><strong>Poda de seguridad:</strong> Reduccion de peso en ramas que suponen un riesgo</li>
<li><strong>Poda de aclareo:</strong> Mejora de la aireacion y la entrada de luz</li>
</ul>
<p>Trabajamos con herramientas profesionales y seguimos los protocolos de <strong>arboricultura moderna</strong>, respetando siempre la biologia del arbol para garantizar su salud a largo plazo.</p>',
                'image_path' => 'images/gallery/poda-en-altura.jpg',
                'order' => 2,
            ],
            [
                'icon_class' => 'bi bi-hurricane',
                'title' => 'Desbroces',
                'slug' => 'desbroces',
                'description' => 'Desbroces en taludes con sistemas de anclaje y linea de vida para trabajos en pendientes pronunciadas.',
                'short_description' => 'Desbroces mecanicos y manuales en taludes, parcelas y terrenos forestales con sistemas de seguridad.',
                'long_description' => '<h3>Desbroces profesionales</h3>
<p>Realizamos desbroces en todo tipo de terrenos, desde parcelas urbanas hasta taludes de dificil acceso. Nuestro equipo cuenta con <strong>sistemas de anclaje y linea de vida</strong> para trabajar con total seguridad en pendientes pronunciadas.</p>
<h4>Servicios de desbroce</h4>
<ul>
<li>Desbroce mecanico con desbrozadora profesional</li>
<li>Desbroce en taludes con sistemas de seguridad</li>
<li>Limpieza de parcelas y terrenos abandonados</li>
<li>Desbroce selectivo respetando especies protegidas</li>
<li>Desbroce para prevencion de incendios forestales</li>
</ul>
<p>Utilizamos equipos de ultima generacion que permiten un trabajo rapido y eficiente, minimizando el impacto sobre el terreno y la vegetacion que se desea conservar.</p>',
                'image_path' => 'images/gallery/desbroce-talud.jpg',
                'order' => 3,
            ],
            [
                'icon_class' => 'bi bi-fire',
                'title' => 'Prevencion de Incendios',
                'slug' => 'prevencion-de-incendios',
                'description' => 'Limpieza del sotobosque y creacion de cortafuegos y podas para evitar la continuidad vertical de los arboles.',
                'short_description' => 'Creacion de cortafuegos, fajas auxiliares y limpieza del sotobosque para la prevencion de incendios forestales.',
                'long_description' => '<h3>Prevencion de incendios forestales</h3>
<p>La prevencion de incendios es una de las actividades mas importantes que realizamos. Trabajamos en la <strong>creacion de cortafuegos</strong>, la limpieza del sotobosque y la poda de arboles para evitar la continuidad vertical del fuego.</p>
<h4>Actuaciones que realizamos</h4>
<ul>
<li><strong>Cortafuegos:</strong> Apertura y mantenimiento de franjas cortafuegos</li>
<li><strong>Fajas auxiliares:</strong> Zonas de transicion con vegetacion controlada</li>
<li><strong>Limpieza de sotobosque:</strong> Eliminacion de matorral y vegetacion baja</li>
<li><strong>Poda de arboles:</strong> Eliminacion de ramas bajas para evitar continuidad vertical</li>
<li><strong>Gestion de restos:</strong> Triturado y eliminacion de restos vegetales</li>
</ul>
<p>Colaboramos con ayuntamientos y entidades publicas en planes de prevencion de incendios, adaptando nuestras actuaciones a las normativas vigentes.</p>',
                'image_path' => 'images/gallery/bosque-panoramica.jpg',
                'order' => 4,
            ],
            [
                'icon_class' => 'bi bi-signpost-2',
                'title' => 'Trabajo en Carreteras',
                'slug' => 'trabajo-en-carreteras',
                'description' => 'Limpieza de carreteras y cunetas para saneamiento y prevencion de incendios en vias publicas.',
                'short_description' => 'Mantenimiento de margenes de carretera, limpieza de cunetas y gestion de vegetacion en vias publicas.',
                'long_description' => '<h3>Mantenimiento de carreteras y vias</h3>
<p>Realizamos trabajos de <strong>limpieza y mantenimiento de margenes de carretera</strong>, cunetas y zonas adyacentes a vias publicas. Estos trabajos son esenciales para la seguridad vial y la prevencion de incendios.</p>
<h4>Trabajos que realizamos</h4>
<ul>
<li>Limpieza de cunetas y drenajes</li>
<li>Desbroce de margenes de carretera</li>
<li>Tala y poda de arboles junto a vias</li>
<li>Retirada de arboles caidos por temporales</li>
<li>Mantenimiento de zonas ajardinadas en rotondas y medianas</li>
</ul>
<p>Contamos con la senalizacion vial necesaria y cumplimos todas las normativas de seguridad para trabajos en carreteras.</p>',
                'image_path' => 'images/gallery/trabajo-carretera.jpg',
                'order' => 5,
            ],
            [
                'icon_class' => 'bi bi-x-diamond',
                'title' => 'Retirada de Arboles',
                'slug' => 'retirada-de-arboles',
                'description' => 'Retirada de arboles muertos con riesgo de caida en zonas urbanas y forestales.',
                'short_description' => 'Retirada segura de arboles muertos, danados o con riesgo de caida en entornos urbanos y naturales.',
                'long_description' => '<h3>Retirada segura de arboles</h3>
<p>Los arboles muertos o gravemente danados representan un <strong>peligro real</strong> para personas, vehiculos e infraestructuras. En Manzer Agroforestal realizamos la retirada de estos arboles con total seguridad.</p>
<h4>Situaciones en las que intervenimos</h4>
<ul>
<li>Arboles muertos con riesgo de caida inminente</li>
<li>Arboles danados por tormentas o temporales</li>
<li>Arboles afectados por enfermedades o plagas</li>
<li>Arboles que interfieren con obras o construcciones</li>
<li>Emergencias por caida de arboles</li>
</ul>
<p>Evaluamos cada situacion de forma individual, determinando la mejor tecnica de retirada para garantizar la seguridad del entorno. Disponemos de servicio de <strong>emergencia 24 horas</strong> para situaciones criticas.</p>',
                'image_path' => 'images/gallery/trabajo-forestal-1.jpg',
                'order' => 6,
            ],
        ];

        foreach ($services as $serviceData) {
            $page = Page::create([
                'name' => $serviceData['title'],
                'slug' => 'servicio-' . $serviceData['slug'],
                'url_path' => '/servicios/' . $serviceData['slug'],
                'page_type' => 'service',
            ]);

            LandingService::create(array_merge($serviceData, [
                'page_id' => $page->id,
                'is_active' => true,
            ]));
        }

        // ========== GALLERY IMAGES ==========
        $galleryImages = [
            ['image_path' => 'images/gallery/tala-en-altura.jpg', 'alt_text' => 'Tala en altura con sistema de trepa', 'caption' => 'Tala controlada en zona urbana', 'category' => 'tala'],
            ['image_path' => 'images/gallery/poda-en-altura.jpg', 'alt_text' => 'Poda en altura mediante trepa', 'caption' => 'Poda de mantenimiento en arbol centenario', 'category' => 'poda'],
            ['image_path' => 'images/gallery/desbroce-talud.jpg', 'alt_text' => 'Desbroce en talud con sistema de seguridad', 'caption' => 'Desbroce en pendiente pronunciada', 'category' => 'desbroce'],
            ['image_path' => 'images/gallery/trabajo-carretera.jpg', 'alt_text' => 'Limpieza de margenes de carretera', 'caption' => 'Mantenimiento de cunetas en carretera comarcal', 'category' => 'carreteras'],
            ['image_path' => 'images/gallery/bosque-panoramica.jpg', 'alt_text' => 'Vista panoramica de bosque gestionado', 'caption' => 'Bosque tras trabajos de prevencion', 'category' => 'desbroce'],
            ['image_path' => 'images/gallery/trabajo-forestal-1.jpg', 'alt_text' => 'Equipo de trabajo en operacion de tala', 'caption' => 'Operacion con grua en zona residencial', 'category' => 'tala'],
            ['image_path' => 'images/gallery/trabajo-forestal-2.jpg', 'alt_text' => 'Arborista en altura con equipo de seguridad', 'caption' => 'Trepa profesional con arneses certificados', 'category' => 'poda'],
            ['image_path' => 'images/gallery/trabajo-forestal-3.jpg', 'alt_text' => 'Trabajo de poda con motosierra profesional', 'caption' => 'Corte de precision en rama de gran diametro', 'category' => 'poda'],
            ['image_path' => 'images/gallery/trabajo-forestal-4.jpg', 'alt_text' => 'Operacion de tala controlada', 'caption' => 'Tala con sistema de control direccional', 'category' => 'tala'],
            ['image_path' => 'images/gallery/trabajo-forestal-5.jpg', 'alt_text' => 'Trabajo forestal en altura', 'caption' => 'Intervencion en arbol de gran porte', 'category' => 'tala'],
            ['image_path' => 'images/gallery/trabajo-forestal-6.jpg', 'alt_text' => 'Equipo de trabajo Manzer Agroforestal', 'caption' => 'Nuestro equipo en accion', 'category' => 'poda'],
        ];

        foreach ($galleryImages as $i => $img) {
            LandingGalleryImage::create(array_merge($img, [
                'order' => $i + 1,
                'is_active' => true,
            ]));
        }

        // ========== BLOG CATEGORIES ==========
        $categories = [
            ['name' => 'Consejos Forestales', 'slug' => 'consejos-forestales', 'description' => 'Consejos y recomendaciones para el cuidado de arboles y zonas forestales'],
            ['name' => 'Noticias', 'slug' => 'noticias', 'description' => 'Novedades y noticias de Manzer Agroforestal'],
            ['name' => 'Prevencion', 'slug' => 'prevencion', 'description' => 'Articulos sobre prevencion de incendios y seguridad forestal'],
            ['name' => 'Medio Ambiente', 'slug' => 'medio-ambiente', 'description' => 'Articulos sobre sostenibilidad y medio ambiente'],
        ];

        $catIds = [];
        foreach ($categories as $cat) {
            $c = BlogCategory::create(array_merge($cat, ['is_active' => true]));
            $catIds[$cat['slug']] = $c->id;
        }

        // ========== BLOG TAGS ==========
        $tags = [];
        foreach (['arboricultura', 'seguridad', 'incendios', 'poda', 'tala', 'sostenibilidad', 'Lleida', 'normativa'] as $tagName) {
            $tags[$tagName] = BlogTag::create(['name' => $tagName, 'slug' => Str::slug($tagName)])->id;
        }

        // ========== BLOG POSTS ==========
        $author = User::first();
        $authorId = $author ? $author->id : 1;

        $posts = [
            [
                'title' => 'Cuando es necesario talar un arbol: senales que debes conocer',
                'slug' => 'cuando-talar-arbol-senales',
                'excerpt' => 'Aprende a identificar las senales que indican que un arbol necesita ser talado por seguridad. Te explicamos los criterios profesionales que utilizamos.',
                'body' => '<p>Los arboles son elementos fundamentales de nuestro entorno, pero en ocasiones pueden representar un <strong>riesgo para la seguridad</strong> de personas e infraestructuras. Saber identificar cuando un arbol necesita ser talado es crucial.</p>

<h2>Senales de alerta</h2>

<h3>1. Inclinacion excesiva</h3>
<p>Un arbol que se inclina mas de 15 grados respecto a la vertical puede indicar problemas en las raices o en el suelo. Si la inclinacion es reciente o progresiva, es una senal clara de peligro.</p>

<h3>2. Ramas secas en la copa</h3>
<p>Cuando mas del 50% de la copa presenta ramas secas, el arbol puede estar muriendo. Las ramas secas son fragiles y pueden caer sin aviso, especialmente durante tormentas o vientos fuertes.</p>

<h3>3. Hongos en la base del tronco</h3>
<p>La presencia de hongos (setas) en la base del tronco o en las raices superficiales suele indicar <strong>pudricion interna</strong>. Esto debilita la estructura del arbol y aumenta el riesgo de caida.</p>

<h3>4. Cavidades y huecos</h3>
<p>Los huecos en el tronco reducen la resistencia mecanica del arbol. Aunque algunos arboles pueden sobrevivir con cavidades, un profesional debe evaluar si la estructura es segura.</p>

<h3>5. Danos en las raices</h3>
<p>Obras cercanas, compactacion del suelo o cortes de raices pueden comprometer la estabilidad del arbol. Si se han realizado obras en un radio de 3-5 metros del tronco, es recomendable una evaluacion profesional.</p>

<h2>¿Que hacer si detectas alguna de estas senales?</h2>
<p>Lo mas importante es <strong>no intentar talar el arbol por tu cuenta</strong>. La tala de arboles es un trabajo peligroso que requiere equipamiento y formacion especifica. Contacta con profesionales como Manzer Agroforestal para una evaluacion gratuita.</p>

<blockquote>
<p>Un arbol evaluado a tiempo puede evitar accidentes graves. No esperes a que sea demasiado tarde.</p>
</blockquote>

<p>En Manzer Agroforestal realizamos evaluaciones de riesgo de arboles y, cuando es necesario, procedemos a la tala controlada con los maximos estandares de seguridad.</p>',
                'category_id' => $catIds['consejos-forestales'],
                'featured_image' => 'images/gallery/tala-en-altura.jpg',
                'featured_image_alt' => 'Profesional realizando tala en altura',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'is_featured' => true,
                'tags' => ['tala', 'seguridad', 'arboricultura'],
            ],
            [
                'title' => 'Prevencion de incendios forestales: guia practica para propietarios',
                'slug' => 'prevencion-incendios-forestales-guia',
                'excerpt' => 'Descubre las medidas esenciales que todo propietario de terreno forestal debe tomar para prevenir incendios. Normativa, obligaciones y consejos practicos.',
                'body' => '<p>La prevencion de incendios forestales no es solo responsabilidad de las administraciones publicas. Los <strong>propietarios de terrenos forestales y parcelas</strong> tienen obligaciones legales y una responsabilidad directa en la proteccion del monte.</p>

<h2>Obligaciones legales en Cataluna</h2>
<p>La legislacion catalana establece que los propietarios de fincas forestales y urbanas colindantes con zonas de bosque deben mantener una <strong>franja de proteccion</strong> alrededor de sus edificaciones:</p>
<ul>
<li>Franja de 25 metros alrededor de edificaciones en zona forestal</li>
<li>Limpieza de vegetacion seca y matorral</li>
<li>Poda de arboles hasta 1/3 de su altura</li>
<li>Eliminacion de ramas que esten a menos de 3 metros de una edificacion</li>
</ul>

<h2>Medidas preventivas recomendadas</h2>

<h3>Gestion del sotobosque</h3>
<p>El sotobosque es el principal combustible en un incendio forestal. Mantenerlo limpio y controlado reduce drasticamente el riesgo de propagacion.</p>

<h3>Creacion de cortafuegos</h3>
<p>Las franjas cortafuegos son zonas desprovistas de vegetacion que actuan como barrera contra el avance del fuego. Su anchura y ubicacion deben ser estudiadas por profesionales.</p>

<h3>Poda de arboles</h3>
<p>La poda de las ramas bajas evita la <strong>continuidad vertical</strong> del fuego, impidiendo que las llamas trepen desde el suelo hasta las copas de los arboles.</p>

<h2>¿Como podemos ayudarte?</h2>
<p>En Manzer Agroforestal realizamos todos los trabajos de prevencion de incendios: desbroces, cortafuegos, podas y gestion de restos vegetales. Trabajamos con ayuntamientos y particulares en toda la provincia de Lleida.</p>',
                'category_id' => $catIds['prevencion'],
                'featured_image' => 'images/gallery/bosque-panoramica.jpg',
                'featured_image_alt' => 'Bosque gestionado para prevencion de incendios',
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'is_featured' => false,
                'tags' => ['incendios', 'seguridad', 'normativa', 'Lleida'],
            ],
            [
                'title' => 'La poda en altura: tecnicas y seguridad en el trabajo arboreo',
                'slug' => 'poda-altura-tecnicas-seguridad',
                'excerpt' => 'Conoce las tecnicas profesionales de poda en altura y por que es fundamental contar con profesionales cualificados para este tipo de trabajos.',
                'body' => '<p>La poda en altura es una de las disciplinas mas exigentes dentro de la arboricultura. Requiere <strong>formacion especializada</strong>, equipamiento de seguridad certificado y una profunda comprension de la biologia de los arboles.</p>

<h2>Tecnicas de trepa</h2>
<p>Existen diferentes tecnicas de acceso a la copa de los arboles:</p>

<h3>Trepa con cuerda (SRT y DRT)</h3>
<p>El sistema de trepa con cuerda es el metodo mas versatil y menos agresivo con el arbol. Permite acceder a cualquier punto de la copa sin necesidad de maquinaria pesada.</p>
<ul>
<li><strong>SRT (Single Rope Technique):</strong> Ascenso por cuerda simple, ideal para arboles altos y rectos</li>
<li><strong>DRT (Double Rope Technique):</strong> Cuerda doble, ofrece mayor seguridad y versatilidad de movimiento</li>
</ul>

<h3>Cesta elevadora</h3>
<p>Para arboles en zonas accesibles, la cesta elevadora permite un posicionamiento rapido y comodo. Sin embargo, no siempre es posible utilizarla por limitaciones de acceso.</p>

<h2>Equipamiento de seguridad</h2>
<p>Todo arborista profesional debe utilizar:</p>
<ul>
<li>Arnes de trepa certificado EN 813</li>
<li>Casco con proteccion auditiva y facial</li>
<li>Mosquetones y conectores certificados</li>
<li>Cuerdas semistaticas de arboricultura</li>
<li>Motosierra con dispositivo anticorte</li>
</ul>

<p>En Manzer Agroforestal todos nuestros arboristas estan formados y certificados para trabajos en altura. La seguridad es nuestra prioridad absoluta.</p>',
                'category_id' => $catIds['consejos-forestales'],
                'featured_image' => 'images/gallery/poda-en-altura.jpg',
                'featured_image_alt' => 'Arborista realizando poda en altura con sistema de trepa',
                'status' => 'published',
                'published_at' => now()->subDays(20),
                'is_featured' => false,
                'tags' => ['poda', 'arboricultura', 'seguridad'],
            ],
            [
                'title' => 'Manzer Agroforestal amplia sus servicios en la comarca del Segria',
                'slug' => 'manzer-amplia-servicios-segria',
                'excerpt' => 'Nos complace anunciar que ampliamos nuestra zona de actuacion y servicios para dar cobertura a mas municipios de la comarca del Segria.',
                'body' => '<p>En Manzer Agroforestal estamos en constante crecimiento. Nos complace anunciar que <strong>ampliamos nuestros servicios</strong> para dar cobertura a mas municipios de la comarca del Segria y comarcas limotrofes.</p>

<h2>Nuevos servicios disponibles</h2>
<p>Ademas de nuestros servicios habituales de tala, poda y desbroces, ahora ofrecemos:</p>
<ul>
<li>Gestion integral de zonas verdes municipales</li>
<li>Planes de prevencion de incendios para urbanizaciones</li>
<li>Mantenimiento de jardines y parques publicos</li>
<li>Asesoramiento tecnico en arboricultura urbana</li>
</ul>

<h2>Compromiso con la comarca</h2>
<p>Desde nuestra sede en <strong>Menarguens</strong>, servimos a toda la provincia de Lleida y zonas limotrofes. Nuestro conocimiento del terreno y las especies locales nos permite ofrecer soluciones adaptadas a cada situacion.</p>

<p>Si necesitas servicios forestales o de mantenimiento de zonas verdes, no dudes en contactarnos. Estaremos encantados de atenderte y prepararte un presupuesto sin compromiso.</p>',
                'category_id' => $catIds['noticias'],
                'featured_image' => 'images/gallery/trabajo-forestal-2.jpg',
                'featured_image_alt' => 'Equipo Manzer Agroforestal en campo',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'is_featured' => true,
                'tags' => ['Lleida', 'sostenibilidad'],
            ],
            [
                'title' => 'Sostenibilidad en trabajos forestales: nuestro compromiso con el medio ambiente',
                'slug' => 'sostenibilidad-trabajos-forestales',
                'excerpt' => 'En Manzer Agroforestal la sostenibilidad no es una palabra, es una forma de trabajar. Descubre como minimizamos nuestro impacto ambiental.',
                'body' => '<p>La sostenibilidad es uno de los pilares fundamentales de Manzer Agroforestal. Cada trabajo que realizamos se ejecuta pensando en el <strong>impacto ambiental</strong> y en la conservacion del ecosistema.</p>

<h2>Nuestras practicas sostenibles</h2>

<h3>Gestion de residuos</h3>
<p>Los restos vegetales de nuestros trabajos se gestionan de forma responsable. Siempre que es posible, realizamos el <strong>triturado in situ</strong> para su aprovechamiento como acolchado o biomasa.</p>

<h3>Maquinaria eficiente</h3>
<p>Utilizamos maquinaria de ultima generacion con <strong>motores de bajas emisiones</strong> y mantenimiento preventivo para minimizar el consumo de combustible y las emisiones contaminantes.</p>

<h3>Respeto por la biodiversidad</h3>
<p>Antes de cada intervencion, evaluamos la presencia de <strong>fauna protegida</strong> (nidos, refugios) y especies vegetales de interes. Adaptamos nuestro calendario de trabajo para respetar los periodos de nidificacion y reproduccion.</p>

<h3>Formacion continua</h3>
<p>Nuestro equipo recibe formacion continua en <strong>buenas practicas ambientales</strong> y en las ultimas tecnicas de arboricultura sostenible.</p>

<blockquote>
<p>Creemos que el trabajo forestal bien hecho es la mejor herramienta para la conservacion de nuestros bosques.</p>
</blockquote>',
                'category_id' => $catIds['medio-ambiente'],
                'featured_image' => 'images/gallery/trabajo-forestal-3.jpg',
                'featured_image_alt' => 'Trabajo forestal sostenible',
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'is_featured' => false,
                'tags' => ['sostenibilidad', 'arboricultura'],
            ],
        ];

        foreach ($posts as $postData) {
            $postTags = $postData['tags'];
            unset($postData['tags']);

            $page = Page::create([
                'name' => $postData['title'],
                'slug' => 'blog-' . $postData['slug'],
                'url_path' => '/blog/' . $postData['slug'],
                'page_type' => 'blog',
            ]);

            $postData['author_id'] = $authorId;
            $postData['page_id'] = $page->id;
            $postData['reading_time'] = max(1, (int) ceil(str_word_count(strip_tags($postData['body'])) / 200));

            $post = BlogPost::create($postData);

            $tagIds = [];
            foreach ($postTags as $tagName) {
                if (isset($tags[$tagName])) {
                    $tagIds[] = $tags[$tagName];
                }
            }
            $post->tags()->sync($tagIds);
        }

        $this->command->info('Manzer content seeded successfully!');
    }
}
