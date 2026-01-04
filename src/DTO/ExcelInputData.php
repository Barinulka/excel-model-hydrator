<?php

namespace App\DTO;

class ExcelInputData
{
    public function __construct(
        public readonly array $sheetData
    ) {
    }
}
