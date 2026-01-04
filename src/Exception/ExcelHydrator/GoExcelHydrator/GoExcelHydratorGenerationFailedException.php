<?php

namespace App\Exception\ExcelHydrator\GoExcelHydrator;

use App\Exception\BaseException;
use Throwable;

class GoExcelHydratorGenerationFailedException extends BaseException
{
    public function __construct(
        string $message = "Ошибка генерации excel файла",
        int $httpStatusCode = 400,
        array $context = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $httpStatusCode, $context, $code, $previous);
    }
}
