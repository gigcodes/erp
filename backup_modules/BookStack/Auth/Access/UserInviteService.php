<?php

namespace Modules\BookStack\Auth\Access;

use Modules\BookStack\Auth\User;
use Modules\BookStack\Notifications\UserInvite;

class UserInviteService extends UserTokenService
{
    protected $tokenTable = 'user_invites';

    protected $expiryTime = 336; // Two weeks

    /**
     * Send an invitation to a user to sign into BookStack
     * Removes existing invitation tokens.
     */
    public function sendInvitation(User $user)
    {
        $this->deleteByUser($user);
        $token = $this->createTokenForUser($user);
        $user->notify(new UserInvite($token));
    }
}
