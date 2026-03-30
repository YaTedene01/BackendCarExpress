<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAgencyRequest;
use App\Http\Requests\Admin\UpdateAgencyStatusRequest;
use App\Http\Resources\AgencyResource;
use App\Models\Agency;
use App\Repository\AgencyRepository;
use App\Services\AgenceService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AgencyController extends Controller
{
    public function __construct(
        private readonly AgencyRepository $agencies,
        private readonly AgenceService $agenceService
    ) {}

    #[OA\Get(path: '/api/v1/administration/agences', tags: ['Administration'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Agences administration')])]
    public function index(): JsonResponse
    {
        return $this->successResponse(
            'Agences recuperees avec succes.',
            AgencyResource::collection($this->agencies->getAllWithCount())
        );
    }

    #[OA\Post(path: '/api/v1/administration/agences', tags: ['Administration'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Agence créée')])]
    public function store(StoreAgencyRequest $request): JsonResponse
    {
        $agency = $this->agenceService->creerDepuisAdministration($request->validated());

        return $this->successResponse('Agence créée avec succès.', new AgencyResource($agency), 201);
    }

    #[OA\Patch(
        path: '/api/v1/administration/agences/{agency}/statut',
        tags: ['Administration'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'agency', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Statut agence mis à jour')]
    )]
    public function updateStatus(UpdateAgencyStatusRequest $request, Agency $agency): JsonResponse
    {
        $agency = $this->agencies->updateStatus($agency, $request->string('status')->toString());

        return $this->successResponse('Statut de l\'agence mis à jour.', new AgencyResource($agency));
    }
}
