<?php

declare(strict_types=1);

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION, priority: -100)]
final class ExceptionListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string          $environment,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        // Let Symfony handle exceptions in dev
        if ('prod' !== $this->environment) {
            return;
        }

        $exception  = $event->getThrowable();
        $request    = $event->getRequest();
        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        // Log 5xx errors
        if ($statusCode >= 500) {
            $this->logger->critical('Unhandled exception', [
                'exception' => $exception,
                'url'       => $request->getUri(),
            ]);
        }

        // Return JSON for API requests
        if ($this->isApiRequest($request)) {
            $event->setResponse(new JsonResponse([
                'error'   => Response::$statusTexts[$statusCode] ?? 'Error',
                'status'  => $statusCode,
            ], $statusCode));
        }
    }

    private function isApiRequest(\Symfony\Component\HttpFoundation\Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api')
            || $request->getAcceptableContentTypes()[0] === 'application/json';
    }
}
