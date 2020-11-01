<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    /**
     * Estrutura e global exceptions
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $throwable = $event->getThrowable();
        $message = $throwable->getMessage() ? $throwable->getMessage() : "";
        $httpCode = $throwable->getCode() ? $throwable->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        $response = new Response($message, $httpCode);
        $event->setResponse($response);
    }
}