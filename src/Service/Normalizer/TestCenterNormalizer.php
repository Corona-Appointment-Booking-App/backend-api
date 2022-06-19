<?php

declare(strict_types=1);

namespace App\Service\Normalizer;

use App\AppConstants;
use App\DataTransferObject\OpeningDayCollection;
use App\Entity\TestCenter;
use App\Service\Generator\OpeningDayGeneratorListInterface;
use App\Service\Generator\OpeningDayGeneratorTextInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TestCenterNormalizer implements ContextAwareNormalizerInterface
{
    private ObjectNormalizer $normalizer;

    private OpeningDayGeneratorTextInterface $openingDayGeneratorText;

    private OpeningDayGeneratorListInterface $openingDayGeneratorList;

    public function __construct(
        ObjectNormalizer $normalizer,
        OpeningDayGeneratorTextInterface $openingDayGeneratorText,
        OpeningDayGeneratorListInterface $openingDayGeneratorList
    ) {
        $this->normalizer = $normalizer;
        $this->openingDayGeneratorText = $openingDayGeneratorText;
        $this->openingDayGeneratorList = $openingDayGeneratorList;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var array $data */
        /** @var TestCenter $object */
        $data = $this->normalizer->normalize($object, $format, $context);
        $data['generatedOpeningDaysText'] = null;
        $data['availableNextOpeningDays'] = [];

        $openingDays = $object->getOpeningDays();

        if ($openingDays) {
            $openingDaysCollection = (new OpeningDayCollection())->createOpeningDayCollectionFromArray($openingDays);
            $data['generatedOpeningDaysText'] = $this->openingDayGeneratorText->generateOpeningDaysText($openingDaysCollection);

            $generatedNextOpeningDaysCollection = $this->openingDayGeneratorList->generateOpeningDaysForDays($openingDaysCollection, AppConstants::FORMAT_DATE_TODAY);
            foreach ($generatedNextOpeningDaysCollection->getOpeningDays() as $openingDayGeneratorListDto) {
                $data['availableNextOpeningDays'][] = $openingDayGeneratorListDto->toArray();
            }
        }

        unset($openingDays);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof TestCenter;
    }
}
