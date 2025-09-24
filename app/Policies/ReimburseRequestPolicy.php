<?php

namespace App\Policies;

use App\Models\ReimburseRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReimburseRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the reimburse request.
     */
    public function view(User $user, ReimburseRequest $reimburseRequest)
    {
        // Allow admin or super-admin to view any request
        if (in_array($user->role, ['admin', 'super-admin'])) {
            return true;
        }
        // Allow users to view their own reimburse requests
        return $reimburseRequest->user_id === $user->id;
    }

    // Optionally, add other methods like update, delete if needed
}
