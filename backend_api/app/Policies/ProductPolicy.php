<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isShopOwner(); // all view permission is shop owner
    }

    public function view(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->isShopOwner();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isShopOwner();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin() || ($user->isShopOwner() && $product->created_by === $user->id);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin() || ($user->isShopOwner() && $product->created_by === $user->id);
    }
}

