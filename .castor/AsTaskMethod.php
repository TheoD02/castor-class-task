<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;

#[Attribute(Attribute::TARGET_METHOD)]
class AsTaskMethod extends AsTask
{
}
