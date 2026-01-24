<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaController extends Controller
{
    /**
     * Mapeo de tablas a nombres legibles
     */
    protected array $tablaLabels = [
        'trabajadores' => 'Trabajadores',
        'users' => 'Usuarios',
        'obras' => 'Obras',
        'clientes' => 'Clientes',
        'facturas' => 'Facturas',
        'ingresos' => 'Ingresos',
        'gastos' => 'Gastos',
        'contratos' => 'Contratos',
        'vehiculos' => 'Vehículos',
        'maquinaria' => 'Maquinaria',
        'subcontratas' => 'Subcontratas',
        'epi_inventario' => 'EPIs Inventario',
        'epi_entregas' => 'EPIs Entregas',
        'epi_revisiones' => 'EPIs Revisiones',
        'trabajador_formaciones' => 'Formaciones',
        'trabajador_documentos' => 'Documentos Trabajador',
        'cuadrillas' => 'Cuadrillas',
        'partes_diarios' => 'Partes Diarios',
        'fichajes' => 'Fichajes',
        'alertas' => 'Alertas',
        'leads' => 'Leads/Oportunidades',
        'vehiculo_documentos' => 'Documentos Vehículos',
        'maquinaria_inspecciones' => 'Inspecciones Maquinaria',
        'maquinaria_mantenimientos' => 'Mantenimientos Maquinaria',
        'obra_documentos' => 'Documentos Obra',
        'obra_hitos' => 'Hitos de Obra',
        'subcontrata_documentos_cae' => 'Documentos CAE',
        'caducidades_generales' => 'Caducidades Empresa',
        'trabajador_bonos' => 'Bonos/Primas',
    ];

    /**
     * Mapeo de acciones a colores de badge
     */
    protected array $accionColores = [
        'crear' => 'success',
        'editar' => 'warning',
        'eliminar' => 'danger',
        'ver' => 'info',
        'login' => 'primary',
        'logout' => 'secondary',
        'otro' => 'dark',
    ];

    /**
     * Listado de registros de auditoría con filtros
     */
    public function index(Request $request)
    {
        $query = Auditoria::with('user')
            ->orderBy('created_at', 'desc');

        // Filtro por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por tabla
        if ($request->filled('tabla')) {
            $query->where('tabla', $request->tabla);
        }

        // Filtro por acción
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        // Filtro por fecha desde
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        // Filtro por fecha hasta
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Filtro por registro específico
        if ($request->filled('registro_id')) {
            $query->where('registro_id', $request->registro_id);
        }

        // Paginar resultados
        $auditorias = $query->paginate(50)->withQueryString();

        // Estadísticas del día
        $hoy = now()->toDateString();
        $stats = [
            'total_hoy' => Auditoria::whereDate('created_at', $hoy)->count(),
            'creaciones_hoy' => Auditoria::whereDate('created_at', $hoy)->where('accion', 'crear')->count(),
            'ediciones_hoy' => Auditoria::whereDate('created_at', $hoy)->where('accion', 'editar')->count(),
            'eliminaciones_hoy' => Auditoria::whereDate('created_at', $hoy)->where('accion', 'eliminar')->count(),
        ];

        // Usuarios para el filtro
        $usuarios = User::orderBy('name')->get(['id', 'name']);

        // Tablas únicas para el filtro
        $tablasUnicas = Auditoria::select('tabla')
            ->distinct()
            ->orderBy('tabla')
            ->pluck('tabla');

        // Acciones disponibles
        $acciones = ['crear', 'editar', 'eliminar', 'ver', 'login', 'logout', 'otro'];

        return view('auditoria.index', compact(
            'auditorias',
            'stats',
            'usuarios',
            'tablasUnicas',
            'acciones',
            'request'
        ))->with([
            'tablaLabels' => $this->tablaLabels,
            'accionColores' => $this->accionColores,
        ]);
    }

    /**
     * Ver detalle de un registro de auditoría
     */
    public function show(Auditoria $auditoria)
    {
        $auditoria->load('user');

        // Obtener URL del registro relacionado si existe
        $urlRegistro = $this->getUrlRegistro($auditoria->tabla, $auditoria->registro_id);

        return view('auditoria.show', compact('auditoria', 'urlRegistro'))
            ->with([
                'tablaLabels' => $this->tablaLabels,
                'accionColores' => $this->accionColores,
            ]);
    }

    /**
     * Exportar auditoría a CSV (AJAX)
     */
    public function exportar(Request $request)
    {
        $query = Auditoria::with('user')
            ->orderBy('created_at', 'desc');

        // Aplicar mismos filtros que en index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('tabla')) {
            $query->where('tabla', $request->tabla);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Limitar a 10,000 registros para evitar problemas de memoria
        $auditorias = $query->limit(10000)->get();

        $filename = 'auditoria_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($auditorias) {
            $file = fopen('php://output', 'w');
            // BOM para UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Cabeceras
            fputcsv($file, ['ID', 'Fecha/Hora', 'Usuario', 'Acción', 'Tabla', 'Registro ID', 'IP'], ';');

            foreach ($auditorias as $auditoria) {
                fputcsv($file, [
                    $auditoria->id,
                    $auditoria->created_at?->format('d/m/Y H:i:s'),
                    $auditoria->user?->name ?? 'Sistema',
                    ucfirst($auditoria->accion),
                    $this->tablaLabels[$auditoria->tabla] ?? $auditoria->tabla,
                    $auditoria->registro_id,
                    $auditoria->ip_address,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Obtener URL para navegar al registro relacionado
     */
    protected function getUrlRegistro(?string $tabla, ?int $registroId): ?string
    {
        if (!$tabla || !$registroId) {
            return null;
        }

        $rutas = [
            'trabajadores' => 'trabajadores.show',
            'users' => 'admin.users.show',
            'obras' => 'obras.show',
            'clientes' => 'clientes.show',
            'facturas' => 'facturas.show',
            'ingresos' => 'ingresos.show',
            'gastos' => 'gastos.show',
            'contratos' => 'contratos.show',
            'vehiculos' => 'vehiculos.show',
            'maquinaria' => 'maquinaria.show',
            'subcontratas' => 'subcontratas.show',
            'epi_inventario' => 'epi-inventario.show',
            'cuadrillas' => 'cuadrillas.show',
            'partes_diarios' => 'partes-diarios.show',
            'leads' => 'leads.show',
            'caducidades_generales' => 'caducidades-generales.show',
        ];

        if (!isset($rutas[$tabla])) {
            return null;
        }

        try {
            return route($rutas[$tabla], $registroId);
        } catch (\Exception $e) {
            return null;
        }
    }
}
