<?php

namespace App\Traits;

use App\Enums\HttpStatus;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success response
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $statusCode = HttpStatus::OK
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a created response (201)
     */
    protected function createdResponse(
        mixed $data,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return $this->successResponse($data, $message, HttpStatus::CREATED);
    }

    /**
     * Return an error response
     */
    protected function errorResponse(
        string $message,
        int $statusCode = HttpStatus::BAD_REQUEST,
        ?array $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'error' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a not found response (404)
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, HttpStatus::NOT_FOUND);
    }

    /**
     * Return a validation error response (400)
     */
    protected function validationErrorResponse(
        string $message = 'Validation failed',
        ?array $errors = null
    ): JsonResponse {
        return $this->errorResponse($message, HttpStatus::BAD_REQUEST, $errors);
    }

    /**
     * Return an unauthorized response (401)
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, HttpStatus::UNAUTHORIZED);
    }

    /**
     * Return a forbidden response (403)
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, HttpStatus::FORBIDDEN);
    }

    /**
     * Return a conflict response (409)
     */
    protected function conflictResponse(string $message = 'Conflict'): JsonResponse
    {
        return $this->errorResponse($message, HttpStatus::CONFLICT);
    }

    /**
     * Return a server error response (500)
     */
    protected function serverErrorResponse(string $message = 'Internal server error'): JsonResponse
    {
        return $this->errorResponse($message, HttpStatus::INTERNAL_SERVER_ERROR);
    }

    /**
     * Return a paginated response
     */
    protected function paginatedResponse(
        $paginator,
        string $message = 'Success',
        int $statusCode = HttpStatus::OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'limit' => $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
                'hasMore' => $paginator->hasMorePages(),
            ],
        ], $statusCode);
    }

    /**
     * Return a no content response (204)
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, HttpStatus::NO_CONTENT);
    }
}
