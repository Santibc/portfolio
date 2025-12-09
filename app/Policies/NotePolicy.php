<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     * Las notas son públicas, todos pueden verlas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Las notas son públicas, todos pueden verlas.
     */
    public function view(User $user, Note $note): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * Cualquier usuario autenticado puede crear notas.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Solo el autor o admin puede editar.
     */
    public function update(User $user, Note $note): bool
    {
        return $user->id === $note->user_id || $user->hasRole('Administrador');
    }

    /**
     * Determine whether the user can delete the model.
     * Solo el autor o admin puede eliminar.
     */
    public function delete(User $user, Note $note): bool
    {
        return $user->id === $note->user_id || $user->hasRole('Administrador');
    }
}
