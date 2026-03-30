<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StorePurchaseRequestRequest;
use App\Http\Resources\PurchaseRequestResource;
use App\Models\Vehicle;
use App\Repository\PurchaseRequestRepository;
use App\Services\DemandeAchatService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class PurchaseRequestController extends Controller
{
    public function __construct(
        private readonly PurchaseRequestRepository $purchaseRequests,
        private readonly DemandeAchatService $demandeAchatService
    ) {}

    #[OA\Get(path: '/api/v1/client/demandes-achat', tags: ['Client'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Demandes d\'achat client')])]
    public function index(): JsonResponse
    {
        return $this->successResponse(
            'Demandes d achat recuperees avec succes.',
            PurchaseRequestResource::collection(
                $this->purchaseRequests->getClientRequests(auth()->id())
            )
        );
    }

    #[OA\Post(path: '/api/v1/client/demandes-achat', tags: ['Client'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Demande d\'achat créée')])]
    public function store(StorePurchaseRequestRequest $request): JsonResponse
    {
        $vehicle = Vehicle::query()->with('agency')->findOrFail($request->integer('vehicle_id'));
        $purchaseRequest = $this->demandeAchatService->creer(
            $request->validated(),
            $vehicle,
            $request->user()->id
        );

        return $this->successResponse(
            'Demande d\'achat créée avec succès.',
            new PurchaseRequestResource($purchaseRequest),
            201
        );
    }
}
