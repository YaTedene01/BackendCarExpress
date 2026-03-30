<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use App\Models\Reservation;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class VehicleController extends Controller
{
    #[OA\Get(path: '/api/v1/vehicules', tags: ['Public'], responses: [new OA\Response(response: 200, description: 'Liste des véhicules')])]
    public function index(Request $request): JsonResponse
    {
        $vehicles = Vehicle::query()
            ->with('agency')
            ->when($request->filled('listing_type'), fn ($query) => $query->where('listing_type', $request->string('listing_type')))
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->string('city')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->when($request->filled('brand'), fn ($query) => $query->where('brand', $request->string('brand')))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('min_price'), fn ($query) => $query->where('price', '>=', $request->float('min_price')))
            ->when($request->filled('max_price'), fn ($query) => $query->where('price', '<=', $request->float('max_price')))
            ->when($request->boolean('featured'), fn ($query) => $query->where('is_featured', true))
            ->latest()
            ->get();

        return $this->successResponse(
            'Vehicules recuperes avec succes.',
            VehicleResource::collection($vehicles)
        );
    }

    #[OA\Get(
        path: '/api/v1/vehicules/{vehicle}',
        tags: ['Public'],
        parameters: [new OA\Parameter(name: 'vehicle', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Détail véhicule')]
    )]
    public function show(Vehicle $vehicle): JsonResponse
    {
        return $this->successResponse(
            'Vehicule recupere avec succes.',
            new VehicleResource($vehicle->load('agency'))
        );
    }

    #[OA\Get(
        path: '/api/v1/vehicules/{vehicle}/disponibilite',
        tags: ['Public'],
        parameters: [
            new OA\Parameter(name: 'vehicle', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'pickup_date', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'return_date', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [new OA\Response(response: 200, description: 'Disponibilité véhicule')]
    )]
    public function availability(Request $request, Vehicle $vehicle): JsonResponse
    {
        $validated = $request->validate([
            'pickup_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after:pickup_date'],
        ]);

        $overlap = Reservation::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('pickup_date', '<', $validated['return_date'])
            ->whereDate('return_date', '>', $validated['pickup_date'])
            ->exists();

        return response()->json([
            'status' => true,
            'message' => 'Disponibilite verifiee avec succes.',
            'data' => [
                'vehicle_id' => $vehicle->id,
                'available' => ! $overlap,
                'pickup_date' => $validated['pickup_date'],
                'return_date' => $validated['return_date'],
            ],
        ]);
    }

    #[OA\Get(path: '/api/v1/metadonnees', tags: ['Public'], responses: [new OA\Response(response: 200, description: 'Métadonnées de filtres')])]
    public function metadata(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Metadonnees recuperees avec succes.',
            'data' => [
                'brands' => Vehicle::query()->distinct()->orderBy('brand')->pluck('brand'),
                'categories' => Vehicle::query()->distinct()->orderBy('category')->pluck('category'),
                'cities' => Vehicle::query()->distinct()->orderBy('city')->pluck('city'),
                'listing_types' => ['rental', 'sale'],
            ],
        ]);
    }
}
