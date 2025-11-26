<?php

namespace CompWright\FullNameParser;

use CompWright\FullNameParser\Exception\IncorrectInputException;

class Name
{
    public const string PART_TITLE = 'title';
    public const string PART_FIRST_NAME = 'first';
    public const string PART_MIDDLE_NAME = 'middle';
    public const string PART_LAST_NAME = 'last';
    public const string PART_NICKNAME = 'nick';
    public const string PART_SUFFIX = 'suffix';
    public const string PART_ERRORS = 'error';
    public const string PART_ALL = 'all';

    /**
     * Full name.
     */
    private string $fullName;

    /**
     * Leading initial part.
     */
    private string $leadingInitial;

    /**
     * First name part.
     */
    private string $firstName;

    /**
     * Nicknames part.
     */
    private string $nicknames;

    /**
     * Middle name part.
     */
    private string $middleName;

    /**
     * Last name part.
     */
    private string $lastName;

    /**
     * Title part.
     */
    private string $academicTitle;

    /**
     * Suffixes part.
     */
    private string $suffix;

    /**
     * Array of parsing error messages.
     *
     * @var string[]
     */
    private array $errors;

    /**
     * Parsing result getter.
     *
     * @param string $part Name of part of name to return for.
     *
     * @return ($part is self::PART_ERRORS ? string[] : string) Return self if all parts needed, or array if errors needed, or string of part of name.
     */
    public function getPart(string $part): array|string
    {
        $value = match ($part) {
            self::PART_TITLE => $this->getAcademicTitle(),
            self::PART_FIRST_NAME => $this->getFirstName(),
            self::PART_MIDDLE_NAME => $this->getMiddleName(),
            self::PART_LAST_NAME => $this->getLastName(),
            self::PART_NICKNAME => $this->getNicknames(),
            self::PART_SUFFIX => $this->getSuffix(),
            self::PART_ERRORS => $this->getErrors(),
            default => null,
        };

        if (is_null($value)) {
            throw IncorrectInputException::new();
        }

        return $value;
    }

    /**
     * Array of errors getter.
     *
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors ?? [];
    }

    /**
     * Add error message to the array of errors.
     *
     * @param Exception\NameParsingException $ex Error to add.
     */
    public function addError(Exception\NameParsingException $ex): self
    {
        $this->errors[] = $ex->getMessage();
        return $this;
    }

    /**
     * First name getter.
     */
    public function getFirstName(): string
    {
        return $this->firstName ?? '';
    }

    /**
     * First name setter.
     *
     * @param string $firstName The first name.
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Nicknames getter.
     */
    public function getNicknames(): string
    {
        return $this->nicknames ?? '';
    }

    /**
     * Nicknames setter.
     *
     * @param string $nicknames The nicknames.
     */
    public function setNicknames(string $nicknames): self
    {
        $this->nicknames = $nicknames;

        return $this;
    }

    /**
     * Middle name getter.
     */
    public function getMiddleName(): string
    {
        return $this->middleName ?? '';
    }

    /**
     * Middle name setter.
     *
     * @param string $middleName The middle name.
     */
    public function setMiddleName(string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Last name getter.
     */
    public function getLastName(): string
    {
        return $this->lastName ?? '';
    }

    /**
     * Last name setter.
     *
     * @param string $lastName The last name.
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Suffixes getter.
     */
    public function getSuffix(): string
    {
        return $this->suffix ?? '';
    }

    /**
     * Suffixes setter.
     *
     * @param string $suffix The suffix.
     */
    public function setSuffix(string $suffix): self
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Leading initial getter.
     */
    public function getLeadingInitial(): string
    {
        return $this->leadingInitial ?? '';
    }

    /**
     * Leading initial setter.
     *
     * @param string $leadingInitial The leading initial.
     */
    public function setLeadingInitial(string $leadingInitial): self
    {
        $this->leadingInitial = $leadingInitial;

        return $this;
    }

    /**
     * Academic title getter.
     */
    public function getAcademicTitle(): string
    {
        return $this->academicTitle ?? '';
    }

    /**
     * Title setter.
     *
     * @param string $academicTitle The academic title.
     */
    public function setAcademicTitle(string $academicTitle): self
    {
        $this->academicTitle = $academicTitle;

        return $this;
    }

    /**
     * Full name getter.
     */
    public function getFullName(): string
    {
        return $this->fullName ?? '';
    }

    /**
     * Full name setter.
     *
     * @param string $full_name The full name.
     */
    public function setFullName(string $full_name): self
    {
        $this->fullName = $full_name;

        return $this;
    }
}
