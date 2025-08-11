<?php

namespace App\Policies;

use App\Models\TutoringSession;
use App\Models\User;

class TutoringSessionPolicy
{
    public function view(User $user, TutoringSession $session): bool
    {
        return $user->hasRole(['admin', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'super-admin']);
    }

    public function update(User $user, TutoringSession $session): bool
    {
        return $user->hasRole(['admin', 'super-admin']);
    }

    public function delete(User $user, TutoringSession $session): bool
    {
        return $user->hasRole(['admin', 'super-admin']);
    }
}