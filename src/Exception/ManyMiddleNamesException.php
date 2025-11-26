<?php

namespace CompWright\FullNameParser\Exception;

/**
 * @method static self new(int $count)
 */
class ManyMiddleNamesException extends NameParsingException
{
    public const MESSAGE = 'Warning: %d middle names';
}
