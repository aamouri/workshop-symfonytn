<?php

namespace AppBundle\Listener;

use AppBundle\Event\TriggerTimeEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OpenDaysListener implements EventSubscriberInterface
{
    private $dispatcher;
    private $logger;

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->dispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $now = new \Datetime();

        if (true) {
            $this->dispatcher->dispatch(TriggerTimeEvent::TRIGGER_TIME, new TriggerTimeEvent($now));
        }

        // 0 = sunday | 6 = saturday
        if (0 === (int)$now->format('w') || 6 === (int)$now->format('w')) {
            $event->setResponse(
                new Response('<html><body>Sorry the website is now closed.</body></html>')
            );
        }
    }

    public function onTriggeredTime(TriggerTimeEvent $event)
    {
        $this->logger->info('The website will be closed in 1 day.',
            array('triggerTime' => $event->getTriggeredTime())
        );
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 255),
            TriggerTimeEvent::TRIGGER_TIME => array('onTriggeredTime')
        );
    }
}