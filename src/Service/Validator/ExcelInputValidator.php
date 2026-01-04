<?php

namespace App\Service\Validator;

use App\DTO\ExcelInputData;
use App\Exception\ExcelValidator\ExcelValidatorException;

class ExcelInputValidator
{
    /**
     * @throws ExcelValidatorException
     */
    public function validate(ExcelInputData $data): void
    {
        if (empty($data->sheetData)) {
            throw new ExcelValidatorException("Data is required");
        }

        foreach ($data->sheetData as $sheet => $cells) {
            if (!is_string($sheet)) {
                throw new ExcelValidatorException('Sheet name must be a string');
            }

            foreach (array_keys($cells) as $cell) {
                if (!preg_match('/^[A-Z]+[0-9]+$/i', $cell)) {
                    throw new ExcelValidatorException("Invalid cell reference: $cell");
                }
            }
        }
    }
}
