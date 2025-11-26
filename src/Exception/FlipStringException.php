<?php

namespace CompWright\FullNameParser\Exception;

/**
 * @method static self new(string $char, string $full_name)
 */
class FlipStringException extends NameParsingException
{
    public const MESSAGE = "Can't flip around multiple '%s' characters in name string '%s'.";
}
