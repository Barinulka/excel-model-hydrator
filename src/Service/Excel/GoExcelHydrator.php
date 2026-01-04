<?php

namespace App\Service\Excel;

use App\Constants\HttpStatus;
use App\Exception\ExcelHydrator\GoExcelHydrator\GoExcelHydratorException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoExcelHydrator implements ExcelHydratorInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $goServiceUrl,
        private readonly ?LoggerInterface $hydratorLogger = null
    ) {}

    /**
     * @throws GoExcelHydratorException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function hydrate(string $template, array $data): string
    {
        try {
            $response = $this->httpClient->request('POST', $this->goServiceUrl . '/generate', [
                'json' => [
                    'template' => $template,
                    'data' => $data,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false); // не кидать исключение при ошибке статуса
            $jsonData = @json_decode($content, true);

            if ($statusCode >= 400) {
                $errorMessage = $jsonData['message'] ?? $content ?: 'Неизвестная ошибка GO сервиса';
                $this->hydratorLogger?->error('GoExcelHydrator error', [
                    'status_code' => $statusCode,
                    'error_message' => $errorMessage,
                    'template' => $template,
                    'data_keys' => array_keys($data),
                ]);

                throw new GoExcelHydratorException("Ошибка наполнения excel: {$errorMessage}", HttpStatus::BAD_REQUEST);
            }

            if (!isset($jsonData['filename'])) {
                throw new GoExcelHydratorException("Некорректный ответ от GO сервиса: не передан параметр 'filename'", HttpStatus::BAD_REQUEST);
            }

            return $jsonData['filename'];

        } catch (TransportExceptionInterface $e) {
            $this->hydratorLogger?->error("Ошибка подключения к GO сервису", ['exception' => $e]);
            throw new GoExcelHydratorException("Ошибка подключения к GO сервису", HttpStatus::BAD_REQUEST, [], 0, $e);
        }
    }
}
