<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(path: '/api/v1/client/profil', tags: ['Client'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Profil client')])]
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user()->load('agency'));
    }

    #[OA\Put(path: '/api/v1/client/profil', tags: ['Client'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Profil client mis à jour')])]
    public function update(ProfileUpdateRequest $request): UserResource
    {
        $user = $request->user();
        $user->update($request->validated());

        return new UserResource($user->fresh());
    }
}
