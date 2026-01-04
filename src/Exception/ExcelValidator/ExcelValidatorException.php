<?php

namespace App\Exception\ExcelValidator;

use App\Exception\BaseException;
use Throwable;

class ExcelValidatorException extends BaseException
{
    public function __construct(
        string $message = "Ошибка валидации входных данных",
        int $httpStatusCode = 400,
        array $context = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $httpStatusCode, $context, $code, $previous);
    }
}
