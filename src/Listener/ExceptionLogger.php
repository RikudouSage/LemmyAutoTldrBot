<?php

namespace App\Listener;

use App\Exception\ContentFetchingFailedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionLogger implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onException'];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof ContentFetchingFailedException) {
            return;
        }

        error_log(json_encode([
            'error' => $exception->getMessage(),
        ], flags: JSON_THROW_ON_ERROR));
    }
}
