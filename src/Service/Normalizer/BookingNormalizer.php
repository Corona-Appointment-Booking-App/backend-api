<?php

declare(strict_types=1);

namespace App\Service\Normalizer;

use App\AppConstants;
use App\Entity\Booking;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class BookingNormalizer implements ContextAwareNormalizerInterface
{
    private ObjectNormalizer $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        try {
            $data['formattedCreatedDate'] = (new \DateTimeImmutable($data['createdAt']))->format(AppConstants::FORMAT_CREATED_AT);
            $data['formattedTime'] = (new \DateTimeImmutable($data['time']))->format(AppConstants::FORMAT_CREATED_AT);
        } catch (\Throwable $e) {
            $data['formattedCreatedDate'] = null;
            $data['formattedTime'] = null;
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Booking;
    }
}
