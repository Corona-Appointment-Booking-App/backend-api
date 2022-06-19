<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Uid\AbstractUid;

interface EntityInterface
{
    public function getId(): int;

    public function setId(int $id): void;

    public function getUuid(): AbstractUid;

    public function setUuid(AbstractUid $uuid): void;

    public function getSerializationGroups(): array;
}
