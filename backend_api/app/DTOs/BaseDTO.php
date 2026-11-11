<?php

namespace App\DTOs;

abstract class BaseDTO
{
    abstract public static function fromArray(array $data): static;

    abstract public function toArray(): array;
}

