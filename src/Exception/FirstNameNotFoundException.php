<?php

namespace CompWright\FullNameParser\Exception;

class FirstNameNotFoundException extends NameParsingException
{
    public const MESSAGE = 'Couldn\'t find a first name.';
}
