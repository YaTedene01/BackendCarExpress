<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Vehicle;
use App\Repository\ReservationRepository;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationRepository $reservations,
        private readonly ReservationService $reservationService
    ) {}

    #[OA\Get(path: '/api/v1/client/reservations', tags: ['Client'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Réservations client')])]
    public function index(): JsonResponse
    {
        return $this->successResponse(
            'Reservations recuperees avec succes.',
            ReservationResource::collection(
                $this->reservations->getClientReservations(auth()->id())
            )
        );
    }

    #[OA\Post(path: '/api/v1/client/reservations', tags: ['Client'], security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Réservation créée')])]
    public function store(StoreReservationRequest $request): JsonResponse
    {
        $vehicle = Vehicle::query()->with('agency')->findOrFail($request->integer('vehicle_id'));
        $reservation = $this->reservationService->creer(
            $request->validated(),
            $vehicle,
            $request->user()->id
        );

        return $this->successResponse(
            'Réservation créée avec succès.',
            new ReservationResource($reservation),
            201
        );
    }
}
