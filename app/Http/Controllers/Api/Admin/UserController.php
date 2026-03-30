<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(path: '/api/v1/administration/utilisateurs', tags: ['Administration'], security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Utilisateurs administration')])]
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::query()->with('agency')->latest()->get()
        );
    }
}
