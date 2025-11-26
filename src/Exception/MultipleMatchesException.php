<?php

namespace CompWright\FullNameParser\Exception;

class MultipleMatchesException extends NameParsingException
{
    public const MESSAGE = 'The regex being used has multiple matches.';
}
