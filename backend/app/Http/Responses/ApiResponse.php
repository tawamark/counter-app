<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    protected function success(mixed $data, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    protected function paginated(LengthAwarePaginator $paginator, callable $transform, string $message): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => collect($paginator->items())->map($transform)->values(),
            'message' => $message,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
