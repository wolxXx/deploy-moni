<?php

declare(strict_types=1);

namespace Application\DataObject;

class Group
{
    protected array $deployments = [];

    public function __construct(
        public string $name,
    ) {
    }

    public function add(Deployment $deployment): static
    {
        $this->deployments[] = $deployment;

        return $this;
    }

    /**
     * @return Deployment[]
     */
    public function get(): array
    {
        return $this->deployments;
    }
}
