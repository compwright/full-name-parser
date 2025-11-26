<?php

namespace CompWright\FullNameParser\Exception;

use RuntimeException;

/**
 * @phpstan-consistent-constructor
 */
class NameParsingException extends RuntimeException
{
    public const MESSAGE = 'An unexpected parsing error occurred';

    public static function new(int|string ...$args): static
    {
        return new static(sprintf(static::MESSAGE, ...$args));
    }
}
