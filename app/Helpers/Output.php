<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Output
{
    public static function ok($data = null, $message = null, $statusCode = Response::HTTP_OK): JsonResponse
    {
        if (is_array($data)) {
            array_walk_recursive($data, function (&$item, $key) {
                $item = $item === null ? '' : $item;
            });
        }

        return response()->json([
            'status' => 'ok',
            'code' => $statusCode,
            'data' => $data ?? null,
            'message' => $message ?? null
        ]);
    }

    public static function fails($message, $logMessage = null, $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        Log::error($logMessage ?? $message);
        return response()->json([
            'status' => 'fails',
            'code' => $statusCode,
            'message' => $message
        ], $statusCode);
    }
}
