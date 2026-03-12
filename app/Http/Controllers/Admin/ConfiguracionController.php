<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ConfiguracionSistema;
use App\Traits\RegistraActividad;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    use RegistraActividad;

    public function index()
    {
        $configs = ConfiguracionSistema::all()->keyBy('clave');
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.configuracion.index', compact('configs', 'clientes'));
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
        foreach ($datos as $clave => $valor) {
            if (!in_array($clave, $clavesPermitidas)) {
                continue;
            }
            ConfiguracionSistema::set($clave, $valor);
            $actualizadas++;
        }

        $this->registrarActividad(
            'configuracion.actualizada',
            "Se actualizaron {$actualizadas} parametro(s) del sistema",
            null,
            ['claves' => array_keys(array_intersect_key($datos, array_flip($clavesPermitidas)))]
        );

        return response()->json([
            'success' => true,
            'message' => "{$actualizadas} configuracion(es) actualizada(s) correctamente.",
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

        $ruta = '/uploads/empresa/' . $nombre;
        ConfiguracionSistema::set('logo_empresa', $ruta);

        $this->registrarActividad('configuracion.logo_actualizado', 'Logo de empresa actualizado');

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

        $this->registrarActividad('configuracion.logo_eliminado', 'Logo de empresa eliminado');

        return response()->json([
            'success' => true,
            'message' => 'Logo eliminado correctamente.',
        ]);
    }
}
