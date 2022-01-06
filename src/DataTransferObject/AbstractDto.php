<?php

declare(strict_types=1);

namespace App\DataTransferObject;

abstract class AbstractDto
{
    public function fromArray(array $data): self
    {
        /**
         * data = [
         *  'id' => 1
         * }.
         */
        $dto = new static();

        foreach ($data as $property => $value) {
            $setterMethod = sprintf('set%s', ucfirst($property));

            if (method_exists($this, $setterMethod)) {
                $dto->{$setterMethod}($value);
            }
        }

        return $dto;
    }

    public function toArray(): array
    {
        $reflectionClass = new \ReflectionClass($this);
        $reflectionProperties = $reflectionClass->getProperties();

        $data = [];

        foreach ($reflectionProperties as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);
            $data[$reflectionProperty->getName()] = $reflectionProperty->getValue($this);
        }

        return $data;
    }
}
