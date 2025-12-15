<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = (int) $request->query('page', 1);
            $limit = (int) $request->query('limit', 10);

            $users = $this->userService->getAllUsers($page, $limit);

            return $this->paginatedResponse($users, 'Users retrieved successfully');
        } catch (InvalidArgumentException $e) {
            return $this->validationErrorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve users');
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->only(['username', 'email']);

            $user = $this->userService->createUser($data);

            return $this->createdResponse($user, 'User created successfully');
        } catch (InvalidArgumentException $e) {
            return $this->validationErrorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create user');
        }
    }

    /**
     * Display the specified user.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $userId = (int) $id;

            if ($userId < 1) {
                return $this->validationErrorResponse('Invalid user ID');
            }

            $user = $this->userService->getUserById($userId);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            return $this->successResponse($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve user');
        }
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return $this->errorResponse('Method not allowed', 405);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        return $this->errorResponse('Method not allowed', 405);
    }
}
