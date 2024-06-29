<?php

declare(strict_types=1);

namespace App\Enums\Auth;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TokenName extends Enum
{
    const APIToken = 0;
}
