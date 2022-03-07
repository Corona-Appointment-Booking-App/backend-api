<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Booking;
use App\Event\BookingCancelledEvent;
use App\Event\BookingCreatedEvent;
use App\Service\BookingMailServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BookingEventListener implements EventSubscriberInterface
{
    private BookingMailServiceInterface $bookingMailService;

    public function __construct(BookingMailServiceInterface $bookingMailService)
    {
        $this->bookingMailService = $bookingMailService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BookingCreatedEvent::NAME => 'onBookingCreated',
            BookingCancelledEvent::NAME => 'onBookingCancelled'
        ];
    }

    public function onBookingCreated(BookingCreatedEvent $event): void
    {
        $this->bookingMailService->sendEmailConfirmation($event->getBooking(), Booking::STATUS_CONFIRMED);
    }

    public function onBookingCancelled(BookingCancelledEvent $event): void
    {
        $this->bookingMailService->sendEmailConfirmation($event->getBooking(), Booking::STATUS_CANCELLED);
    }
}