<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class TaskController extends Controller
{
    use ApiResponse;

    public function __construct(
        private TaskService $taskService
    ) {}

    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = (int) $request->query('page', 1);
            $limit = (int) $request->query('limit', 10);
            $userId = $request->query('userId') ? (int) $request->query('userId') : null;

            $tasks = $this->taskService->getAllTasks($page, $limit, $userId);

            return $this->paginatedResponse($tasks, 'Tasks retrieved successfully');
        } catch (InvalidArgumentException $e) {
            return $this->validationErrorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve tasks');
        }
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->only(['title', 'description', 'status', 'user_id']);

            $task = $this->taskService->createTask($data);

            return $this->createdResponse($task, 'Task created successfully');
        } catch (InvalidArgumentException $e) {
            return $this->validationErrorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create task');
        }
    }

    /**
     * Display the specified task.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $taskId = (int) $id;

            if ($taskId < 1) {
                return $this->validationErrorResponse('Invalid task ID');
            }

            $task = $this->taskService->getTaskById($taskId);

            if (!$task) {
                return $this->notFoundResponse('Task not found');
            }

            return $this->successResponse($task, 'Task retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve task');
        }
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $taskId = (int) $id;

            if ($taskId < 1) {
                return $this->validationErrorResponse('Invalid task ID');
            }

            $data = $request->only(['title', 'description', 'status']);

            $task = $this->taskService->updateTask($taskId, $data);

            return $this->successResponse($task, 'Task updated successfully');
        } catch (InvalidArgumentException $e) {
            return $this->validationErrorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update task');
        }
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $taskId = (int) $id;

            if ($taskId < 1) {
                return $this->validationErrorResponse('Invalid task ID');
            }

            $this->taskService->deleteTask($taskId);

            return $this->successResponse(null, 'Task deleted successfully');
        } catch (InvalidArgumentException $e) {
            return $this->notFoundResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete task');
        }
    }
}
