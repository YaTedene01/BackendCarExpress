<?php

namespace App\Repository;

use App\Models\User;

class UserRepository
{
    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function findByRoleAndIdentifier(string $role, string $identifier): ?User
    {
        return User::query()
            ->where('role', $role)
            ->where(function ($query) use ($identifier): void {
                $query->where('email', $identifier)
                    ->orWhere('phone', $identifier);
            })
            ->first();
    }

    public function getAllWithAgency()
    {
        return User::query()->with('agency')->latest()->get();
    }
}
