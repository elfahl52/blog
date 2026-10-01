<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Role;
use Illuminate\Auth\Access\Response;

class PostPolicy
{

    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }



    public function create(User $user): bool
    {
        return in_array($user->role, [Role::Admin, Role::Author], true);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
