<?php
namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => [
                ['notFoundHttpException', 10]
            ],
        ];
    }

    public function notFoundHttpException(ExceptionEvent $event)
    {
        if (preg_match('#\/ws\/#', $event->getRequest()->getRequestUri())) {
            $response  =  new Response('Format de donnée incorrect', 400);
            $event->setResponse($response);
        }
    }
}
