<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly UserRepository $users
    ) {}

    public function inscrireClient(array $data): array
    {
        $user = $this->users->create([
            'role' => UserRole::Client,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'password' => $data['password'],
            'status' => 'active',
        ]);

        return [
            'utilisateur' => $user,
            'token' => $user->createToken($data['device_name'] ?? 'client-web')->plainTextToken,
        ];
    }

    public function connecter(array $data): array
    {
        $user = $this->users->findByRoleAndIdentifier($data['role'], $data['identifier']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['Les identifiants fournis sont invalides.'],
            ]);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return [
            'utilisateur' => $user->load('agency'),
            'token' => $user->createToken($data['device_name'] ?? 'web-app')->plainTextToken,
        ];
    }
}
