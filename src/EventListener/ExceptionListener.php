<?php

namespace App\EventListener;

use App\Exception\BaseException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener(event: 'kernel.exception', priority: 200)]
final class ExceptionListener
{
    public function __construct(
        private readonly LoggerInterface $exceptionLogger,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof BaseException) {
            $this->logInfoException($exception);
            $prepareResponse = $this->prepareException($exception);
        } else {
            $this->logErrorException($exception);
            $prepareResponse = $this->prepareErrorException();
        }

        $response = new JsonResponse($prepareResponse, $prepareResponse['code']);

        $event->setResponse($response);
    }

    private function prepareException(BaseException $exception): array
    {
        $responseData = [
            'code' => $exception->getHttpStatusCode(),
            'message' => $exception->getMessage(),
        ];

        if (!empty($exception->getContext())) {
            $responseData['context'] = $exception->getContext();
        }

        return $responseData;
    }

    private function prepareErrorException(): array
    {
        return [
            'code' => 500,
            'message' => "Внутренняя ошибка сервера",
        ];
    }

    private function logInfoException(BaseException $exception): void
    {
        $logData = [
            'exception' => $exception,
        ];

        if (!empty($exception->getContext())) {
            $logData['context'] = $exception->getContext();
        }

        $this->exceptionLogger->info(
            sprintf(
                'Exception: %s (code: %d)',
                $exception->getMessage(),
                $exception->getHttpStatusCode()
            ),
            $logData
        );
    }

    private function logErrorException(\Throwable $exception): void
    {
        $this->exceptionLogger->error(
            sprintf(
                'Uncaught Exception: %s',
                $exception->getMessage()
            ),
            [
                'exception' => $exception,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]
        );
    }
}
