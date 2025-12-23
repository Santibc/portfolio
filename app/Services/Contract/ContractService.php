<?php

namespace App\Services\Contract;

use App\Models\User;
use App\Models\Proyecto;
use App\Models\Inversion;
use App\Models\PlantillaContrato;
use App\Models\AceptacionContrato;
use App\Models\CategoriaProyecto;
use Carbon\Carbon;

class ContractService
{
    /**
     * Mapeo de categorías a tipos de contrato ENUM
     */
    private const CATEGORIA_TO_TIPO_CONTRATO = [
        'Staking Agrícola' => 'inversion_staking',
        'Trading de Inversiones' => 'inversion_ear',
        'Retiro Anticipado con Penalización' => 'inversion_ear',
        'Contratos a Futuro' => 'inversion_futuros',
        'Fondo Diversificado' => 'inversion_cross_fund',
        'Farming - Asociaciones Agrícolas' => 'proyecto_agricultor',
    ];

    /**
     * Obtener el tipo de contrato ENUM para una categoría
     */
    private function getTipoContrato(CategoriaProyecto $categoria): string
    {
        return self::CATEGORIA_TO_TIPO_CONTRATO[$categoria->nombre] ?? 'inversion_staking';
    }

    /**
     * Obtener plantilla activa para una categoría
     */
    public function getActiveTemplate(CategoriaProyecto $categoria): ?PlantillaContrato
    {
        $tipoContrato = $this->getTipoContrato($categoria);

        // Buscar plantilla específica para la categoría
        $plantilla = PlantillaContrato::where('tipo_contrato', $tipoContrato)
            ->where('activo', true)
            ->orderBy('version', 'desc')
            ->first();

        // Si no existe, buscar plantilla de staking como fallback
        if (!$plantilla) {
            $plantilla = PlantillaContrato::where('tipo_contrato', 'inversion_staking')
                ->where('activo', true)
                ->orderBy('version', 'desc')
                ->first();
        }

        return $plantilla;
    }

    /**
     * Generar contrato con variables reemplazadas
     */
    public function generateContract(
        PlantillaContrato $plantilla,
        User $user,
        Proyecto $proyecto,
        float $monto
    ): string {
        $contenido = $plantilla->contenido;

        // Variables del inversionista
        $variables = [
            '{{nombre_inversionista}}' => $user->name,
            '{{email_inversionista}}' => $user->email,
            '{{documento_inversionista}}' => $user->documento_identidad ?? 'N/A',
            '{{telefono_inversionista}}' => $user->telefono ?? 'N/A',
            '{{direccion_inversionista}}' => $user->direccion ?? 'N/A',

            // Variables del proyecto
            '{{nombre_proyecto}}' => $proyecto->nombre,
            '{{codigo_proyecto}}' => $proyecto->codigo,
            '{{descripcion_proyecto}}' => $proyecto->descripcion ?? '',
            '{{categoria_proyecto}}' => $proyecto->categoria->nombre ?? 'N/A',
            '{{ubicacion_proyecto}}' => $proyecto->ubicacion ?? 'N/A',

            // Variables financieras
            '{{monto_inversion}}' => '$' . number_format($monto, 0, ',', '.'),
            '{{monto_inversion_letras}}' => $this->numeroALetras($monto),
            '{{roi_anual}}' => $proyecto->roi_anual . '%',
            '{{duracion_meses}}' => $proyecto->duracion_meses . ' meses',
            '{{periodo_dividendos}}' => ($proyecto->periodo_dividendos_dias ?? 30) . ' días',

            // Fechas
            '{{fecha_actual}}' => Carbon::now()->format('d/m/Y'),
            '{{fecha_actual_letras}}' => $this->fechaEnLetras(Carbon::now()),
            '{{fecha_vencimiento}}' => Carbon::now()->addMonths($proyecto->duracion_meses)->format('d/m/Y'),

            // Retornos estimados
            '{{retorno_mensual_estimado}}' => '$' . number_format(($monto * ($proyecto->roi_anual / 100)) / 12, 0, ',', '.'),
            '{{retorno_total_estimado}}' => '$' . number_format(($monto * ($proyecto->roi_anual / 100) * $proyecto->duracion_meses) / 12, 0, ',', '.'),

            // Plataforma
            '{{nombre_plataforma}}' => config('app.name', 'AGROMARKET'),
            '{{url_plataforma}}' => config('app.url', 'https://agromarket.com'),
        ];

        return str_replace(array_keys($variables), array_values($variables), $contenido);
    }

    /**
     * Aceptar contrato digitalmente
     */
    public function acceptContract(
        Inversion $inversion,
        PlantillaContrato $plantilla,
        string $contenidoGenerado,
        string $firmaDigital,
        string $ip,
        string $userAgent
    ): AceptacionContrato {
        // Crear registro de aceptación
        $aceptacion = AceptacionContrato::create([
            'inversion_id' => $inversion->id,
            'usuario_id' => $inversion->usuario_id,
            'plantilla_contrato_id' => $plantilla->id,
            'contenido_contrato' => $contenidoGenerado,
            'firma_digital' => $firmaDigital,
            'ip_aceptacion' => $ip,
            'user_agent' => $userAgent,
            'fecha_aceptacion' => Carbon::now(),
            'acepto_terminos' => true,
        ]);

        // Actualizar inversión con referencia al contrato
        $inversion->update([
            'contrato_id' => $plantilla->id,
        ]);

        return $aceptacion;
    }

    /**
     * Obtener contrato firmado de una inversión
     */
    public function getSignedContract(Inversion $inversion): ?AceptacionContrato
    {
        return AceptacionContrato::where('inversion_id', $inversion->id)->first();
    }

    /**
     * Convertir número a letras (simplificado para pesos colombianos)
     */
    private function numeroALetras(float $numero): string
    {
        $entero = floor($numero);

        $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $especiales = [
            11 => 'ONCE', 12 => 'DOCE', 13 => 'TRECE', 14 => 'CATORCE', 15 => 'QUINCE',
            16 => 'DIECISÉIS', 17 => 'DIECISIETE', 18 => 'DIECIOCHO', 19 => 'DIECINUEVE',
            21 => 'VEINTIUNO', 22 => 'VEINTIDÓS', 23 => 'VEINTITRÉS', 24 => 'VEINTICUATRO',
            25 => 'VEINTICINCO', 26 => 'VEINTISÉIS', 27 => 'VEINTISIETE', 28 => 'VEINTIOCHO', 29 => 'VEINTINUEVE'
        ];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

        if ($entero == 0) {
            return 'CERO PESOS M/CTE';
        }

        $resultado = '';

        // Millones
        if ($entero >= 1000000) {
            $millones = floor($entero / 1000000);
            if ($millones == 1) {
                $resultado .= 'UN MILLÓN ';
            } else {
                $resultado .= $this->convertirCentenas($millones, $unidades, $decenas, $especiales, $centenas) . ' MILLONES ';
            }
            $entero = $entero % 1000000;
        }

        // Miles
        if ($entero >= 1000) {
            $miles = floor($entero / 1000);
            if ($miles == 1) {
                $resultado .= 'MIL ';
            } else {
                $resultado .= $this->convertirCentenas($miles, $unidades, $decenas, $especiales, $centenas) . ' MIL ';
            }
            $entero = $entero % 1000;
        }

        // Centenas, decenas, unidades
        if ($entero > 0) {
            $resultado .= $this->convertirCentenas($entero, $unidades, $decenas, $especiales, $centenas);
        }

        return trim($resultado) . ' PESOS M/CTE';
    }

    /**
     * Convertir centenas a letras
     */
    private function convertirCentenas(int $numero, array $unidades, array $decenas, array $especiales, array $centenas): string
    {
        if ($numero == 100) {
            return 'CIEN';
        }

        $resultado = '';

        // Centenas
        if ($numero >= 100) {
            $resultado .= $centenas[floor($numero / 100)] . ' ';
            $numero = $numero % 100;
        }

        // Especiales (11-19, 21-29)
        if (isset($especiales[$numero])) {
            return $resultado . $especiales[$numero];
        }

        // Decenas
        if ($numero >= 10) {
            $resultado .= $decenas[floor($numero / 10)];
            $numero = $numero % 10;
            if ($numero > 0) {
                $resultado .= ' Y ';
            }
        }

        // Unidades
        if ($numero > 0) {
            $resultado .= $unidades[$numero];
        }

        return trim($resultado);
    }

    /**
     * Convertir fecha a letras
     */
    private function fechaEnLetras(Carbon $fecha): string
    {
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];

        return $fecha->day . ' de ' . $meses[$fecha->month] . ' de ' . $fecha->year;
    }

    /**
     * Generar plantilla por defecto si no existe
     */
    public function getOrCreateDefaultTemplate(CategoriaProyecto $categoria): PlantillaContrato
    {
        $plantilla = $this->getActiveTemplate($categoria);

        if (!$plantilla) {
            $tipoContrato = $this->getTipoContrato($categoria);

            $plantilla = PlantillaContrato::create([
                'codigo' => 'CONT-' . strtoupper(substr(str_replace('inversion_', '', $tipoContrato), 0, 4)) . '-' . strtoupper(substr(md5($categoria->nombre), 0, 4)),
                'nombre' => 'Contrato de Inversión - ' . $categoria->nombre,
                'tipo_contrato' => $tipoContrato,
                'version' => '1.0',
                'contenido' => $this->getDefaultContractContent(),
                'variables_requeridas' => json_encode([
                    'nombre_inversionista', 'email_inversionista', 'documento_inversionista',
                    'nombre_proyecto', 'codigo_proyecto', 'monto_inversion', 'roi_anual',
                    'duracion_meses', 'fecha_actual', 'fecha_vencimiento'
                ]),
                'activo' => true,
                'fecha_vigencia' => now(),
            ]);
        }

        return $plantilla;
    }

    /**
     * Contenido por defecto del contrato
     */
    private function getDefaultContractContent(): string
    {
        return <<<HTML
<div class="contract-content">
    <h2 class="text-center mb-4">CONTRATO DE INVERSIÓN AGRÍCOLA</h2>

    <p class="text-justify">
        En la ciudad de Bogotá D.C., a los {{fecha_actual_letras}}, entre {{nombre_plataforma}}
        (en adelante "LA PLATAFORMA") y <strong>{{nombre_inversionista}}</strong>, identificado(a) con
        documento de identidad número <strong>{{documento_inversionista}}</strong> (en adelante "EL INVERSIONISTA"),
        se celebra el presente contrato de inversión agrícola.
    </p>

    <h4 class="mt-4">PRIMERA: OBJETO DEL CONTRATO</h4>
    <p class="text-justify">
        EL INVERSIONISTA declara su voluntad de invertir en el proyecto agrícola denominado
        <strong>"{{nombre_proyecto}}"</strong> (Código: {{codigo_proyecto}}), ubicado en {{ubicacion_proyecto}},
        administrado a través de LA PLATAFORMA.
    </p>

    <h4 class="mt-4">SEGUNDA: MONTO DE LA INVERSIÓN</h4>
    <p class="text-justify">
        EL INVERSIONISTA aporta la suma de <strong>{{monto_inversion}}</strong> ({{monto_inversion_letras}})
        para participar en el proyecto mencionado.
    </p>

    <h4 class="mt-4">TERCERA: RENDIMIENTOS</h4>
    <p class="text-justify">
        El proyecto ofrece un rendimiento anual estimado del <strong>{{roi_anual}}</strong>.
        Los dividendos se pagarán cada {{periodo_dividendos}}, estimándose un retorno mensual
        de aproximadamente {{retorno_mensual_estimado}}.
    </p>

    <h4 class="mt-4">CUARTA: DURACIÓN</h4>
    <p class="text-justify">
        La inversión tiene una duración de <strong>{{duracion_meses}}</strong>, contados a partir
        de la fecha de aceptación del presente contrato. La fecha estimada de vencimiento es
        el <strong>{{fecha_vencimiento}}</strong>.
    </p>

    <h4 class="mt-4">QUINTA: DECLARACIONES</h4>
    <p class="text-justify">
        EL INVERSIONISTA declara que:
    </p>
    <ul>
        <li>Los fondos invertidos son de origen lícito.</li>
        <li>Ha leído y comprende los riesgos asociados a la inversión agrícola.</li>
        <li>La información proporcionada en su perfil es verídica y actualizada.</li>
        <li>Acepta los términos y condiciones de LA PLATAFORMA.</li>
    </ul>

    <h4 class="mt-4">SEXTA: RIESGOS</h4>
    <p class="text-justify">
        EL INVERSIONISTA reconoce que toda inversión conlleva riesgos, incluyendo pero no
        limitándose a: condiciones climáticas adversas, fluctuaciones del mercado, plagas
        y enfermedades de cultivos. LA PLATAFORMA no garantiza los rendimientos proyectados.
    </p>

    <h4 class="mt-4">SÉPTIMA: ACEPTACIÓN DIGITAL</h4>
    <p class="text-justify">
        Las partes acuerdan que la firma digital mediante el ingreso del nombre completo
        del inversionista en la plataforma tiene la misma validez legal que una firma
        manuscrita, conforme a la Ley 527 de 1999 de Colombia.
    </p>

    <div class="mt-5 pt-4 border-top">
        <p class="mb-1"><strong>Fecha de aceptación:</strong> {{fecha_actual}}</p>
        <p class="mb-1"><strong>Inversionista:</strong> {{nombre_inversionista}}</p>
        <p class="mb-1"><strong>Email:</strong> {{email_inversionista}}</p>
    </div>
</div>
HTML;
    }
}
