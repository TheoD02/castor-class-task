<?php

declare(strict_types=1);

namespace TheoD02\Castor\Classes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsTaskClass
{
    public function __construct(
        public readonly ?string $namespace = null,
    ) {
    }
}
