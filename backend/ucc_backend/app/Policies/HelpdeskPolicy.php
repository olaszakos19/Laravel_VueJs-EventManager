<?php

namespace App\Policies;

use App\Models\User;

class HelpdeskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewHelpdesk(User $user)
{
    return $user->role === 'helpdesk_agent';
}
}
