<?php

declare(strict_types=1);

#[Attribute(Attribute::TARGET_CLASS)]
class AsTaskClass
{
    public function __construct(
        public readonly ?string $namespace = null,
    ) {
    }
}
