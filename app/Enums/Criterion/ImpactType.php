<?php

declare(strict_types=1);

namespace App\Enums\Criterion;

use BenSampo\Enum\Enum;

/**
 * @method static static Negative()
 * @method static static Positive()
 * @method static static Neutral()
 */
final class ImpactType extends Enum
{
    const Cost = 0;
    const Benefit = 1;
}
