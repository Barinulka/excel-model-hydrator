<?php

namespace App\Service;

use App\DTO\ExcelInputData;
use App\DTO\GoHydrationDTO;
use App\Exception\ExcelValidator\ExcelValidatorException;
use App\Service\Excel\ExcelHydratorInterface;
use App\Service\Validator\ExcelInputValidator;

class ExcelModelGenerator
{
    public function __construct(
        private ExcelHydratorInterface $excelHydrator,
        private ExcelInputValidator $validator,
        private readonly string $defaultTemplate = 'model.xlsx',
        private readonly string $downloadBaseUrl = '/excel/output/'
    ) {}

    /**
     * @throws ExcelValidatorException
     */
    public function generateFromRequest(array $request): array
    {
        $sheetData = $request['sheetData'] ?? $request['data'] ?? [];
        $input = new ExcelInputData($sheetData);
        $this->validator->validate($input);

        $template = $request['template'] ?? $this->resolveTemplate($request['modelType'] ?? null);
        $hydrationDTO = new GoHydrationDTO(
            template: $template,
            data: $input->sheetData
        );

        return $this->generate($hydrationDTO);
    }

    private function resolveTemplate(?string $modelType): string
    {
        // TODO: Позже: маппинг типа модель - шаблон. Например: ['retail' => 'model_retail.xlsx']
        return $this->defaultTemplate;
    }

    /**
     * @param GoHydrationDTO $hydrationDTO
     *
     * @return array
     */
    private function generate(GoHydrationDTO $hydrationDTO): array
    {
        $filename = $this->excelHydrator->hydrate($hydrationDTO->template, $hydrationDTO->data);

        return [
            'filename' => $filename,
            'download_url' => rtrim($this->downloadBaseUrl, '/') . '/' . $filename,
        ];
    }
}
