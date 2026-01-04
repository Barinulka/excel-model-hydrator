<?php

namespace App\Constants;

final class HttpStatus
{
    // Информативные
    public const OK = 200;
    public const CREATED = 201;
    public const NO_CONTENT = 204;

    // Клиентские ошибки
    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;

    // Серверные ошибки
    public const INTERNAL_SERVER_ERROR = 500;
    public const SERVICE_UNAVAILABLE = 503;

    private function __construct() {}
}