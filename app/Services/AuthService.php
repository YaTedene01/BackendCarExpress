<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Repository\AgencyRepository;
use App\Repository\UserRepository;
use App\Utils\GenererReference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly AgencyRepository $agencies
    ) {}

    public function inscrireClient(array $data): array
    {
        $user = $this->users->create([
            'role' => UserRole::Client,
            'name' => $data['name'] ?: $this->genererNomParDefautDepuisIdentifiants($data['email'], $data['phone']),
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

    public function inscrireAgence(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $agency = $this->agencies->create([
                'name' => $data['company'],
                'slug' => GenererReference::slug($data['company']),
                'activity' => $data['activity'] ?? 'Location et vente',
                'city' => $data['city'],
                'contact_phone' => $data['phone'],
                'contact_email' => $data['email'],
                'status' => 'pending',
                'metadata' => [
                    'source' => 'public_agency_signup',
                ],
            ]);

            $user = $this->users->create([
                'agency_id' => $agency->id,
                'role' => UserRole::Agency,
                'name' => $data['company'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'city' => $data['city'],
                'password' => $data['password'],
                'status' => 'active',
            ]);

            return [
                'utilisateur' => $user->load('agency'),
                'agence' => $agency->loadCount('vehicles'),
                'token' => $user->createToken($data['device_name'] ?? 'agency-web')->plainTextToken,
            ];
        });
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

    private function genererNomParDefautDepuisIdentifiants(string $email, string $phone): string
    {
        $emailPrefix = trim((string) strstr($email, '@', true));
        $candidate = $emailPrefix !== '' ? $emailPrefix : $phone;
        $candidate = trim(str_replace(['.', '_', '-'], ' ', $candidate));

        return $candidate !== ''
            ? ucwords($candidate)
            : 'Client Car Express';
    }
}
