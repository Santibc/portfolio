<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ConfiguracionSistema;
use App\Models\TipoPago;
use App\Traits\RegistraActividad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConfiguracionController extends Controller
{
    use RegistraActividad;

    public function index()
    {
        $configs = ConfiguracionSistema::all()->keyBy('clave');
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        $tiposPago = TipoPago::orderBy('orden')->orderBy('id')->get();

        return view('admin.configuracion.index', compact('configs', 'clientes', 'tiposPago'));
    }

    /* ===================== Tipos de Pago CRUD ===================== */

    private function reglasTipoPago($tipoId = null): array
    {
        return [
            'codigo' => [
                'required', 'string', 'max:50',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('tipos_pago', 'codigo')->ignore($tipoId),
            ],
            'nombre' => 'required|string|max:100',
            'icono'  => 'required|string|max:50',
            'color'  => ['required', Rule::in(['success','primary','info','warning','danger','secondary','purple','dark'])],
            'orden'  => 'nullable|integer|min:0',
        ];
    }

    public function storeTipoPago(Request $request)
    {
        $data = $request->validate($this->reglasTipoPago());
        $data['activo'] = true;
        $data['orden'] = $data['orden'] ?? (TipoPago::max('orden') + 1);

        $tipo = TipoPago::create($data);

        $this->registrarCreacion(
            'configuracion.tipo_pago_creado',
            "Se creo el tipo de pago '{$tipo->nombre}' ({$tipo->codigo})",
            $tipo
        );

        return response()->json(['success' => true, 'tipo' => $tipo, 'message' => 'Tipo de pago creado.']);
    }

    public function updateTipoPago(Request $request, TipoPago $tipo)
    {
        $data = $request->validate($this->reglasTipoPago($tipo->id));
        $valoresOriginales = $tipo->getOriginal();
        $tipo->update($data);

        $this->registrarActualizacion(
            'configuracion.tipo_pago_actualizado',
            "Se actualizo el tipo de pago '{$tipo->nombre}' ({$tipo->codigo})",
            $tipo,
            $valoresOriginales
        );

        return response()->json(['success' => true, 'tipo' => $tipo, 'message' => 'Tipo de pago actualizado.']);
    }

    public function destroyTipoPago(TipoPago $tipo)
    {
        // Soft delete via flag activo. Conserva pagos historicos con label/color/icono.
        $valoresOriginales = $tipo->getOriginal();
        $tipo->update(['activo' => false]);

        $this->registrarActualizacion(
            'configuracion.tipo_pago_desactivado',
            "Se desactivo el tipo de pago '{$tipo->nombre}' ({$tipo->codigo})",
            $tipo,
            $valoresOriginales
        );

        return response()->json(['success' => true, 'message' => 'Tipo de pago desactivado.']);
    }

    public function restoreTipoPago(TipoPago $tipo)
    {
        $valoresOriginales = $tipo->getOriginal();
        $tipo->update(['activo' => true]);

        $this->registrarActualizacion(
            'configuracion.tipo_pago_reactivado',
            "Se reactivo el tipo de pago '{$tipo->nombre}' ({$tipo->codigo})",
            $tipo,
            $valoresOriginales
        );

        return response()->json(['success' => true, 'message' => 'Tipo de pago reactivado.']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'configs' => 'required|array',
        ]);

        $reglas = [
            'nombre_empresa' => 'string|max:255',
            'direccion_empresa' => 'string|max:500|nullable',
            'telefono_empresa' => 'string|max:50|nullable',
            'nit_empresa' => 'string|max:50|nullable',
            'porcentaje_iva_defecto' => 'numeric|min:0|max:100',
            'numeros_nequi' => 'array',
            'numeros_nequi.*' => 'string|max:20',
            'timeout_autoguardado_recepcion' => 'integer|min:1|max:60',
            'timeout_forzar_cierre' => 'integer|min:10|max:600',
            'dias_expiracion_borradores' => 'integer|min:1|max:365',
            'dias_borradores_recientes' => 'integer|min:1|max:90',
            'materiales_disponibles' => 'array',
            'materiales_disponibles.*' => 'string|max:100',
            'calibres_disponibles' => 'array',
            'calibres_disponibles.*.calibre' => 'required|string|max:20',
            'calibres_disponibles.*.mm' => 'required|numeric|min:0',
            'cliente_predeterminado_id' => 'nullable|integer|exists:clientes,id',
        ];

        $clavesPermitidas = [
            'nombre_empresa', 'direccion_empresa', 'telefono_empresa', 'nit_empresa',
            'porcentaje_iva_defecto', 'numeros_nequi',
            'timeout_autoguardado_recepcion', 'timeout_forzar_cierre',
            'dias_expiracion_borradores', 'dias_borradores_recientes',
            'materiales_disponibles', 'calibres_disponibles',
            'cliente_predeterminado_id',
        ];

        $datos = $request->input('configs', []);

        // Validar solo las claves que vienen
        $validar = [];
        foreach ($datos as $clave => $valor) {
            if (!in_array($clave, $clavesPermitidas)) {
                continue;
            }
            $prefijo = "configs.{$clave}";
            if (isset($reglas[$clave])) {
                $validar[$prefijo] = $reglas[$clave];
            }
            // Reglas para items de arrays
            if (isset($reglas["{$clave}.*"])) {
                $validar["{$prefijo}.*"] = $reglas["{$clave}.*"];
            }
            if (isset($reglas["{$clave}.*.calibre"])) {
                $validar["{$prefijo}.*.calibre"] = $reglas["{$clave}.*.calibre"];
                $validar["{$prefijo}.*.mm"] = $reglas["{$clave}.*.mm"];
            }
        }

        $request->validate($validar);

        $actualizadas = 0;
        $cambios = [];
        foreach ($datos as $clave => $valor) {
            if (!in_array($clave, $clavesPermitidas)) {
                continue;
            }
            $valorAnterior = ConfiguracionSistema::get($clave);
            ConfiguracionSistema::set($clave, $valor);
            $cambios[$clave] = [
                'antes' => $valorAnterior,
                'despues' => $valor,
            ];
            $actualizadas++;
        }

        $this->registrarActividad(
            'configuracion.actualizada',
            "Se actualizaron {$actualizadas} parametro(s) del sistema",
            null,
            [
                'tipo_cambio' => 'update',
                'modelo' => 'ConfiguracionSistema',
                'modelo_id' => null,
                'cambios' => $cambios,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "{$actualizadas} configuracion(es) actualizada(s) correctamente.",
        ]);
    }

    public function uploadFondo(Request $request)
    {
        $request->validate([
            'fondo' => 'required|image|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        $fondoActual = ConfiguracionSistema::get('imagen_fondo_login');
        if ($fondoActual && file_exists(public_path($fondoActual))) {
            unlink(public_path($fondoActual));
        }

        $destino = public_path('uploads/empresa');
        if (!is_dir($destino)) {
            mkdir($destino, 0755, true);
        }

        $archivo = $request->file('fondo');
        $nombre = 'fondo_login.' . $archivo->getClientOriginalExtension();
        $archivo->move($destino, $nombre);

        $ruta = '/uploads/empresa/' . $nombre;
        ConfiguracionSistema::set('imagen_fondo_login', $ruta);

        $this->registrarActividad(
            'configuracion.fondo_actualizado',
            'Imagen de fondo de login actualizada',
            null,
            [
                'tipo_cambio' => 'update',
                'modelo' => 'ConfiguracionSistema',
                'modelo_id' => null,
                'cambios' => [
                    'imagen_fondo_login' => ['antes' => $fondoActual, 'despues' => $ruta],
                ],
            ]
        );

        return response()->json([
            'success' => true,
            'path' => $ruta,
            'message' => 'Imagen de fondo actualizada correctamente.',
        ]);
    }

    public function deleteFondo()
    {
        $fondoActual = ConfiguracionSistema::get('imagen_fondo_login');
        if ($fondoActual && file_exists(public_path($fondoActual))) {
            unlink(public_path($fondoActual));
        }

        ConfiguracionSistema::set('imagen_fondo_login', null);

        $this->registrarActividad(
            'configuracion.fondo_eliminado',
            'Imagen de fondo de login eliminada',
            null,
            [
                'tipo_cambio' => 'update',
                'modelo' => 'ConfiguracionSistema',
                'modelo_id' => null,
                'cambios' => [
                    'imagen_fondo_login' => ['antes' => $fondoActual, 'despues' => null],
                ],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Imagen de fondo eliminada correctamente.',
        ]);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        // Borrar logo anterior
        $logoActual = ConfiguracionSistema::get('logo_empresa');
        if ($logoActual && file_exists(public_path($logoActual))) {
            unlink(public_path($logoActual));
        }

        $destino = public_path('uploads/empresa');
        if (!is_dir($destino)) {
            mkdir($destino, 0755, true);
        }

        $archivo = $request->file('logo');
        $nombre = 'logo_empresa.' . $archivo->getClientOriginalExtension();
        $archivo->move($destino, $nombre);

        $logoAnterior = ConfiguracionSistema::get('logo_empresa');
        $ruta = '/uploads/empresa/' . $nombre;
        ConfiguracionSistema::set('logo_empresa', $ruta);

        $this->registrarActividad(
            'configuracion.logo_actualizado',
            'Logo de empresa actualizado',
            null,
            [
                'tipo_cambio' => 'update',
                'modelo' => 'ConfiguracionSistema',
                'modelo_id' => null,
                'cambios' => [
                    'logo_empresa' => ['antes' => $logoAnterior, 'despues' => $ruta],
                ],
            ]
        );

        return response()->json([
            'success' => true,
            'path' => $ruta,
            'message' => 'Logo actualizado correctamente.',
        ]);
    }

    public function deleteLogo()
    {
        $logoActual = ConfiguracionSistema::get('logo_empresa');
        if ($logoActual && file_exists(public_path($logoActual))) {
            unlink(public_path($logoActual));
        }

        ConfiguracionSistema::set('logo_empresa', null);

        $this->registrarActividad(
            'configuracion.logo_eliminado',
            'Logo de empresa eliminado',
            null,
            [
                'tipo_cambio' => 'update',
                'modelo' => 'ConfiguracionSistema',
                'modelo_id' => null,
                'cambios' => [
                    'logo_empresa' => ['antes' => $logoActual, 'despues' => null],
                ],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Logo eliminado correctamente.',
        ]);
    }
}
