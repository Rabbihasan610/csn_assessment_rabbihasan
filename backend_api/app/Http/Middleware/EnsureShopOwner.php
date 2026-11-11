<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isShopOwner()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Shop owner access required.',
            ], 403);
        }

        if (! $user->shop_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'No assigned shop found for this shop owner.',
            ], 403);
        }

        return $next($request);
    }
}

