<?php

namespace App\Policies;

use App\Models\ShopProduct;
use App\Models\User;

class ShopProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isShopOwner();
    }

    public function view(User $user, ShopProduct $shopProduct): bool
    {
        return $user->isAdmin() || ($user->isShopOwner() && $user->shop_id === $shopProduct->shop_id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || ($user->isShopOwner() && $user->shop_id !== null);
    }

    public function update(User $user, ShopProduct $shopProduct): bool
    {
        return $user->isAdmin() || ($user->isShopOwner() && $user->shop_id === $shopProduct->shop_id);
    }

    public function delete(User $user, ShopProduct $shopProduct): bool
    {
        return $user->isAdmin() || ($user->isShopOwner() && $user->shop_id === $shopProduct->shop_id);
    }
}

