<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BookingParticipant;

class BookingParticipantRepository extends AbstractRepository
{
    protected function getEntityClass(): string
    {
        return BookingParticipant::class;
    }
}
