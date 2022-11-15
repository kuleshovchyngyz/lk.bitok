<?php

namespace App\Policies;

use App\Models\AddedUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddedUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\AddedUser $addedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AddedUser $addedUser)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\AddedUser $addedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AddedUser $addedUser)
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\AddedUser $addedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AddedUser $addedUser)
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\AddedUser $addedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, AddedUser $addedUser)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\AddedUser $addedUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, AddedUser $addedUser)
    {
        //
    }
}
