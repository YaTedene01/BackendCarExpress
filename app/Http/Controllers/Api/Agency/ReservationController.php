<?php

namespace App\Http\Controllers\Api\Agency;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agency\UpdateReservationStatusRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReservationController extends Controller
{
    #[OA\Get(path: '/api/v1/agence/reservations', tags: ['Agence'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Réservations agence')])]
    public function index(Request $request): JsonResponse
    {
        $reservations = Reservation::query()
            ->with(['vehicle', 'client'])
            ->where('agency_id', $request->user()->agency_id)
            ->latest()
            ->get();

        return $this->successResponse(
            'Reservations recuperees avec succes.',
            ReservationResource::collection($reservations)
        );
    }

    #[OA\Patch(
        path: '/api/v1/agence/reservations/{reservation}/statut',
        tags: ['Agence'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'reservation', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Statut réservation mis à jour')]
    )]
    public function updateStatus(UpdateReservationStatusRequest $request, Reservation $reservation): JsonResponse
    {
        abort_unless($reservation->agency_id === $request->user()->agency_id, 403);

        $reservation->update([
            'status' => $request->string('status')->toString(),
        ]);

        return $this->successResponse(
            'Statut de la reservation mis a jour.',
            new ReservationResource($reservation->load(['vehicle', 'client']))
        );
    }
}
