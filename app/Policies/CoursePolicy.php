<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        // Admin siempre puede ver
        if ($user->hasRole('Administrador')) {
            return true;
        }

        // El curso debe estar publicado y el usuario debe tener acceso secuencial
        return $course->is_published && $user->canAccessCourse($course);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Administrador');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->hasRole('Administrador');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->hasRole('Administrador');
    }
}
