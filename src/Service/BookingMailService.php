<?php

declare(strict_types=1);

namespace App\Service;

use App\AppConstants;
use App\AppContext;
use App\Entity\Booking;
use App\Entity\BookingParticipant;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class BookingMailService implements BookingMailServiceInterface
{
    private const SUBJECT_BOOKING_CONFIRMATION = 'Buchungsbestätigung';
    private const SUBJECT_BOOKING_CANCEL_CONFIRMATION = 'Buchung Stornierungsbestätigung';

    private const HTML_TEMPLATE_BOOKING_CONFIRMATION = 'emails/booking-confirmation.html.twig';
    private const HTML_TEMPLATE_BOOKING_CANCEL_CONFIRMATION = 'emails/booking-cancel-confirmation.html.twig';

    private MailerInterface $mailer;

    private LoggerInterface $logger;

    private AppContext $appContext;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger, AppContext $appContext)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->appContext = $appContext;
    }

    public function sendEmailConfirmation(Booking $booking, string $type): void
    {
        $email = match ($type) {
            Booking::STATUS_CONFIRMED => $this->getTemplatedEmailWithData(
                $booking,
                self::SUBJECT_BOOKING_CONFIRMATION,
                self::HTML_TEMPLATE_BOOKING_CONFIRMATION
            ),
            Booking::STATUS_CANCELLED => $this->getTemplatedEmailWithData(
                $booking,
                self::SUBJECT_BOOKING_CANCEL_CONFIRMATION,
                self::HTML_TEMPLATE_BOOKING_CANCEL_CONFIRMATION
            ),
            default => throw new \InvalidArgumentException(sprintf('sending email confirmation for type %s is not supported.', $type)),
        };

        $loggerContext = [
            'id' => $booking->getUuid()->toRfc4122(),
            'addresses' => $email->getTo(),
            'type' => $type,
        ];

        try {
            $this->mailer->send($email);
            $this->logger->info('booking confirmation for booking {id} was sent', $loggerContext);
        } catch (\Throwable $e) {
            $loggerContext['exception'] = $e;
            $this->logger->error('unable to send booking confirmation for booking {id}', $loggerContext);
        } finally {
            $this->logger->debug('trying to send booking confirmation for booking {id}', $loggerContext);
        }
    }

    private function getTemplatedEmailWithData(Booking $booking, string $subject, string $htmlTemplate): TemplatedEmail
    {
        $toAddresses = [];
        foreach ($booking->getParticipants() as $participant) {
            /* @var BookingParticipant $participant */
            $toAddresses[] = new Address($participant->getEmail());
        }

        return (new TemplatedEmail())
            ->from($this->appContext->getContextMailSender())
            ->to(...$toAddresses)
            ->subject($subject)
            ->htmlTemplate($htmlTemplate)
            ->context([
                'testCenter' => [
                    'name' => $booking->getTestCenter()->getName(),
                    'address' => $booking->getTestCenter()->getAddress(),
                ],
                'bookingCode' => $booking->getCode(),
                'bookingDate' => $booking->getTime()->format(AppConstants::FORMAT_EMAIL_CONFIRMATION),
                'cancelUrl' => sprintf(
                    '%s/#/booking/cancel/%s',
                    $this->appContext->getContextFrontendUrl(),
                    $booking->getUuid()->toRfc4122()
                ),
            ]);
    }
}
