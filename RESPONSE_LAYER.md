# API Response Layer Documentation

## Overview

The API Response Layer provides a consistent, reusable way to format all API responses across the application. It uses a trait-based approach to ensure uniform response structures and HTTP status codes.

## Components

### 1. HttpStatus Enum (`app/Enums/HttpStatus.php`)

Centralized HTTP status code constants with helper methods.

```php
use App\Enums\HttpStatus;

// Use constants
$code = HttpStatus::OK;                    // 200
$code = HttpStatus::CREATED;               // 201
$code = HttpStatus::BAD_REQUEST;           // 400
$code = HttpStatus::NOT_FOUND;             // 404
$code = HttpStatus::INTERNAL_SERVER_ERROR; // 500

// Get status message
$message = HttpStatus::message(200);  // 'OK'
```

**Available Status Codes:**

| Constant | Code | Usage |
|----------|------|-------|
| OK | 200 | Successful GET, PUT, DELETE |
| CREATED | 201 | Successful POST |
| ACCEPTED | 202 | Request accepted for processing |
| NO_CONTENT | 204 | Successful request with no content |
| BAD_REQUEST | 400 | Validation errors |
| UNAUTHORIZED | 401 | Authentication required |
| FORBIDDEN | 403 | Access denied |
| NOT_FOUND | 404 | Resource not found |
| CONFLICT | 409 | Resource conflict |
| UNPROCESSABLE_ENTITY | 422 | Validation failed |
| INTERNAL_SERVER_ERROR | 500 | Server error |
| NOT_IMPLEMENTED | 501 | Feature not implemented |
| SERVICE_UNAVAILABLE | 503 | Service unavailable |

### 2. ApiResponse Trait (`app/Traits/ApiResponse.php`)

Provides methods for consistent API response formatting.

#### Methods

##### Success Responses

**`successResponse($data, $message, $statusCode)`**
```php
return $this->successResponse(
    data: $user,
    message: 'User retrieved successfully',
    statusCode: HttpStatus::OK
);
```

Response:
```json
{
  "success": true,
  "message": "User retrieved successfully",
  "data": { "id": 1, "username": "john" }
}
```

**`createdResponse($data, $message)`**
```php
return $this->createdResponse(
    data: $user,
    message: 'User created successfully'
);
```

Response (201):
```json
{
  "success": true,
  "message": "User created successfully",
  "data": { "id": 1, "username": "john" }
}
```

**`paginatedResponse($paginator, $message, $statusCode)`**
```php
return $this->paginatedResponse(
    paginator: $users,
    message: 'Users retrieved successfully'
);
```

Response:
```json
{
  "success": true,
  "message": "Users retrieved successfully",
  "data": [
    { "id": 1, "username": "user1" },
    { "id": 2, "username": "user2" }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 25,
    "totalPages": 3,
    "hasMore": true
  }
}
```

**`noContentResponse()`**
```php
return $this->noContentResponse();
```

Response (204): Empty

##### Error Responses

**`errorResponse($message, $statusCode, $errors)`**
```php
return $this->errorResponse(
    message: 'Validation failed',
    statusCode: HttpStatus::BAD_REQUEST,
    errors: ['username' => 'Username is required']
);
```

Response:
```json
{
  "success": false,
  "message": "Validation failed",
  "error": "Validation failed",
  "errors": {
    "username": "Username is required"
  }
}
```

**`validationErrorResponse($message, $errors)`**
```php
return $this->validationErrorResponse(
    message: 'Validation failed',
    errors: ['email' => 'Email already exists']
);
```

Response (400):
```json
{
  "success": false,
  "message": "Validation failed",
  "error": "Validation failed",
  "errors": {
    "email": "Email already exists"
  }
}
```

**`notFoundResponse($message)`**
```php
return $this->notFoundResponse('User not found');
```

Response (404):
```json
{
  "success": false,
  "message": "User not found",
  "error": "User not found"
}
```

**`unauthorizedResponse($message)`**
```php
return $this->unauthorizedResponse('Authentication required');
```

Response (401):
```json
{
  "success": false,
  "message": "Authentication required",
  "error": "Authentication required"
}
```

**`forbiddenResponse($message)`**
```php
return $this->forbiddenResponse('Access denied');
```

Response (403):
```json
{
  "success": false,
  "message": "Access denied",
  "error": "Access denied"
}
```

**`conflictResponse($message)`**
```php
return $this->conflictResponse('Username already exists');
```

Response (409):
```json
{
  "success": false,
  "message": "Username already exists",
  "error": "Username already exists"
}
```

**`serverErrorResponse($message)`**
```php
return $this->serverErrorResponse('Failed to process request');
```

Response (500):
```json
{
  "success": false,
  "message": "Failed to process request",
  "error": "Failed to process request"
}
```

## Usage in Controllers

### Example: UserController

```php
use App\Traits\ApiResponse;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        try {
            $users = $this->userService->getAllUsers(
                page: $request->query('page', 1),
                limit: $request->query('limit', 10)
            );

            return $this->paginatedResponse(
                $users,
                'Users retrieved successfully'
            );
        } catch (InvalidArgumentException $e) {
            return $this->validationErrorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve users');
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser(
                $request->only(['username', 'email'])
            );

            return $this->createdResponse(
                $user,
                'User created successfully'
            );
        } catch (InvalidArgumentException $e) {
            return $this->validationErrorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create user');
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById((int) $id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            return $this->successResponse(
                $user,
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve user');
        }
    }
}
```

## Response Structure

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { /* resource data */ }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "error": "Error description",
  "errors": { /* optional validation errors */ }
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Resources retrieved",
  "data": [ /* array of resources */ ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 50,
    "totalPages": 5,
    "hasMore": true
  }
}
```

## Benefits

1. **Consistency** - All API responses follow the same structure
2. **Reusability** - Trait can be used in any controller
3. **Maintainability** - Centralized response logic
4. **Type Safety** - PHP type hints throughout
5. **Flexibility** - Easy to customize response formats
6. **HTTP Standards** - Proper status codes for each scenario
7. **Error Handling** - Consistent error response format
8. **Pagination Support** - Built-in pagination response format

## Best Practices

1. **Always use the trait methods** - Don't create custom response formats
2. **Use appropriate status codes** - Choose the right code for each scenario
3. **Provide clear messages** - Help clients understand what happened
4. **Include error details** - Add validation errors when relevant
5. **Handle exceptions properly** - Catch and format exceptions consistently
6. **Test response formats** - Ensure responses match expected structure

## Integration with Controllers

All API controllers should:
1. Import the `ApiResponse` trait
2. Use trait methods for all responses
3. Handle exceptions with appropriate error responses
4. Provide meaningful error messages

Example:
```php
class TaskController extends Controller
{
    use ApiResponse;

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->taskService->deleteTask((int) $id);
            return $this->successResponse(null, 'Task deleted successfully');
        } catch (InvalidArgumentException $e) {
            return $this->notFoundResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete task');
        }
    }
}
```

## Testing Response Layer

Example test:
```php
public function test_success_response_format()
{
    $response = $this->getJson('/api/users/1');
    
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'User retrieved successfully',
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'username', 'email'],
        ]);
}
```

---

**Version**: 1.0.0  
**Last Updated**: 2025-12-15  
**Status**: Production Ready ✅
