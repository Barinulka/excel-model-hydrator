<?php

namespace App\Service\Excel;

interface ExcelHydratorInterface
{
    /**
     * @param string $template
     * @param array  $data
     *
     * @return string Имя сгенерированного файла (например, hydrated_20250405_123456.xlsx)
     */
    public function hydrate(string $template, array $data): string;
}
