<?php

namespace CompWright\FullNameParser\Exception;

class LastNameNotFoundException extends NameParsingException
{
    public const MESSAGE = 'Couldn\'t find a last name.';
}
