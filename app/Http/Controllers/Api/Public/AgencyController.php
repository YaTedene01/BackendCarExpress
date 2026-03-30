<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgencyResource;
use App\Models\Agency;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AgencyController extends Controller
{
    #[OA\Get(path: '/api/v1/agences', tags: ['Public'], responses: [new OA\Response(response: 200, description: 'Liste des agences')])]
    public function index(): JsonResponse
    {
        $agencies = Agency::query()
            ->withCount('vehicles')
            ->where('status', 'active')
            ->latest()
            ->get();

        return $this->successResponse(
            'Agences recuperees avec succes.',
            AgencyResource::collection($agencies)
        );
    }

    #[OA\Get(
        path: '/api/v1/agences/{slug}',
        tags: ['Public'],
        parameters: [new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Détail agence')]
    )]
    public function show(Agency $agency): JsonResponse
    {
        return $this->successResponse(
            'Agence recuperee avec succes.',
            new AgencyResource($agency->loadCount('vehicles'))
        );
    }
}
