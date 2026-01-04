<?php

namespace App\Exception;

use Throwable;

class BaseException extends \Exception
{
    protected int $httpStatusCode;
    protected array $context = [];

    public function __construct(
        string $message = "",
        int $httpStatusCode = 500,
        array $context = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->httpStatusCode = $httpStatusCode;
        $this->context = $context;

        parent::__construct($message, $code, $previous);
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
