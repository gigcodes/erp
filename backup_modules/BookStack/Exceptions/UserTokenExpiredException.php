<?php

namespace Modules\BookStack\Exceptions;

class UserTokenExpiredException extends \Exception
{
    public $userId;

    /**
     * UserTokenExpiredException constructor.
     */
    public function __construct(string $message, int $userId)
    {
        $this->userId = $userId;
        parent::__construct($message);
    }
}
