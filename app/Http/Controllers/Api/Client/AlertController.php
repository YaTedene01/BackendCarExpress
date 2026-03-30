<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlertResource;
use App\Models\Alert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AlertController extends Controller
{
    #[OA\Get(path: '/api/v1/client/alertes', tags: ['Client'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Alertes client')])]
    public function index(Request $request): JsonResponse
    {
        return $this->successResponse(
            'Alertes recuperees avec succes.',
            AlertResource::collection(
                $request->user()->alerts()->latest()->get()
            )
        );
    }

    #[OA\Patch(
        path: '/api/v1/client/alertes/{alert}/lire',
        tags: ['Client'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'alert', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Alerte lue')]
    )]
    public function markAsRead(Request $request, Alert $alert): JsonResponse
    {
        abort_unless($alert->user_id === $request->user()->id, 403);

        $alert->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this->successResponse('Alerte marquee comme lue.', new AlertResource($alert));
    }
}
