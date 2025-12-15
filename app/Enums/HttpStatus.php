<?php

namespace App\Enums;

class HttpStatus
{
    // Success Codes
    public const OK = 200;
    public const CREATED = 201;
    public const ACCEPTED = 202;
    public const NO_CONTENT = 204;

    // Client Error Codes
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const CONFLICT = 409;
    public const UNPROCESSABLE_ENTITY = 422;

    // Server Error Codes
    public const INTERNAL_SERVER_ERROR = 500;
    public const NOT_IMPLEMENTED = 501;
    public const SERVICE_UNAVAILABLE = 503;

    /**
     * Get status message for a given code
     */
    public static function message(int $code): string
    {
        return match ($code) {
            self::OK => 'OK',
            self::CREATED => 'Created',
            self::ACCEPTED => 'Accepted',
            self::NO_CONTENT => 'No Content',
            self::BAD_REQUEST => 'Bad Request',
            self::UNAUTHORIZED => 'Unauthorized',
            self::FORBIDDEN => 'Forbidden',
            self::NOT_FOUND => 'Not Found',
            self::CONFLICT => 'Conflict',
            self::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
            self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
            self::NOT_IMPLEMENTED => 'Not Implemented',
            self::SERVICE_UNAVAILABLE => 'Service Unavailable',
            default => 'Unknown Status',
        };
    }
}
