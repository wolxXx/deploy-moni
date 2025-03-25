<?php

declare(strict_types=1);

namespace Application\DataObject;

class Deployment
{
    public function __construct(
        public int $id,
        public string $name,
        public string $createdAt,
    ) {
    }
}
