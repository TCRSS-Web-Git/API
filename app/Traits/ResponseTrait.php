<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    public function errorResponse(string $message, array $details = [], int $code = 400): JsonResponse
    {
        $response = [
            'message' => $message,
            'errors' => $details,
        ];

        return response()->json($response, $code);
    }

    public function exceptionResponse(Exception $exception): JsonResponse
    {
        return $this->errorResponse($exception->getMessage(), [], $exception->getCode());
    }

    public function successResponse(array $data = [], int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }
}
