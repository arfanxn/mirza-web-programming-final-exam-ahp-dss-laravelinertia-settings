<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function __construct()
    {
    }

    /**
     * @param string $email
     * @return ?User
     */
    public function findByEmail(string $email): ?User
    {
        $user = User::where('email', $email)->first();
        return $user;
    }
}
