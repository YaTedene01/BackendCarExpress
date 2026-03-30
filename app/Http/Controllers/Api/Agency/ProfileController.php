<?php

namespace App\Http\Controllers\Api\Agency;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agency\AgencyProfileUpdateRequest;
use App\Http\Resources\AgencyResource;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(path: '/api/v1/agence/profil', tags: ['Agence'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Profil agence')])]
    public function show(Request $request): AgencyResource
    {
        return new AgencyResource($request->user()->agency()->withCount('vehicles')->firstOrFail());
    }

    #[OA\Put(path: '/api/v1/agence/profil', tags: ['Agence'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Profil agence mis à jour')])]
    public function update(AgencyProfileUpdateRequest $request): AgencyResource
    {
        $agency = $request->user()->agency;
        $agency->update($request->validated());

        return new AgencyResource($agency->fresh()->loadCount('vehicles'));
    }
}
