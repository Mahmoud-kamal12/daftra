<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    public static function ok(mixed $data = null, ?string $message = null, array $meta = [], int $status = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ];

        return response()->json($payload, $status);
    }

    public static function created(mixed $data = null, ?string $message = null, array $meta = []): JsonResponse
    {
        return self::ok($data, $message, $meta, 201);
    }

    public static function paginated(LengthAwarePaginator $paginator, mixed $data, ?string $message = null): JsonResponse
    {
        return self::ok($data, $message, [
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public static function fail(string $message, array $errors = [], int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => (object) $errors,
        ], $status);
    }

    public static function unauthorized(?string $message = null, array $errors = []): JsonResponse
    {
        return self::fail($message ?? 'Unauthenticated', $errors, 401);
    }

    public static function forbidden(?string $message = null, array $errors = []): JsonResponse
    {
        return self::fail($message ?? 'Forbidden', $errors, 403);
    }

    public static function notFound(?string $message = null, array $errors = []): JsonResponse
    {
        return self::fail($message ?? 'Not Found', $errors, 404);
    }
}
