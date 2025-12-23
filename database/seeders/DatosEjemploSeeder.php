<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Subcontrata;
use App\Models\Trabajador;
use App\Models\Cuadrilla;
use App\Models\Obra;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatosEjemploSeeder extends Seeder
{
    public function run(): void
    {
        // Crear clientes de ejemplo
        $clientes = [
            [
                'tipo' => 'publico',
                'nombre_comercial' => 'ADIF Alta Velocidad',
                'razon_social' => 'Administrador de Infraestructuras Ferroviarias',
                'cif' => 'Q2801660H',
                'direccion' => 'Calle Sor Ángela de la Cruz, 3',
                'codigo_postal' => '28020',
                'ciudad' => 'Madrid',
                'provincia' => 'Madrid',
                'telefono' => '912 007 000',
                'email' => 'info@adif.es',
                'persona_contacto' => 'Pedro Sánchez',
                'condiciones_pago' => '60 días',
                'retencion_porcentaje' => 5,
            ],
            [
                'tipo' => 'publico',
                'nombre_comercial' => 'Ayuntamiento de Manresa',
                'razon_social' => 'Ajuntament de Manresa',
                'cif' => 'P0811500J',
                'direccion' => 'Plaça Major, 1',
                'codigo_postal' => '08241',
                'ciudad' => 'Manresa',
                'provincia' => 'Barcelona',
                'telefono' => '938 782 300',
                'email' => 'ajuntament@manresa.cat',
                'persona_contacto' => 'Marta Vila',
                'condiciones_pago' => '30 días',
                'retencion_porcentaje' => 0,
            ],
            [
                'tipo' => 'privado',
                'nombre_comercial' => 'Forestal Catalunya',
                'razon_social' => 'Forestal Catalunya SL',
                'cif' => 'B12345678',
                'direccion' => 'Carrer Major, 45',
                'codigo_postal' => '08600',
                'ciudad' => 'Berga',
                'provincia' => 'Barcelona',
                'telefono' => '938 210 000',
                'email' => 'info@forestalcatalunya.com',
                'persona_contacto' => 'Joan Puig',
                'condiciones_pago' => '30 días',
                'retencion_porcentaje' => 0,
            ],
            [
                'tipo' => 'publico',
                'nombre_comercial' => 'Diputación de Barcelona',
                'razon_social' => 'Diputació de Barcelona',
                'cif' => 'P0800000B',
                'direccion' => 'Rambla de Catalunya, 126',
                'codigo_postal' => '08008',
                'ciudad' => 'Barcelona',
                'provincia' => 'Barcelona',
                'telefono' => '934 022 222',
                'email' => 'info@diba.cat',
                'persona_contacto' => 'Laura Costa',
                'condiciones_pago' => '45 días',
                'retencion_porcentaje' => 0,
            ],
        ];

        foreach ($clientes as $clienteData) {
            Cliente::create($clienteData);
        }

        // Crear subcontratas
        $subcontratas = [
            [
                'nombre' => 'Trabajos Forestales del Pirineo',
                'razon_social' => 'Trabajos Forestales del Pirineo SL',
                'cif' => 'B87654321',
                'direccion' => 'Av. Pirineus, 23',
                'telefono' => '973 350 000',
                'email' => 'info@tfpirineo.com',
                'persona_contacto' => 'Carlos Roca',
                'tarifa_hora' => 18.50,
                'tarifa_dia' => 140.00,
                'activa' => true,
                'homologada' => true,
                'fecha_homologacion' => '2024-01-15',
            ],
            [
                'nombre' => 'Jardinería Integral BCN',
                'razon_social' => 'Jardinería Integral BCN SL',
                'cif' => 'B11223344',
                'direccion' => 'Carrer Indústria, 78',
                'telefono' => '932 100 000',
                'email' => 'contacto@jardineriabcn.com',
                'persona_contacto' => 'Miguel Torres',
                'tarifa_hora' => 16.00,
                'tarifa_dia' => 120.00,
                'activa' => true,
                'homologada' => true,
                'fecha_homologacion' => '2023-06-01',
            ],
        ];

        foreach ($subcontratas as $subData) {
            Subcontrata::create($subData);
        }

        // Crear trabajadores de ejemplo
        $trabajadores = [
            ['nombre' => 'Antonio', 'apellidos' => 'García López', 'dni' => '12345678A', 'categoria_convenio' => 'Oficial 1ª', 'salario_bruto_mensual' => 2200, 'coste_hora' => 18.50],
            ['nombre' => 'Francisco', 'apellidos' => 'Martínez Ruiz', 'dni' => '23456789B', 'categoria_convenio' => 'Oficial 1ª', 'salario_bruto_mensual' => 2200, 'coste_hora' => 18.50],
            ['nombre' => 'José', 'apellidos' => 'Rodríguez Fernández', 'dni' => '34567890C', 'categoria_convenio' => 'Oficial 2ª', 'salario_bruto_mensual' => 1900, 'coste_hora' => 16.00],
            ['nombre' => 'Manuel', 'apellidos' => 'López García', 'dni' => '45678901D', 'categoria_convenio' => 'Oficial 2ª', 'salario_bruto_mensual' => 1900, 'coste_hora' => 16.00],
            ['nombre' => 'David', 'apellidos' => 'Hernández Martín', 'dni' => '56789012E', 'categoria_convenio' => 'Peón', 'salario_bruto_mensual' => 1600, 'coste_hora' => 14.00],
            ['nombre' => 'Javier', 'apellidos' => 'Sánchez Pérez', 'dni' => '67890123F', 'categoria_convenio' => 'Peón', 'salario_bruto_mensual' => 1600, 'coste_hora' => 14.00],
            ['nombre' => 'Carlos', 'apellidos' => 'Gómez Sánchez', 'dni' => '78901234G', 'categoria_convenio' => 'Oficial 1ª', 'salario_bruto_mensual' => 2200, 'coste_hora' => 18.50],
            ['nombre' => 'Miguel', 'apellidos' => 'Díaz González', 'dni' => '89012345H', 'categoria_convenio' => 'Peón', 'salario_bruto_mensual' => 1600, 'coste_hora' => 14.00],
            ['nombre' => 'Rafael', 'apellidos' => 'Muñoz Álvarez', 'dni' => '90123456I', 'categoria_convenio' => 'Oficial 2ª', 'salario_bruto_mensual' => 1900, 'coste_hora' => 16.00],
            ['nombre' => 'Pedro', 'apellidos' => 'Romero Jiménez', 'dni' => '01234567J', 'categoria_convenio' => 'Peón', 'salario_bruto_mensual' => 1600, 'coste_hora' => 14.00],
        ];

        foreach ($trabajadores as $index => $trabajadorData) {
            $trabajador = Trabajador::create([
                'tipo_relacion' => 'propio',
                'nombre' => $trabajadorData['nombre'],
                'apellidos' => $trabajadorData['apellidos'],
                'dni' => $trabajadorData['dni'],
                'email' => strtolower($trabajadorData['nombre']) . '.' . strtolower(explode(' ', $trabajadorData['apellidos'])[0]) . '@manzer.com',
                'telefono' => '6' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),
                'fecha_alta' => now()->subMonths(rand(6, 36)),
                'categoria_convenio' => $trabajadorData['categoria_convenio'],
                'salario_bruto_mensual' => $trabajadorData['salario_bruto_mensual'],
                'coste_empresa_dia' => $trabajadorData['salario_bruto_mensual'] * 1.35 / 22,
                'coste_hora' => $trabajadorData['coste_hora'],
                'activo' => true,
            ]);

            // Crear usuario para algunos trabajadores (los 3 primeros como ejemplo)
            if ($index < 3) {
                $user = User::create([
                    'name' => $trabajadorData['nombre'] . ' ' . $trabajadorData['apellidos'],
                    'email' => strtolower($trabajadorData['nombre']) . '.' . strtolower(explode(' ', $trabajadorData['apellidos'])[0]) . '@manzer.com',
                    'password' => Hash::make('password'),
                ]);
                $user->assignRole('Trabajador');
                $trabajador->update(['user_id' => $user->id]);
            }
        }

        // Crear cuadrillas
        $cuadrillas = [
            ['nombre' => 'Cuadrilla Alpha', 'capataz_id' => 1, 'descripcion' => 'Cuadrilla principal de desbroce'],
            ['nombre' => 'Cuadrilla Beta', 'capataz_id' => 7, 'descripcion' => 'Cuadrilla de tala y poda'],
        ];

        foreach ($cuadrillas as $cuadrillaData) {
            $cuadrilla = Cuadrilla::create($cuadrillaData);

            // Asignar trabajadores a cuadrillas
            if ($cuadrilla->id == 1) {
                $cuadrilla->trabajadores()->attach([1, 2, 3, 4, 5], [
                    'fecha_incorporacion' => now()->subMonths(6),
                    'activo' => true,
                ]);
            } else {
                $cuadrilla->trabajadores()->attach([7, 8, 9, 10], [
                    'fecha_incorporacion' => now()->subMonths(3),
                    'activo' => true,
                ]);
            }
        }

        // Crear obras de ejemplo
        $obras = [
            [
                'codigo' => 'OBR-2025-001',
                'nombre' => 'Desbroce L220 Calaf-Manresa',
                'descripcion' => 'Trabajos de desbroce y control de vegetación en línea ferroviaria L220',
                'cliente_id' => 1,
                'obra_tipo_id' => 1, // Desbroce
                'localidad' => 'Calaf',
                'provincia' => 'Barcelona',
                'linea' => 'L220 E1',
                'trayecto' => 'Calaf - Manresa',
                'pk_inicio' => '262+000',
                'pk_fin' => '280+000',
                'gerencia_jefatura' => 'BCN',
                'fecha_inicio_prevista' => now()->subDays(30),
                'fecha_fin_prevista' => now()->addDays(60),
                'fecha_inicio_real' => now()->subDays(25),
                'presupuesto' => 85000.00,
                'coste_estimado' => 65000.00,
                'estado' => 'en_curso',
                'encargado_id' => 3, // Usuario encargado
            ],
            [
                'codigo' => 'OBR-2025-002',
                'nombre' => 'Poda urbana Manresa',
                'descripcion' => 'Poda de arbolado urbano en diversos puntos del municipio',
                'cliente_id' => 2,
                'obra_tipo_id' => 3, // Poda
                'localidad' => 'Manresa',
                'provincia' => 'Barcelona',
                'fecha_inicio_prevista' => now()->addDays(15),
                'fecha_fin_prevista' => now()->addDays(45),
                'presupuesto' => 28000.00,
                'coste_estimado' => 22000.00,
                'estado' => 'aprobada',
                'encargado_id' => 3,
            ],
            [
                'codigo' => 'OBR-2025-003',
                'nombre' => 'Tala selectiva Berguedà',
                'descripcion' => 'Tala selectiva de pinos afectados por procesionaria',
                'cliente_id' => 3,
                'obra_tipo_id' => 2, // Tala
                'localidad' => 'Berga',
                'provincia' => 'Barcelona',
                'fecha_inicio_prevista' => now()->subDays(60),
                'fecha_fin_prevista' => now()->subDays(10),
                'fecha_inicio_real' => now()->subDays(55),
                'fecha_fin_real' => now()->subDays(5),
                'presupuesto' => 45000.00,
                'coste_estimado' => 38000.00,
                'estado' => 'finalizada',
                'encargado_id' => 3,
            ],
        ];

        foreach ($obras as $obraData) {
            $obra = Obra::create($obraData);

            // Asignar cuadrilla a la obra en curso
            if ($obra->estado == 'en_curso') {
                $obra->cuadrillas()->attach(1, [
                    'fecha_inicio' => now()->subDays(25),
                    'activo' => true,
                ]);
            }
        }
    }
}
