<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    /**
     * Return a successful single-resource or collection response.
     */
    public static function success(mixed $data, array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => array_merge([
                'timestamp' => now()->toIso8601String(),
                'version'   => '1',
            ], $meta),
        ], $status);
    }

    /**
     * Return a paginated collection response.
     */
    public static function collection(LengthAwarePaginator $paginator): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $paginator->items(),
            'meta'    => [
                'timestamp'  => now()->toIso8601String(),
                'version'    => '1',
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'last_page'    => $paginator->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Return a structured error response.
     */
    public static function error(string $code, string $message, int $status = 400, array $details = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error'   => [
                'code'    => $code,
                'message' => $message,
                'details' => $details,
            ],
            'meta'    => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], $status);
    }

    /**
     * Return a validation error response (422).
     */
    public static function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error'   => [
                'code'    => 'VALIDATION_FAILED',
                'message' => 'The given data was invalid.',
                'details' => $errors,
            ],
            'meta'    => [
                'timestamp' => now()->toIso8601String(),
            ],
        ], 422);
    }
}
