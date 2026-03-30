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

    #[OA\Post(
        path: '/api/v1/authentification/client/inscription',
        tags: ['Authentification'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'phone', 'city', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Moussa Ndiaye'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'moussa@carexpress.sn'),
                    new OA\Property(property: 'phone', type: 'string', example: '+221771234567'),
                    new OA\Property(property: 'city', type: 'string', example: 'Dakar'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'client12345'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'client12345'),
                    new OA\Property(property: 'device_name', type: 'string', example: 'client-web')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Compte client cree',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Compte client cree avec succes.'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'token', type: 'string', example: '1|zXc123tokenvalue'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                                new OA\Property(
                                    property: 'utilisateur',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 15),
                                        new OA\Property(property: 'name', type: 'string', example: 'Moussa Ndiaye'),
                                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'moussa@carexpress.sn'),
                                        new OA\Property(property: 'phone', type: 'string', example: '+221771234567'),
                                        new OA\Property(property: 'city', type: 'string', example: 'Dakar'),
                                        new OA\Property(property: 'role', type: 'string', example: 'client')
                                    ],
                                    type: 'object'
                                )
                            ],
                            type: 'object'
                        )
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Erreur de validation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Les donnees fournies sont invalides.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'email',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'Cette adresse email est deja utilisee.')
                                ),
                                new OA\Property(
                                    property: 'password',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'La confirmation du mot de passe ne correspond pas.')
                                )
                            ],
                            type: 'object'
                        )
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function registerClient(ClientRegisterRequest $request): JsonResponse
    {
        $resultat = $this->authService->inscrireClient([
            ...$request->validated(),
            'device_name' => $request->input('device_name', 'client-web'),
        ]);

        return $this->successResponse('Compte client créé avec succès.', [
            'token' => $resultat['token'],
            'token_type' => 'Bearer',
            'utilisateur' => new UserResource($resultat['utilisateur']),
        ], 201);
    }

    #[OA\Post(
        path: '/api/v1/authentification/connexion',
        tags: ['Authentification'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['identifier', 'password', 'role'],
                properties: [
                    new OA\Property(property: 'identifier', type: 'string', example: 'client@carexpress.sn'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'client12345'),
                    new OA\Property(property: 'role', type: 'string', example: 'client', enum: ['client', 'agency', 'admin']),
                    new OA\Property(property: 'device_name', type: 'string', example: 'web-app')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authentification reussie',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Connexion reussie.'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'token', type: 'string', example: '1|zXc123tokenvalue'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                                new OA\Property(
                                    property: 'utilisateur',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 7),
                                        new OA\Property(property: 'name', type: 'string', example: 'Client Demo'),
                                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'client@carexpress.sn'),
                                        new OA\Property(property: 'phone', type: 'string', example: '+221770000000'),
                                        new OA\Property(property: 'city', type: 'string', example: 'Dakar'),
                                        new OA\Property(property: 'role', type: 'string', example: 'client')
                                    ],
                                    type: 'object'
                                )
                            ],
                            type: 'object'
                        )
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Authentification refusee',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Authentification requise ou jeton invalide.')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Erreur de validation ou identifiants invalides',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Les donnees fournies sont invalides.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'identifier',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'Les identifiants fournis sont invalides.')
                                )
                            ],
                            type: 'object'
                        )
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $resultat = $this->authService->connecter([
            ...$request->validated(),
            'device_name' => $request->input('device_name', 'web-app'),
        ]);

        return $this->successResponse('Connexion réussie.', [
            'token' => $resultat['token'],
            'token_type' => 'Bearer',
            'utilisateur' => new UserResource($resultat['utilisateur']),
        ]);
    }

    #[OA\Get(
        path: '/api/v1/authentification/moi',
        tags: ['Authentification'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Utilisateur courant recupere',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Utilisateur recupere avec succes.'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 7),
                                new OA\Property(property: 'name', type: 'string', example: 'Client Demo'),
                                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'client@carexpress.sn'),
                                new OA\Property(property: 'phone', type: 'string', example: '+221770000000'),
                                new OA\Property(property: 'city', type: 'string', example: 'Dakar'),
                                new OA\Property(property: 'role', type: 'string', example: 'client')
                            ],
                            type: 'object'
                        )
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token manquant ou invalide',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Authentification requise ou jeton invalide.')
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            'Utilisateur recupere avec succes.',
            new UserResource($request->user()->load('agency'))
        );
    }

    #[OA\Post(
        path: '/api/v1/authentification/deconnexion',
        tags: ['Authentification'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deconnexion reussie',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Deconnexion reussie.'),
                        new OA\Property(property: 'data', type: 'null', nullable: true, example: null)
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Token manquant ou invalide',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Authentification requise ou jeton invalide.')
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse('Déconnexion réussie.');
    }
}
