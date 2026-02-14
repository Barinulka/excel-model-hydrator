<?php

namespace App\EventListener;

use App\Exception\BaseException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener(event: 'kernel.exception', priority: 200)]
final class ExceptionListener
{
    public function __construct(
        private readonly LoggerInterface $exceptionLogger,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        if (!$this->shouldHandleAsJson($event->getRequest())) {
            return;
        }

        $exception = $event->getThrowable();

        if ($exception instanceof BaseException) {
            $this->logInfoException($exception);
            $prepareResponse = $this->prepareException($exception);
        } elseif ($exception instanceof HttpExceptionInterface) {
            $this->logErrorException($exception);
            $prepareResponse = $this->prepareHttpException($exception);
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

    private function prepareHttpException(HttpExceptionInterface $exception): array
    {
        $statusCode = $exception->getStatusCode();

        return [
            'code' => $statusCode,
            'message' => $statusCode >= 500 ? 'Внутренняя ошибка сервера' : $exception->getMessage(),
        ];
    }

    private function prepareErrorException(): array
    {
        return [
            'code' => 500,
            'message' => "Внутренняя ошибка сервера",
        ];
    }

    private function shouldHandleAsJson(Request $request): bool
    {
        if (str_starts_with($request->getPathInfo(), '/excel')) {
            return true;
        }

        $acceptHeader = (string) $request->headers->get('Accept', '');

        return $request->isXmlHttpRequest() || str_contains($acceptHeader, 'application/json');
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
