<?php

namespace App\Http\Controllers\Api\Agency;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agency\UpdatePurchaseRequestStatusRequest;
use App\Http\Resources\PurchaseRequestResource;
use App\Models\PurchaseRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PurchaseRequestController extends Controller
{
    #[OA\Get(path: '/api/v1/agence/demandes-achat', tags: ['Agence'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Demandes d\'achat agence')])]
    public function index(Request $request): JsonResponse
    {
        $requests = PurchaseRequest::query()
            ->with(['vehicle', 'client'])
            ->where('agency_id', $request->user()->agency_id)
            ->latest()
            ->get();

        return $this->successResponse(
            'Demandes d achat recuperees avec succes.',
            PurchaseRequestResource::collection($requests)
        );
    }

    #[OA\Patch(
        path: '/api/v1/agence/demandes-achat/{purchaseRequest}/statut',
        tags: ['Agence'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'purchaseRequest', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Statut demande achat mis à jour')]
    )]
    public function updateStatus(UpdatePurchaseRequestStatusRequest $request, PurchaseRequest $purchaseRequest): JsonResponse
    {
        abort_unless($purchaseRequest->agency_id === $request->user()->agency_id, 403);

        $purchaseRequest->update([
            'status' => $request->string('status')->toString(),
        ]);

        return $this->successResponse(
            'Statut de la demande d achat mis a jour.',
            new PurchaseRequestResource($purchaseRequest->load(['vehicle', 'client']))
        );
    }
}
