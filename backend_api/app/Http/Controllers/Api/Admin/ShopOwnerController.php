<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShopOwnerRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ShopOwnerController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        $owners = User::with('shop')
            ->where('role', 'shop_owner')
            ->latest()
            ->get();

        return $this->successResponse(UserResource::collection($owners));
    }

    public function store(StoreShopOwnerRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['role'] = 'shop_owner';
        $validated['password'] = Hash::make($validated['password']);

        $owner = User::create($validated)->load('shop');

        return $this->successResponse(new UserResource($owner), 'Shop owner created successfully.', 201);
    }
}

