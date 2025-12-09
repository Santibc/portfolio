<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Course;
use Symfony\Component\HttpFoundation\Response;

class CheckCourseAccess
{
    /**
     * Handle an incoming request.
     *
     * Verifica que el usuario tenga acceso secuencial al curso.
     * Debe haber completado los cursos anteriores de la misma categoría.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $course = $request->route('course');

        // Si no hay curso en la ruta, continuar
        if (!$course) {
            return $next($request);
        }

        // Si es un string (slug), buscar el curso
        if (is_string($course)) {
            $course = Course::where('slug', $course)->first();
        }

        // Si no se encontró el curso, continuar (el controlador manejará el 404)
        if (!$course) {
            return $next($request);
        }

        $user = $request->user();

        // Si no hay usuario autenticado, redirigir al login
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a este curso.');
        }

        // Verificar si el usuario puede acceder al curso
        if (!$user->canAccessCourse($course)) {
            // Obtener el curso anterior que debe completar
            $previousCourse = Course::where('category_id', $course->category_id)
                ->where('order', '<', $course->order)
                ->where('is_published', true)
                ->orderBy('order', 'desc')
                ->first();

            $message = 'Debes completar los cursos anteriores para acceder a este curso.';

            if ($previousCourse) {
                $message = "Debes completar el curso \"{$previousCourse->title}\" antes de acceder a este curso.";
            }

            return redirect()->route('cursos.index')
                ->with('warning', $message);
        }

        return $next($request);
    }
}
