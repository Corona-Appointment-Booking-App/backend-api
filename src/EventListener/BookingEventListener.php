<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\BookingCreatedEvent;
use App\Service\BookingServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BookingEventListener implements EventSubscriberInterface
{
    private BookingServiceInterface $bookingService;

    public function __construct(BookingServiceInterface $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BookingCreatedEvent::NAME => 'onBookingCreated'
        ];
    }

    public function onBookingCreated(BookingCreatedEvent $event): void
    {
        $this->bookingService->sendEmailConfirmation($event->getBooking());
    }
}