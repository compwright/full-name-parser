<?php

namespace CompWright\FullNameParser;

use CompWright\FullNameParser\Exception\FirstNameNotFoundException;
use CompWright\FullNameParser\Exception\LastNameNotFoundException;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test case based on https://github.com/davidgorges/HumanNameParser.php
 */
class NameTest extends TestCase
{
    public const OUTPUT_STR = "failed to ensure correct %s (%s) in name %s";

    public static function generateNames(): Generator
    {
        yield [
            'original' => 'Björn O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'O\'Malley, Björn',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Bin Lin',
            'leading' => '',
            'first' => 'Bin',
            'nick' => '',
            'middle' => '',
            'last' => 'Lin',
            'suffix' => '',
        ];
        yield [
            'original' => 'Linda Jones',
            'leading' => '',
            'first' => 'Linda',
            'nick' => '',
            'middle' => '',
            'last' => 'Jones',
            'suffix' => '',
        ];
        yield [
            'original' => 'Jason H. Priem',
            'leading' => '',
            'first' => 'Jason',
            'nick' => '',
            'middle' => 'H.',
            'last' => 'Priem',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn O\'Malley-Muñoz',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => '',
            'last' => 'O\'Malley-Muñoz',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn C. O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => 'C.',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn "Bill" O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => 'Bill',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn ("Bill") O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => 'Bill',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn (Bill) O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => 'Bill',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn \'Bill\' O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => 'Bill',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn ("Wild Bill") O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => 'Wild Bill',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn C O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => 'C',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn C. R. O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => 'C. R.',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn Charles O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => 'Charles',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn Charles R. O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => 'Charles R.',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn van O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => '',
            'last' => 'van O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn Charles van der O\'Malley',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => 'Charles',
            'last' => 'van der O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn Charles O\'Malley y Muñoz',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => 'Charles',
            'last' => 'O\'Malley y Muñoz',
            'suffix' => '',
        ];
        yield [
            'original' => 'Björn O\'Malley, Jr.',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => 'Jr.',
        ];
        yield [
            'original' => 'Björn O\'Malley Jr',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => 'Jr',
        ];
        yield [
            'original' => 'O\'Malley, Björn Jr',
            'leading' => '',
            'first' => 'Björn',
            'nick' => '',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => 'Jr',
        ];
        yield [
            'original' => 'B O\'Malley',
            'leading' => '',
            'first' => 'B',
            'nick' => '',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'William Carlos Williams',
            'leading' => '',
            'first' => 'William',
            'nick' => '',
            'middle' => 'Carlos',
            'last' => 'Williams',
            'suffix' => '',
        ];
        yield [
            'original' => 'C. Björn Roger O\'Malley',
            'leading' => 'C.',
            'first' => 'Björn',
            'nick' => '',
            'middle' => 'Roger',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'B. C. O\'Malley',
            'leading' => '',
            'first' => 'B.',
            'nick' => '',
            'middle' => 'C.',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'B C O\'Malley',
            'leading' => '',
            'first' => 'B',
            'nick' => '',
            'middle' => 'C',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'B.J. Thomas',
            'leading' => '',
            'first' => 'B.J.',
            'nick' => '',
            'middle' => '',
            'last' => 'Thomas',
            'suffix' => '',
        ];
        yield [
            'original' => 'O\'Malley, C. Björn',
            'leading' => 'C.',
            'first' => 'Björn',
            'nick' => '',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => '',
        ];
        yield [
            'original' => 'O\'Malley, C. Björn III',
            'leading' => 'C.',
            'first' => 'Björn',
            'nick' => '',
            'middle' => '',
            'last' => 'O\'Malley',
            'suffix' => 'III',
        ];
        yield [
            'original' => 'O\'Malley y Muñoz, C. Björn Roger III',
            'leading' => 'C.',
            'first' => 'Björn',
            'nick' => '',
            'middle' => 'Roger',
            'last' => 'O\'Malley y Muñoz',
            'suffix' => 'III',
        ];

        yield 'https://github.com/ADCI/full-name-parser/issues/8' => [
            'original' => 'Arantes Rodrigues, R',
            'leading' => 'R',
            'first' => 'Arantes',
            'middle' => '',
            'last' => 'Rodrigues',
            'nick' => '',
            'suffix' => '',
        ];
    }

    private Parser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new Parser();
    }

    public function testSuffix(): void
    {
        $name = 'Björn O\'Malley, Jr.';
        $nameObject = $this->parser->parse($name);
        $this->assertEquals('O\'Malley', $nameObject->getLastName());
        $this->assertEquals('Björn', $nameObject->getFirstName());
        $this->assertEquals('Jr.', $nameObject->getSuffix());
    }

    public function testSimple(): void
    {
        $name = 'Hans Meiser';
        $nameObject = $this->parser->parse($name);
        $this->assertEquals('Hans', $nameObject->getFirstName());
        $this->assertEquals('Meiser', $nameObject->getLastName());
    }

    public function testReverse(): void
    {
        $name = 'Meiser, Hans';
        $nameObject = $this->parser->parse($name);
        $this->assertEquals('Hans', $nameObject->getFirstName());
        $this->assertEquals('Meiser', $nameObject->getLastName());
    }

    public function testReverseWithAcademicTitle(): void
    {
        $name = 'Dr. Meiser, Hans';
        $nameObject = $this->parser->parse($name);
        $this->assertEquals('Dr.', $nameObject->getAcademicTitle());
        $this->assertEquals('Meiser', $nameObject->getLastName());
        $this->assertEquals('Hans', $nameObject->getFirstName());
    }

    public function testAcademicTitle(): void
    {
        $name = 'Dr. Hans Meiser';
        $nameObject = $this->parser->parse($name);
        $this->assertEquals('Dr.', $nameObject->getAcademicTitle());
        $this->assertEquals('Meiser', $nameObject->getLastName());
        $this->assertEquals('Hans', $nameObject->getFirstName());
    }

    public function testLastNameWithPrefix(): void
    {
        $name = 'Björn van Olst';
        $nameObject = $this->parser->parse($name);
        $this->assertEquals('van Olst', $nameObject->getLastName());
        $this->assertEquals('Björn', $nameObject->getFirstName());
    }

    public function testNoFirstNameDefaultException(): void
    {
        $this->expectException(FirstNameNotFoundException::class);
        $name = 'Mr. Hyde';
        $this->parser->parse($name);
    }

    public function testNoLastNameDefaultException(): void
    {
        $this->expectException(LastNameNotFoundException::class);
        $name = 'Edward';
        $this->parser->parse($name);
    }

    public function testFirstNameNotMandatory(): void
    {
        $this->parser = new Parser(mandatory_first_name: false);
        $name = 'Dr. Jekyll';
        $nameObject = $this->parser->parse($name);
        $this->assertEquals('Dr.', $nameObject->getAcademicTitle());
        $this->assertEquals('Jekyll', $nameObject->getLastName());
    }

    public function testLastNameNotMandatory(): void
    {
        $this->parser = new Parser(mandatory_last_name: false);
        $name = 'Henry';
        $nameObject = $this->parser->parse($name);
        $this->assertEquals('Henry', $nameObject->getFirstName());
    }

    public function testMiddleNameNotMandatory(): void
    {
        $name = 'Dr. Hans Meiser';
        $this->parser = new Parser(mandatory_middle_name: false);
        $nameObject = $this->parser->parse($name);
        $this->assertEquals('Dr.', $nameObject->getAcademicTitle());
        $this->assertEquals('Meiser', $nameObject->getLastName());
        $this->assertEquals('Hans', $nameObject->getFirstName());
    }

    public function testFirstNameMandatory(): void
    {
        $this->expectException(FirstNameNotFoundException::class);
        $this->parser = new Parser(mandatory_first_name: true);
        $name = 'Mr. Hyde';
        $this->parser->parse($name);
    }

    public function testLastNameMandatory(): void
    {
        $this->expectException(LastNameNotFoundException::class);
        $this->parser = new Parser(mandatory_last_name: true);
        $name = 'Edward';
        $this->parser->parse($name);
    }

    #[DataProvider('generateNames')]
    public function testNameList(string $original, string $leading, string $first, string $nick, string $middle, string $last, string $suffix): void
    {
        $nameObject = $this->parser->parse($original);
        $this->assertEquals($leading, $nameObject->getLeadingInitial(), sprintf(self::OUTPUT_STR, "leading initial", $leading, $original));
        $this->assertEquals($first, $nameObject->getFirstName(), sprintf(self::OUTPUT_STR, "first name", $first, $original));
        $this->assertEquals($nick, $nameObject->getNickNames(), sprintf(self::OUTPUT_STR, "nickname", $nick, $original));
        $this->assertEquals($middle, $nameObject->getMiddleName(), sprintf(self::OUTPUT_STR, "middle name", $middle, $original));
        $this->assertEquals($last, $nameObject->getLastName(), sprintf(self::OUTPUT_STR, "last name", $last, $original));
        $this->assertEquals($suffix, $nameObject->getSuffix(), sprintf(self::OUTPUT_STR, "suffix", $suffix, $original));
    }
}
