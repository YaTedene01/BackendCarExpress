<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientRegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    #[OA\Post(path: '/api/v1/authentification/client/inscription', tags: ['Authentification'], responses: [new OA\Response(response: 201, description: 'Client créé')])]
    public function registerClient(ClientRegisterRequest $request): JsonResponse
    {
        $resultat = $this->authService->inscrireClient([
            ...$request->validated(),
            'device_name' => $request->input('device_name', 'client-web'),
        ]);

        return $this->successResponse('Compte client créé avec succès.', [
            'token' => $resultat['token'],
            'utilisateur' => new UserResource($resultat['utilisateur']),
        ], 201);
    }

    #[OA\Post(path: '/api/v1/authentification/connexion', tags: ['Authentification'], responses: [new OA\Response(response: 200, description: 'Connexion réussie')])]
    public function login(LoginRequest $request): JsonResponse
    {
        $resultat = $this->authService->connecter([
            ...$request->validated(),
            'device_name' => $request->input('device_name', 'web-app'),
        ]);

        return $this->successResponse('Connexion réussie.', [
            'token' => $resultat['token'],
            'utilisateur' => new UserResource($resultat['utilisateur']),
        ]);
    }

    #[OA\Get(path: '/api/v1/authentification/moi', tags: ['Authentification'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Utilisateur courant')])]
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            'Utilisateur recupere avec succes.',
            new UserResource($request->user()->load('agency'))
        );
    }

    #[OA\Post(path: '/api/v1/authentification/deconnexion', tags: ['Authentification'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Déconnexion')])]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse('Déconnexion réussie.');
    }
}
