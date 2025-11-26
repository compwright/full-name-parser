<?php

namespace CompWright\FullNameParser;

class Parser
{
    /*
     * The regex use is a bit tricky.  *Everything* matched by the regex will be replaced,
     * but you can select a particular parenthesized submatch to be returned.
     * Also, note that each regex requires that the preceding ones have been run, and matches chopped out.
     */

    /**
     * Parts with surrounding punctuation as nicknames.
     */
    public const string REGEX_NICKNAMES = "/([\[('‘“\"]+)(.+?)(['’”\"\])]+)/";

    /**
     * Regex for titles.
     * Each title gets a "\.*" behind it.
     * It cannot be the last word in name.
     */
    public const string REGEX_TITLES = "/((^| )(%s)\.* )/";

    /**
     * Regex for suffixes.
     * Before suffix must be space.
     * Each suffix gets a "\.*" behind it. Numeral suffixes does not contain dots behind it.
     * After regular suffix can go extra suffixes - comma separated before each word to the end of string.
     * Or there must be end of string, space or comma after regular suffix.
     */
    public const string REGEX_SUFFIX = "/( (((%s)\.*)|(%s))(((,+ +\S+)*$)|( |,)))/";

    /**
     * Regex for last name.
     */
    public const string REGEX_LAST_NAME = "/(?!^)\b(([^ ]+ y|%s)\.? )*[^ ]+$/i";

    /**
     * Regex for initials.
     * Note the lookahead, which isn't returned or replaced.
     */
    public const string REGEX_LEADING_INITIAL = "/^(.\.*)(?= \p{L}{2})/";

    /**
     * Regex for first name.
     */
    public const string REGEX_FIRST_NAME = "/^[^ ]+/";

    /**
     * List of possible suffixes.
     *
     * @var string[]
     */
    public const array SUFFIXES = [
        'esq',
        'esquire',
        'jr',
        'sr',
        'phd',
    ];

    /**
     * List of numeral suffixes.
     *
     * @var string[]
     */
    public const array NUMERAL_SUFFIXES = [
        '2',
        'iii',
        'ii',
        'iv',
        'v',
    ];

    /**
     * List of possible prefixes.
     *
     * @var string[]
     */
    public const array PREFIXES = [
        'bar',
        'ben',
        'bin',
        'da',
        'dal',
        'de la',
        'de',
        'del',
        'der',
        'di',
        'ibn',
        'la',
        'le',
        'san',
        'st',
        'ste',
        'van der',
        'van den',
        'van',
        'vel',
        'von',
    ];

    /**
     * List of normal cased suffixes.
     *
     * @var string[]
     */
    public const array FORCED_CASE = [
        'e',
        'y',
        'av',
        'af',
        'da',
        'dal',
        'de',
        'del',
        'der',
        'di',
        'la',
        'le',
        'van',
        'der',
        'den',
        'vel',
        'von',
        'II',
        'III',
        'IV',
        'V',
        'J.D.',
        'LL.M.',
        'M.D.',
        'D.O.',
        'D.C.',
        'Ph.D.',
    ];

    /**
     * List of possible titles.
     *
     * @var string[]
     */
    public const array TITLES = ['ms', 'miss', 'mrs', 'mr', 'prof', 'dr'];

    /**
     * Temporary variable of non-parsed name part.
     */
    private string $name_token;

    /**
     * Object which contains parsed name parts.
     */
    private Name $name;

    /**
     * Parser constructor.
     *
     * @param string[] $suffixes Array of suffixes.
     * @param string[] $numeral_suffixes Array of numeral suffixes.
     * @param string[] $prefixes Array of prefixes.
     * @param string[] $academic_titles Array of titles.
     * @param bool $mandatory_first_name Throw error if first name not found.
     * @param bool $mandatory_middle_name Throw error if first name not found.
     * @param bool $mandatory_last_name Throw error if last name not found.
     * @param null|Name::PART_TITLE|Name::PART_FIRST_NAME|Name::PART_MIDDLE_NAME|Name::PART_LAST_NAME|Name::PART_NICKNAME|Name::PART_SUFFIX $name_part Name part to return.
     * @param bool $stop_on_error Stop on errors.
     * @param bool $fix_case Make name parts uppercase first letter.
     */
    public function __construct(
        private array $suffixes = self::SUFFIXES,
        private array $numeral_suffixes = self::NUMERAL_SUFFIXES,
        private array $prefixes = self::PREFIXES,
        private array $academic_titles = self::TITLES,
        private bool $mandatory_first_name = true,
        private bool $mandatory_middle_name = true,
        private bool $mandatory_last_name = true,
        private ?string $name_part = null,
        private bool $stop_on_error = true,
        private bool $fix_case = false,
    ) {
    }

    /**
     * Parse the name into its constituent parts.
     *
     * @param string $name String to parse.
     *
     * @throws Exception\NameParsingException
     */
    public function parse(string $name): Name
    {
        $this->name = new Name();

        if ($name === '') {
            $this->handleError(Exception\IncorrectInputException::new());
            return $this->name;
        }

        if ($this->isFixCase()) {
            $words = explode(' ', $this->normalize($name));
            $casedName = [];
            foreach ($words as $word) {
                $casedName[] = $this->fixParsedNameCase($word);
            }
            $this->name->setFullName(implode(' ', $casedName));
        } else {
            $this->name->setFullName($this->normalize($name));
        }
        $this->name_token = $this->name->getFullName();

        $suffixes = implode("|", $this->getSuffixes());
        $numeral_suffixes = implode("|", $this->getNumeralSuffixes());
        $prefixes = implode("|", $this->getPrefixes());
        $academicTitles = implode("|", $this->getAcademicTitles());

        $this->findAcademicTitle($academicTitles);
        $this->findNicknames();

        $this->findSuffix($numeral_suffixes, $suffixes);
        $this->flipNameToken();

        $this->findLastName($prefixes);
        $this->findLeadingInitial();
        $this->findFirstName();
        $this->findMiddleName();

        return $this->name;
    }

    /**
     * Throw exception if set in options.
     *
     * @param Exception\NameParsingException $ex Error to throw or add to error array.
     *
     * @throws Exception\NameParsingException
     */
    private function handleError(Exception\NameParsingException $ex): self
    {
        $this->name->addError($ex);
        if ($this->isStopOnError()) {
            if ($ex instanceof Exception\ManyMiddleNamesException) {
                trigger_error($ex, E_USER_WARNING);
            } else {
                throw $ex;
            }
        }
        return $this;
    }

    /**
     * Makes each word in name string ucfirst.
     */
    private function fixParsedNameCase(string $word): string
    {
        if ($this->isFixCase()) {
            $forceCaseList = self::FORCED_CASE;
            $in_list = false;
            foreach ($forceCaseList as $item) {
                if (strtolower($word) === strtolower($item)) {
                    $in_list |= strtolower($word) === strtolower($item);
                    $word = $item;
                }
            }
            if (!$in_list) {
                $hyphenated = explode('-', $word);
                foreach ($hyphenated as $id => $part) {
                    $hyphenated[$id] = ucfirst(mb_strtolower($part));
                }
                $word = implode('-', $hyphenated);
            }
        }
        return $word;
    }

    /**
     * Find and add academic title to Name object.
     *
     * @param string $academicTitles Regex to find titles.
     */
    private function findAcademicTitle(string $academicTitles): self
    {
        $regex = sprintf(self::REGEX_TITLES, $academicTitles);
        $title = $this->findWithRegex($regex, 1);
        if ($title) {
            $this->name->setAcademicTitle($title);
            $this->name_token = str_ireplace($title, "", $this->name_token);
        }

        return $this;
    }

    /**
     * Find and add nicknames to Name object.
     *
     * @throws Exception\NameParsingException
     */
    private function findNicknames(): self
    {
        $nicknames = $this->findWithRegex(self::REGEX_NICKNAMES, 2);
        if ($nicknames) {
            // Need to fix case because first char was bracket or quote.
            $this->name->setNicknames($this->fixParsedNameCase($nicknames));
            $this->removeTokenWithRegex(self::REGEX_NICKNAMES);
        }

        return $this;
    }

    /**
     * Find and add suffixes to Name object.
     *
     * @param string $numeral_suffixes The numeral suffixes to be searched for.
     * @param string $suffixes The suffixes to be searched for.
     *
     * @throws Exception\NameParsingException
     */
    private function findSuffix(string $numeral_suffixes, string $suffixes): self
    {
        $regex = sprintf(self::REGEX_SUFFIX, $suffixes, $numeral_suffixes);
        $suffix = $this->findWithRegex($regex, 1);
        if ($suffix) {
            // Remove founded suffix.
            $regex_suffix = preg_quote($suffix);
            $this->removeTokenWithRegex("/ ($regex_suffix)($| |,)/", '$2');

            $this->name->setSuffix($suffix);
        }

        return $this;
    }

    /**
     * Find and add last name to Name object.
     *
     * @param string $prefixes Regex to find prefixes.
     *
     * @throws Exception\NameParsingException
     */
    private function findLastName(string $prefixes): self
    {
        $regex = sprintf(self::REGEX_LAST_NAME, $prefixes);
        $lastName = $this->findWithRegex($regex);
        if ($lastName) {
            $this->name->setLastName($lastName);
            $this->removeTokenWithRegex($regex);
        } elseif ($this->mandatory_last_name) {
            $this->handleError(Exception\LastNameNotFoundException::new());
        }

        return $this;
    }

    /**
     * Find and add first name to Name object.
     *
     * @throws Exception\NameParsingException
     */
    private function findFirstName(): self
    {
        $lastName = $this->findWithRegex(self::REGEX_FIRST_NAME);
        if ($lastName) {
            $this->name->setFirstName($lastName);
            $this->removeTokenWithRegex(self::REGEX_FIRST_NAME);
        } elseif ($this->mandatory_first_name) {
            $this->handleError(Exception\FirstNameNotFoundException::new());
        }

        return $this;
    }

    /**
     * Find and add leading initial to Name object.
     *
     * @throws Exception\NameParsingException
     */
    private function findLeadingInitial(): self
    {
        $leadingInitial = $this->findWithRegex(self::REGEX_LEADING_INITIAL, 1);
        if ($leadingInitial) {
            $this->name->setLeadingInitial($leadingInitial);
            $this->removeTokenWithRegex(self::REGEX_LEADING_INITIAL);
        }

        return $this;
    }

    /**
     * Find and add middle name to Name object.
     *
     * @throws Exception\NameParsingException
     */
    private function findMiddleName(): self
    {
        $middleName = $this->name_token;
        $count = count(explode(' ', $middleName));
        if ($this->mandatory_middle_name && $count > 2) {
            $this->handleError(Exception\ManyMiddleNamesException::new($count));
        }
        if ($middleName) {
            $this->name->setMiddleName($middleName);
        }

        return $this;
    }

    /**
     * Find and return part of name for regex.
     *
     * @param string $regex Regex to search.
     * @param int $submatchIndex Index of regex part.
     */
    private function findWithRegex(string $regex, int $submatchIndex = 0): string|false
    {
        // unicode + case-insensitive
        $regex = $regex . "ui";
        preg_match($regex, $this->name_token, $match);
        $subset = (isset($match[$submatchIndex])) ? $match[$submatchIndex] : false;
        if ($subset === false) {
            return false;
        }
        // No need commas and spaces in name parts.
        $subset = $this->normalize($subset);
        return $subset;
    }

    /**
     * Remove founded part from name string.
     *
     * @param string $regex Regex to remove name part.
     * @param string $replacement String to replace.
     *
     * @throws Exception\NameParsingException
     */
    private function removeTokenWithRegex(string $regex, string $replacement = ' '): self
    {
        $numReplacements = 0;
        $tokenRemoved = preg_replace($regex . 'ui', $replacement, $this->name_token, -1, $numReplacements);
        if ($numReplacements > 1) {
            $this->handleError(Exception\MultipleMatchesException::new());
        }

        $this->name_token = $this->normalize($tokenRemoved ?? '');

        return $this;
    }

    /**
     * Removes extra whitespace and punctuation from string
     * Strips whitespace chars from ends, strips redundant whitespace, converts
     * whitespace chars to " ".
     */
    private function normalize(string $taintedString): string
    {
        // Remove any kind of invisible character from the start.
        $taintedString = preg_replace("#^\s*#u", "", $taintedString) ?? '';
        // Remove any kind of invisible character from the end.
        $taintedString = preg_replace("#\s*$#u", "", $taintedString) ?? '';
        // Add exception so that non-breaking space characters are not stripped during norm function.
        if (substr_count($taintedString, "\xc2\xa0") == 0) {
            // Replace any kind of invisible character in string to whitespace.
            $taintedString = preg_replace("#\s+#u", " ", $taintedString) ?? '';
        }
        // Replace two commas to one.
        $taintedString = preg_replace("(, ?, ?)", ", ", $taintedString) ?? '';
        // Remove commas and spaces from the string.
        $taintedString = trim($taintedString, " ,");

        return $taintedString;
    }

    /**
     * Flip name around comma.
     *
     * @throws Exception\NameParsingException
     */
    private function flipNameToken(): self
    {
        $this->name_token = $this->flipStringPartsAround($this->name_token, ",");
        return $this;
    }

    /**
     * Flips the front and back parts of a name with one another.
     * Front and back are determined by a specified character somewhere in the
     * middle of the string.
     *
     * @param string $string String to flip.
     * @param string $char Char to flip around for.
     *
     * @throws Exception\NameParsingException
     */
    private function flipStringPartsAround(string $string, string $char): string
    {
        $substrings = preg_split("/$char/u", $string);

        if ($substrings !== false && count($substrings) === 2) {
            $string = $substrings[1] . " " . $substrings[0];
            $string = $this->normalize($string);
        } elseif ($substrings !== false && count($substrings) > 2) {
            $this->handleError(Exception\FlipStringException::new($char, $this->name->getFullName()));
        }

        return $string;
    }

    /**
     * Suffixes getter.
     *
     * @return string[]
     */
    public function getSuffixes(): array
    {
        return $this->suffixes;
    }

    /**
     * Suffixes setter.
     *
     * @param string[] $suffixes The suffixes to set.
     */
    public function setSuffixes(array $suffixes): self
    {
        $this->suffixes = $suffixes;
        return $this;
    }

    /**
     * Numeral suffixes getter.
     *
     * @return string[]
     */
    public function getNumeralSuffixes(): array
    {
        return $this->numeral_suffixes;
    }

    /**
     * Numeral suffixes setter.
     *
     * @param string[] $numeral_suffixes The numeral suffixes to set.
     */
    public function setNumeralSuffixes(array $numeral_suffixes): self
    {
        $this->numeral_suffixes = $numeral_suffixes;
        return $this;
    }

    /**
     * Prefixes getter.
     *
     * @return string[]
     */
    public function getPrefixes(): array
    {
        return $this->prefixes;
    }

    /**
     * Prefixes setter.
     *
     * @param string[] $prefixes The prefixes.
     */
    public function setPrefixes(array $prefixes): self
    {
        $this->prefixes = $prefixes;
        return $this;
    }

    /**
     * Titles getter.
     *
     * @return string[]
     */
    public function getAcademicTitles(): array
    {
        return $this->academic_titles;
    }

    /**
     * Titles setter.
     *
     * @param string[] $academicTitles The academic titles.
     */
    public function setAcademicTitles(array $academicTitles): self
    {
        $this->academic_titles = $academicTitles;
        return $this;
    }

    /**
     * Name part getter.
     *
     * @return null|Name::PART_TITLE|Name::PART_FIRST_NAME|Name::PART_MIDDLE_NAME|Name::PART_LAST_NAME|Name::PART_NICKNAME|Name::PART_SUFFIX Name of part of name to return.
     */
    public function getNamePart(): ?string
    {
        return $this->name_part;
    }

    /**
     * Name part setter.
     *
     * @param Name::PART_TITLE|Name::PART_FIRST_NAME|Name::PART_MIDDLE_NAME|Name::PART_LAST_NAME|Name::PART_NICKNAME|Name::PART_SUFFIX $namePart Name of part of name to return.
     */
    public function setNamePart(string $namePart): self
    {
        $this->name_part = $namePart;
        return $this;
    }

    /**
     * Stop on error getter.
     */
    public function isStopOnError(): bool
    {
        return $this->stop_on_error;
    }

    /**
     * Stop on error setter.
     *
     * @param bool $stopOnError Stop when get parse error.
     */
    public function setStopOnError(bool $stopOnError): self
    {
        $this->stop_on_error = $stopOnError;
        return $this;
    }

    /**
     * Fix case getter.
     */
    public function isFixCase(): bool
    {
        return $this->fix_case;
    }

    /**
     * Fix case setter.
     *
     * @param bool $fixCase Fix case when parse.
     */
    public function setFixCase(bool $fixCase): self
    {
        $this->fix_case = $fixCase;
        return $this;
    }
}
