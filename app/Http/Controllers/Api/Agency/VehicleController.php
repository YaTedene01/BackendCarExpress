<?php

namespace App\Http\Controllers\Api\Agency;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agency\UpsertVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Repository\VehicleRepository;
use App\Services\VehiculeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class VehicleController extends Controller
{
    public function __construct(
        private readonly VehicleRepository $vehicles,
        private readonly VehiculeService $vehiculeService
    ) {}

    #[OA\Get(path: '/api/v1/agence/vehicules', tags: ['Agence'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Véhicules agence')])]
    public function index(Request $request): JsonResponse
    {
        return $this->successResponse(
            'Vehicules recuperes avec succes.',
            VehicleResource::collection(
                $this->vehicles->getAgencyVehicles($request->user()->agency_id)
            )
        );
    }

    #[OA\Post(path: '/api/v1/agence/vehicules', tags: ['Agence'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Véhicule créé')])]
    public function store(UpsertVehicleRequest $request): JsonResponse
    {
        $vehicle = $this->vehiculeService->creerPourAgence(
            $request->validated(),
            $request->user()->agency_id
        );

        return $this->successResponse('Véhicule créé avec succès.', new VehicleResource($vehicle), 201);
    }

    #[OA\Put(
        path: '/api/v1/agence/vehicules/{vehicle}',
        tags: ['Agence'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'vehicle', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Véhicule mis à jour')]
    )]
    public function update(UpsertVehicleRequest $request, Vehicle $vehicle): JsonResponse
    {
        abort_unless($vehicle->agency_id === $request->user()->agency_id, 403);

        return $this->successResponse(
            'Vehicule mis a jour avec succes.',
            new VehicleResource($this->vehiculeService->mettreAJourPourAgence($vehicle, $request->validated()))
        );
    }
}
