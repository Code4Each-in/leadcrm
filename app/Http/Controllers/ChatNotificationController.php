<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChatNotificationController extends Controller
{
    public function heartbeat(Request $request): JsonResponse
    {
        $userId = (int) $request->user()->id;

        Cache::put(
            'chatify.active.' . $userId,
            now()->timestamp,
            now()->addMinutes(2)
        );

        return response()->json([
            'success' => true,
        ]);
    }
}
