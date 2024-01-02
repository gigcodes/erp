<?php

namespace Modules\BookStack\Auth\Access;

use Modules\BookStack\Auth\User;
use Modules\BookStack\Notifications\ConfirmEmail;
use Modules\BookStack\Exceptions\ConfirmationEmailException;

class EmailConfirmationService extends UserTokenService
{
    protected $tokenTable = 'email_confirmations';

    protected $expiryTime = 24;

    /**
     * Create new confirmation for a user,
     * Also removes any existing old ones.
     *
     *
     * @throws ConfirmationEmailException
     */
    public function sendConfirmation(User $user)
    {
        if ($user->email_confirmed) {
            throw new ConfirmationEmailException(trans('errors.email_already_confirmed'), '/login');
        }

        $this->deleteByUser($user);
        $token = $this->createTokenForUser($user);

        $user->notify(new ConfirmEmail($token));
    }

    /**
     * Check if confirmation is required in this instance.
     */
    public function confirmationRequired(): bool
    {
        return setting('registration-confirmation')
            || setting('registration-restrict');
    }
}
