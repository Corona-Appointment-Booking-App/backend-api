<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\BookingCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BookingEventListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BookingCreatedEvent::NAME => 'onBookingCreated'
        ];
    }

    public function onBookingCreated(BookingCreatedEvent $event): void
    {
        // todo add action
    }
}