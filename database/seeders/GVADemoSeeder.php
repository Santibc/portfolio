<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GVADemoSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario estudiante de prueba
        $estudiante = User::firstOrCreate(
            ['email' => 'estudiante@gva.com'],
            [
                'name' => 'Estudiante Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $estudiante->assignRole('Estudiante');

        // Crear categorías de ejemplo
        $categorias = [
            [
                'name' => 'Desarrollo Web',
                'slug' => 'desarrollo-web',
                'description' => 'Aprende las tecnologías más demandadas para crear sitios y aplicaciones web modernas.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Marketing Digital',
                'slug' => 'marketing-digital',
                'description' => 'Domina las estrategias de marketing online para hacer crecer cualquier negocio.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Diseño Gráfico',
                'slug' => 'diseno-grafico',
                'description' => 'Desarrolla tu creatividad y aprende a usar las herramientas de diseño profesional.',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($categorias as $catData) {
            $categoria = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );

            // Crear cursos para cada categoría
            $this->createCoursesForCategory($categoria);
        }

        $this->command->info('Datos de demostración GVA creados exitosamente.');
    }

    private function createCoursesForCategory(Category $categoria): void
    {
        $cursosPorCategoria = [
            'desarrollo-web' => [
                [
                    'title' => 'Fundamentos de HTML y CSS',
                    'slug' => 'fundamentos-html-css',
                    'description' => 'Aprende las bases del desarrollo web con HTML5 y CSS3. Construye tu primera página web desde cero.',
                    'order' => 1,
                    'is_published' => true,
                    'videos' => [
                        ['title' => 'Introducción al Desarrollo Web', 'description' => 'Qué es el desarrollo web y por qué aprenderlo', 'order' => 1],
                        ['title' => 'Estructura básica de HTML', 'description' => 'Etiquetas, atributos y estructura de un documento HTML', 'order' => 2],
                        ['title' => 'Trabajando con CSS', 'description' => 'Selectores, propiedades y estilos básicos', 'order' => 3],
                        ['title' => 'Diseño Responsive', 'description' => 'Media queries y diseño adaptable', 'order' => 4],
                    ],
                ],
                [
                    'title' => 'JavaScript Esencial',
                    'slug' => 'javascript-esencial',
                    'description' => 'Domina JavaScript desde los fundamentos hasta conceptos avanzados como promesas y async/await.',
                    'order' => 2,
                    'is_published' => true,
                    'videos' => [
                        ['title' => 'Variables y Tipos de Datos', 'description' => 'Fundamentos de JavaScript', 'order' => 1],
                        ['title' => 'Funciones y Scope', 'description' => 'Cómo funcionan las funciones en JS', 'order' => 2],
                        ['title' => 'Arrays y Objetos', 'description' => 'Estructuras de datos esenciales', 'order' => 3],
                        ['title' => 'DOM Manipulation', 'description' => 'Interactúa con el HTML desde JavaScript', 'order' => 4],
                        ['title' => 'Eventos y Formularios', 'description' => 'Manejo de eventos del usuario', 'order' => 5],
                    ],
                ],
                [
                    'title' => 'Laravel para Principiantes',
                    'slug' => 'laravel-principiantes',
                    'description' => 'Construye aplicaciones web robustas con el framework PHP más popular.',
                    'order' => 3,
                    'is_published' => true,
                    'videos' => [
                        ['title' => 'Instalación y Configuración', 'description' => 'Preparando el entorno de desarrollo', 'order' => 1],
                        ['title' => 'Rutas y Controladores', 'description' => 'El flujo de una petición en Laravel', 'order' => 2],
                        ['title' => 'Blade Templates', 'description' => 'Sistema de plantillas de Laravel', 'order' => 3],
                        ['title' => 'Eloquent ORM', 'description' => 'Trabajando con la base de datos', 'order' => 4],
                        ['title' => 'Autenticación', 'description' => 'Sistema de login y registro', 'order' => 5],
                        ['title' => 'Proyecto Final', 'description' => 'Construye tu primera aplicación completa', 'order' => 6],
                    ],
                ],
            ],
            'marketing-digital' => [
                [
                    'title' => 'Introducción al Marketing Digital',
                    'slug' => 'intro-marketing-digital',
                    'description' => 'Conoce el panorama del marketing digital y las estrategias más efectivas.',
                    'order' => 1,
                    'is_published' => true,
                    'videos' => [
                        ['title' => 'Qué es el Marketing Digital', 'description' => 'Conceptos fundamentales', 'order' => 1],
                        ['title' => 'Canales de Marketing', 'description' => 'SEO, SEM, Redes Sociales y Email', 'order' => 2],
                        ['title' => 'Métricas y KPIs', 'description' => 'Cómo medir el éxito de tus campañas', 'order' => 3],
                    ],
                ],
                [
                    'title' => 'SEO desde Cero',
                    'slug' => 'seo-desde-cero',
                    'description' => 'Aprende a posicionar tu sitio web en los primeros lugares de Google.',
                    'order' => 2,
                    'is_published' => true,
                    'videos' => [
                        ['title' => 'Fundamentos de SEO', 'description' => 'Cómo funcionan los buscadores', 'order' => 1],
                        ['title' => 'Keyword Research', 'description' => 'Encuentra las palabras clave correctas', 'order' => 2],
                        ['title' => 'SEO On-Page', 'description' => 'Optimización dentro de tu sitio', 'order' => 3],
                        ['title' => 'Link Building', 'description' => 'Estrategias de enlaces externos', 'order' => 4],
                    ],
                ],
            ],
            'diseno-grafico' => [
                [
                    'title' => 'Principios del Diseño',
                    'slug' => 'principios-diseno',
                    'description' => 'Aprende los fundamentos del diseño gráfico: composición, color y tipografía.',
                    'order' => 1,
                    'is_published' => true,
                    'videos' => [
                        ['title' => 'Teoría del Color', 'description' => 'Psicología y combinación de colores', 'order' => 1],
                        ['title' => 'Tipografía', 'description' => 'Familias tipográficas y jerarquía', 'order' => 2],
                        ['title' => 'Composición Visual', 'description' => 'Balance, contraste y alineación', 'order' => 3],
                    ],
                ],
                [
                    'title' => 'Diseño con Figma',
                    'slug' => 'diseno-figma',
                    'description' => 'Domina Figma, la herramienta de diseño colaborativo más popular.',
                    'order' => 2,
                    'is_published' => true,
                    'videos' => [
                        ['title' => 'Introducción a Figma', 'description' => 'Interfaz y herramientas básicas', 'order' => 1],
                        ['title' => 'Componentes y Variantes', 'description' => 'Diseño escalable y reutilizable', 'order' => 2],
                        ['title' => 'Prototipos Interactivos', 'description' => 'Crea prototipos navegables', 'order' => 3],
                        ['title' => 'Colaboración en Equipo', 'description' => 'Trabaja con otros diseñadores', 'order' => 4],
                    ],
                ],
            ],
        ];

        $cursos = $cursosPorCategoria[$categoria->slug] ?? [];

        foreach ($cursos as $cursoData) {
            $videos = $cursoData['videos'];
            unset($cursoData['videos']);

            $curso = Course::firstOrCreate(
                ['slug' => $cursoData['slug']],
                array_merge($cursoData, ['category_id' => $categoria->id])
            );

            // Crear videos para el curso
            foreach ($videos as $videoData) {
                Video::firstOrCreate(
                    [
                        'course_id' => $curso->id,
                        'title' => $videoData['title'],
                    ],
                    array_merge($videoData, [
                        'course_id' => $curso->id,
                        'video_path' => 'demo/sample-video.mp4', // Video de ejemplo
                        'duration_seconds' => rand(180, 900), // 3-15 minutos
                    ])
                );
            }
        }
    }
}
