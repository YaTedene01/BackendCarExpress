<?php

namespace App\Support\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Car Express API',
    description: 'API Laravel professionnelle pour Car Express.'
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'Serveur API'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Bearer'
)]
#[OA\Tag(name: 'Public')]
#[OA\Tag(name: 'Authentification')]
#[OA\Tag(name: 'Client')]
#[OA\Tag(name: 'Agence')]
#[OA\Tag(name: 'Administration')]
class OpenApi {}
