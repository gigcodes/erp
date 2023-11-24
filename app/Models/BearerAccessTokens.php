<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\User;

class BearerAccessTokens extends Model
{
    use HasFactory;

    const ID = 'id';
    const CREATED_AT = 'created_at';
    const TOKEN = 'token';
    const USER_ID = 'user_id';

    protected $table = 'bearer_access_tokens';

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return (int)$this->getAttribute(self::ID);
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return (string)$this->getAttribute(self::TOKEN);
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->setAttribute(self::TOKEN, $token);

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->setAttribute(self::USER_ID, $user->id);

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return User::find($this->getUserId()) ?? null;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return (int)$this->getAttribute(self::USER_ID);
    }

    /**
     * @param string $token
     * @return self|null
     */
    public function getByToken(string $token): ?self
    {
        return $this
            ->where(self::TOKEN, $token)
            ->whereDate('created_at', '>=', now()->subDay()->toDateString())
            ->first() ?? null;
    }
}
