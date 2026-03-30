<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class SystemController extends Controller
{
    #[OA\Get(path: '/api/v1/administration/systeme', tags: ['Administration'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'État système')])]
    public function __invoke(): JsonResponse
    {
        return $this->successResponse('Etat systeme recupere avec succes.', [
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'debug' => (bool) config('app.debug'),
            'swagger_url' => url('/api/documentation'),
            'timestamp' => now(),
        ]);
    }
}
