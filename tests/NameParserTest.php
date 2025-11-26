<?php

namespace CompWright\FullNameParser;

use CompWright\FullNameParser\Exception\FirstNameNotFoundException;
use CompWright\FullNameParser\Exception\FlipStringException;
use CompWright\FullNameParser\Exception\IncorrectInputException;
use CompWright\FullNameParser\Exception\LastNameNotFoundException;
use CompWright\FullNameParser\Exception\MultipleMatchesException;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test case based on https://github.com/dschnelldavis/parse-full-name .
 */
class NameParserTest extends TestCase
{
    private Parser $parser;

    public static function generateCaseNames(): Generator
    {
        // Switch to normal case by default in this case test. Parser not switch case by default.
        yield [
            'original' => 'MR. JÜAN MARTINEZ (MARTIN) DE LORENZO Y GUTIEREZ JR.',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'mr. jüan martinez (martin) de lorenzo y gutierez jr.',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        // Checking for not switch case if not need.
        // Switch option must be set false.
        yield [
            'original' => 'Mr. JÜAN MARTINEZ (MARTIN) DE LORENZO Y GUTIEREZ Jr.',
            'title' => 'Mr.',
            'first' => 'JÜAN',
            'middle' => 'MARTINEZ',
            'last' => 'DE LORENZO Y GUTIEREZ',
            'nick' => 'MARTIN',
            'suffix' => 'Jr.',
            'errors' => [],
            'fixCase' => false,
        ];
        // Checking for switch case if need.
        // Switch option must be set true.
        yield [
            'original' => 'Mr. JÜAN MARTINEZ (MARTIN) DE LORENZO Y GUTIEREZ JR.',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
            'fixCase' => true,
        ];
        // Checking for not switch case if not need.
        // Switch option must be set false.
        yield [
            'original' => 'mr. jüan martinez (martin) de lorenzo y gutierez jr.',
            'title' => 'mr.',
            'first' => 'jüan',
            'middle' => 'martinez',
            'last' => 'de lorenzo y gutierez',
            'nick' => 'martin',
            'suffix' => 'jr.',
            'errors' => [],
            'fixCase' => false,
        ];

        yield 'https://github.com/ADCI/full-name-parser/issues/10' => [
            'original' => 'Dr. John P. doe-ray, Jr.',
            'title' => 'Dr.',
            'first' => 'John',
            'middle' => 'P.',
            'last' => 'Doe-Ray',
            'nick' => '',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'Dr. John P. DOE-RAY, Jr.',
            'title' => 'Dr.',
            'first' => 'John',
            'middle' => 'P.',
            'last' => 'Doe-Ray',
            'nick' => '',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
    }

    /**
     * Lists of names.
     * Test case for complex parsing.
     */
    public static function generateNames(): Generator
    {
        yield [
            'original' => 'David Davis',
            'title' => '',
            'first' => 'David',
            'middle' => '',
            'last' => 'Davis',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];

        yield [
            'original' => 'Davis, David',
            'title' => '',
            'first' => 'David',
            'middle' => '',
            'last' => 'Davis',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];

        yield [
            'original' => 'Gerald Böck',
            'title' => '',
            'first' => 'Gerald',
            'middle' => '',
            'last' => 'Böck',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => 'Böck, Gerald',
            'title' => '',
            'first' => 'Gerald',
            'middle' => '',
            'last' => 'Böck',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];

        yield [
            'original' => 'David William Davis',
            'title' => '',
            'first' => 'David',
            'middle' => 'William',
            'last' => 'Davis',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => 'Davis, David William',
            'title' => '',
            'first' => 'David',
            'middle' => 'William',
            'last' => 'Davis',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];

        yield [
            'original' => 'Vincent Van Gogh',
            'title' => '',
            'first' => 'Vincent',
            'middle' => '',
            'last' => 'Van Gogh',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => 'Van Gogh, Vincent',
            'title' => '',
            'first' => 'Vincent',
            'middle' => '',
            'last' => 'Van Gogh',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];

        yield [
            'original' => 'Lorenzo de Médici',
            'title' => '',
            'first' => 'Lorenzo',
            'middle' => '',
            'last' => 'de Médici',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => 'de Médici, Lorenzo',
            'title' => '',
            'first' => 'Lorenzo',
            'middle' => '',
            'last' => 'de Médici',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];

        yield [
            'original' => 'Jüan de la Véña',
            'title' => '',
            'first' => 'Jüan',
            'middle' => '',
            'last' => 'de la Véña',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => 'de la Véña, Jüan',
            'title' => '',
            'first' => 'Jüan',
            'middle' => '',
            'last' => 'de la Véña',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];

        yield [
            'original' => 'Jüan Martinez de Lorenzo y Gutierez',
            'title' => '',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => 'de Lorenzo y Gutierez, Jüan Martinez',
            'title' => '',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];

        yield [
            'original' => 'Orenthal James "O. J." Simpson',
            'title' => '',
            'first' => 'Orenthal',
            'middle' => 'James',
            'last' => 'Simpson',
            'nick' => 'O. J.',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => 'Orenthal \'O. J.\' James Simpson',
            'title' => '',
            'first' => 'Orenthal',
            'middle' => 'James',
            'last' => 'Simpson',
            'nick' => 'O. J.',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => '(O. J.) Orenthal James Simpson',
            'title' => '',
            'first' => 'Orenthal',
            'middle' => 'James',
            'last' => 'Simpson',
            'nick' => 'O. J.',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => 'Simpson, Orenthal James "O. J."',
            'title' => '',
            'first' => 'Orenthal',
            'middle' => 'James',
            'last' => 'Simpson',
            'nick' => 'O. J.',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => 'Simpson, Orenthal ‘O. J.’ James',
            'title' => '',
            'first' => 'Orenthal',
            'middle' => 'James',
            'last' => 'Simpson',
            'nick' => 'O. J.',
            'suffix' => '',
            'errors' => [],
        ];
        yield [
            'original' => 'Simpson, [O. J.] Orenthal James',
            'title' => '',
            'first' => 'Orenthal',
            'middle' => 'James',
            'last' => 'Simpson',
            'nick' => 'O. J.',
            'suffix' => '',
            'errors' => [],
        ];

        yield [
            'original' => 'Sammy Davis, Jr.',
            'title' => '',
            'first' => 'Sammy',
            'middle' => '',
            'last' => 'Davis',
            'nick' => '',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'Davis, Sammy, Jr.',
            'title' => '',
            'first' => 'Sammy',
            'middle' => '',
            'last' => 'Davis',
            'nick' => '',
            'suffix' => 'Jr.',
            'errors' => [],
        ];

        // Multiple suffix.
        yield [
            'original' => 'John P. Doe-Ray, Jr., CLU, CFP, LUTC',
            'title' => '',
            'first' => 'John',
            'middle' => 'P.',
            'last' => 'Doe-Ray',
            'nick' => '',
            'suffix' => 'Jr., CLU, CFP, LUTC',
            'errors' => [],
        ];
        yield [
            'original' => 'Doe-Ray, John P., Jr., CLU, CFP, LUTC',
            'title' => '',
            'first' => 'John',
            'middle' => 'P.',
            'last' => 'Doe-Ray',
            'nick' => '',
            'suffix' => 'Jr., CLU, CFP, LUTC',
            'errors' => [],
        ];
        yield [
            'original' => 'John P. Doe-Ray Jr., CLU, CFP, LUTC',
            'title' => '',
            'first' => 'John',
            'middle' => 'P.',
            'last' => 'Doe-Ray',
            'nick' => '',
            'suffix' => 'Jr., CLU, CFP, LUTC',
            'errors' => [],
        ];
        yield [
            'original' => 'Doe-Ray, John P. Jr., CLU, CFP, LUTC',
            'title' => '',
            'first' => 'John',
            'middle' => 'P.',
            'last' => 'Doe-Ray',
            'nick' => '',
            'suffix' => 'Jr., CLU, CFP, LUTC',
            'errors' => [],
        ];

        yield [
            'original' => 'Dr. John P. Doe-Ray, Jr.',
            'title' => 'Dr.',
            'first' => 'John',
            'middle' => 'P.',
            'last' => 'Doe-Ray',
            'nick' => '',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'Dr. Doe-Ray, John P., Jr.',
            'title' => 'Dr.',
            'first' => 'John',
            'middle' => 'P.',
            'last' => 'Doe-Ray',
            'nick' => '',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'Doe-Ray, Dr. John P., Jr.',
            'title' => 'Dr.',
            'first' => 'John',
            'middle' => 'P.',
            'last' => 'Doe-Ray',
            'nick' => '',
            'suffix' => 'Jr.',
            'errors' => [],
        ];

        yield [
            'original' => 'Mr. Jüan Martinez (Martin) de Lorenzo y Gutierez Jr.',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'de Lorenzo y Gutierez, Mr. Jüan Martinez (Martin) Jr.',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'de Lorenzo y Gutierez, Mr. Jüan (Martin) Martinez Jr.',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'Mr. de Lorenzo y Gutierez, Jüan Martinez (Martin) Jr.',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'Mr. de Lorenzo y Gutierez, Jüan (Martin) Martinez Jr.',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'Mr. de Lorenzo y Gutierez Jr., Jüan Martinez (Martin)',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'Mr. de Lorenzo y Gutierez Jr., Jüan (Martin) Martinez',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'Mr. de Lorenzo y Gutierez, Jr. Jüan Martinez (Martin)',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];
        yield [
            'original' => 'Mr. de Lorenzo y Gutierez, Jr. Jüan (Martin) Martinez',
            'title' => 'Mr.',
            'first' => 'Jüan',
            'middle' => 'Martinez',
            'last' => 'de Lorenzo y Gutierez',
            'nick' => 'Martin',
            'suffix' => 'Jr.',
            'errors' => [],
        ];

        // Errors checking.
        // Garbage input.
        yield [
            'original' => 'as;dfkj ;aerha;sfa ef;oia;woeig hz;sofi hz;oifj;zoseifj zs;eofij z;soeif jzs;oefi jz;osif z;osefij zs;oif jz;soefihz;sodifh z;sofu hzsieufh zlsiudfh zksefiulzseofih ;zosufh ;oseihgfz;osef h:OSfih lziusefhaowieufyg oaweifugy',
            'title' => '',
            'first' => 'as;dfkj',
            'middle' => ';aerha;sfa ef;oia;woeig hz;sofi hz;oifj;zoseifj zs;eofij z;soeif jzs;oefi jz;osif z;osefij zs;oif jz;soefihz;sodifh z;sofu hzsieufh zlsiudfh zksefiulzseofih ;zosufh ;oseihgfz;osef h:OSfih lziusefhaowieufyg',
            'last' => 'oaweifugy',
            'nick' => '',
            'suffix' => '',
            'errors' => ['Warning: 19 middle names'],
        ];

        // Empty input.
        yield [
            'original' => '',
            'title' => '',
            'first' => '',
            'middle' => '',
            'last' => '',
            'nick' => '',
            'suffix' => '',
            'errors' => ['Incorrect input to parse.'],
        ];

        yield 'https://github.com/ADCI/full-name-parser/issues/1' => [
            'original' => 'John J Oliveri',
            'title' => '',
            'first' => 'John',
            'middle' => 'J',
            'last' => 'Oliveri',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];

        yield 'https://github.com/ADCI/full-name-parser/issues/2' => [
            'original' => 'Villuendas, M. V.',
            'title' => '',
            'first' => 'M.',
            'middle' => 'V.',
            'last' => 'Villuendas',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];

        yield 'https://github.com/ADCI/full-name-parser/issues/6' => [
            'original' => 'Jokubas Phillip Gardner ',
            'title' => '',
            'first' => 'Jokubas',
            'middle' => 'Phillip',
            'last' => 'Gardner',
            'nick' => '',
            'suffix' => '',
            'errors' => [],
        ];
    }

    /**
     * List from https://github.com/mklaber/node-another-name-parser
     * Except not valid strings.
     */
    public static function generateAdditionalNames(): Generator
    {
        yield [
            'original' => 'Doe, John',
            'title' => '',
            'first' => 'John',
            'middle' => '',
            'last' => 'Doe',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Doe, John P',
            'title' => '',
            'first' => 'John',
            'middle' => 'P',
            'last' => 'Doe',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Doe, Dr. John P',
            'title' => 'Dr.',
            'first' => 'John',
            'middle' => 'P',
            'last' => 'Doe',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'John R Doe-Smith',
            'title' => '',
            'first' => 'John',
            'middle' => 'R',
            'last' => 'Doe-Smith',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'John Doe',
            'title' => '',
            'first' => 'John',
            'middle' => '',
            'last' => 'Doe',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Mr. Anthony R Von Fange III',
            'title' => 'Mr.',
            'first' => 'Anthony',
            'middle' => 'R',
            'last' => 'Von Fange',
            'nick' => '',
            'suffix' => 'III',
        ];

        yield [
            'original' => 'Sara Ann Fraser',
            'title' => '',
            'first' => 'Sara',
            'middle' => 'Ann',
            'last' => 'Fraser',
            'nick' => '',
            'suffix' => '',
        ];

        // Compound first names. Not implemented.
        //
        // yield [
        //     'original' => 'Mary Ann Fraser',
        //     'title' => '',
        //     'first' => 'Mary Ann',
        //     'middle' => '',
        //     'last' => 'Fraser',
        //     'nick' => '',
        //     'suffix' => '',
        // ];
        //
        // yield [
        //     'original' => 'Fraser, Mary Ann',
        //     'title' => '',
        //     'first' => 'Mary Ann',
        //     'middle' => '',
        //     'last' => 'Fraser',
        //     'nick' => '',
        //     'suffix' => '',
        // ];
        //
        // yield [
        //     'original' => 'Jo Ellen Mary St. Louis',
        //     'title' => '',
        //     'first' => 'Jo Ellen',
        //     'middle' => 'Mary',
        //     'last' => 'St. Louis',
        //     'nick' => '',
        //     'suffix' => '',
        // ];

        yield [
            'original' => 'Adam',
            'title' => '',
            'first' => 'Adam',
            'middle' => '',
            'last' => '',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Donald "Don" Rex St. Louis',
            'title' => '',
            'first' => 'Donald',
            'middle' => 'Rex',
            'last' => 'St. Louis',
            'nick' => 'Don',
            'suffix' => '',
        ];

        yield [
            'original' => 'Donald (Don) Rex St. Louis',
            'title' => '',
            'first' => 'Donald',
            'middle' => 'Rex',
            'last' => 'St. Louis',
            'nick' => 'Don',
            'suffix' => '',
        ];

        yield [
            'original' => 'Mary Ann',
            'title' => '',
            'first' => 'Mary',
            'middle' => '',
            'last' => 'Ann',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Jonathan Smith',
            'title' => '',
            'first' => 'Jonathan',
            'middle' => '',
            'last' => 'Smith',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Anthony Von Fange III',
            'title' => '',
            'first' => 'Anthony',
            'middle' => '',
            'last' => 'Von Fange',
            'nick' => '',
            'suffix' => 'III',
        ];

        yield [
            'original' => 'Mr John Doe',
            'title' => 'Mr',
            'first' => 'John',
            'middle' => '',
            'last' => 'Doe',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Mr John Doe PhD, Esq',
            'title' => 'Mr',
            'first' => 'John',
            'middle' => '',
            'last' => 'Doe',
            'nick' => '',
            'suffix' => 'PhD, Esq',
        ];

        yield [
            'original' => 'Mrs. Jane Doe',
            'title' => 'Mrs.',
            'first' => 'Jane',
            'middle' => '',
            'last' => 'Doe',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Smarty Pants PhD',
            'title' => '',
            'first' => 'Smarty',
            'middle' => '',
            'last' => 'Pants',
            'nick' => '',
            'suffix' => 'PhD',
        ];

        yield [
            'original' => 'Smarty Pants, PhD',
            'title' => '',
            'first' => 'Smarty',
            'middle' => '',
            'last' => 'Pants',
            'nick' => '',
            'suffix' => 'PhD',
        ];

        yield [
            'original' => 'Mark P Williams',
            'title' => '',
            'first' => 'Mark',
            'middle' => 'P',
            'last' => 'Williams',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Aaron bin Omar',
            'title' => '',
            'first' => 'Aaron',
            'middle' => '',
            'last' => 'bin Omar',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Richard van der Dys',
            'title' => '',
            'first' => 'Richard',
            'middle' => '',
            'last' => 'van der Dys',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'Joe de la Cruz',
            'title' => '',
            'first' => 'Joe',
            'middle' => '',
            'last' => 'de la Cruz',
            'nick' => '',
            'suffix' => '',
        ];

        yield [
            'original' => 'John Doe Esquire',
            'title' => '',
            'first' => 'John',
            'middle' => '',
            'last' => 'Doe',
            'nick' => '',
            'suffix' => 'Esquire',
        ];
    }

    /**
     * Lists of names.
     * Test case for name part parsing.
     */
    public static function generatePartNames(): Generator
    {
        // Return one part of name implemented.
        yield [
            'original' => 'Mr. Jüan Martinez (Martin) de Lorenzo y Gutierez Jr.',
            'title' => 'Mr.',
        ];

        yield [
            'original' => 'Mr. Jüan Martinez (Martin) de Lorenzo y Gutierez Jr.',
            'first' => 'Jüan',
        ];

        yield [
            'original' => 'Mr. Jüan Martinez (Martin) de Lorenzo y Gutierez Jr.',
            'middle' => 'Martinez',
        ];

        yield [
            'original' => 'Mr. Jüan Martinez (Martin) de Lorenzo y Gutierez Jr.',
            'last' => 'de Lorenzo y Gutierez',
        ];

        yield [
            'original' => 'Mr. Jüan Martinez (Martin) de Lorenzo y Gutierez Jr.',
            'nick' => 'Martin',
        ];

        yield [
            'original' => 'Mr. Jüan Martinez (Martin) de Lorenzo y Gutierez Jr.',
            'suffix' => 'Jr.',
        ];

        // Additional tests on error part.
        yield [
            'original' => '',
            'errors' => ['Incorrect input to parse.'],
        ];

        yield [
            'original' => 'Jüan, Martinez, de Lorenzo y Gutierez',
            'errors' => ["Can't flip around multiple ',' characters in name string 'Jüan, Martinez, de Lorenzo y Gutierez'."],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new Parser();
    }

    #[DataProvider('generateAdditionalNames')]
    public function testAdditionalNameList(string $original, string $title, string $first, string $middle, string $last, string $nick, string $suffix): void
    {
        $parser = new Parser(stop_on_error: false);
        $parsedName = $parser->parse($original);
        $this->assertEquals($title, $parsedName->getAcademicTitle());
        $this->assertEquals($nick, $parsedName->getNickNames());
        $this->assertEquals($suffix, $parsedName->getSuffix());
        $this->assertEquals($last, $parsedName->getLastName());
        $this->assertEquals($first, $parsedName->getFirstName());
        $this->assertEquals($middle, $parsedName->getMiddleName());
    }

    /**
     * @param string[] $errors
     */
    #[DataProvider('generateNames')]
    public function testNameList(string $original, string $title, string $first, string $middle, string $last, string $nick, string $suffix, array $errors): void
    {
        $parser = new Parser(stop_on_error: false);
        $nameObject = $parser->parse($original);
        $this->assertEquals($title, $nameObject->getAcademicTitle());
        $this->assertEquals($nick, $nameObject->getNickNames());
        $this->assertEquals($suffix, $nameObject->getSuffix());
        $this->assertEquals($last, $nameObject->getLastName());
        $this->assertEquals($first, $nameObject->getFirstName());
        $this->assertEquals($middle, $nameObject->getMiddleName());
        $this->assertEquals($errors, $nameObject->getErrors());
    }

    public function testThrows(): void
    {
        $this->expectException(IncorrectInputException::class);
        $this->parser->parse('');
    }

    public function testDoesNotThrow(): void
    {
        $this->expectNotToPerformAssertions();
        (new Parser(stop_on_error: false))->parse('');
    }

    /**
     * @param string[] $errors
     */
    #[DataProvider('generateCaseNames')]
    public function testCaseNameList(string $original, string $title, string $first, string $middle, string $last, string $nick, string $suffix, array $errors, ?bool $fixCase = null): void
    {
        $parser = new Parser(stop_on_error: false, fix_case: $fixCase ?? true);
        $parsedName = $parser->parse($original);
        $this->assertEquals($title, $parsedName->getAcademicTitle());
        $this->assertEquals($nick, $parsedName->getNickNames());
        $this->assertEquals($suffix, $parsedName->getSuffix());
        $this->assertEquals($last, $parsedName->getLastName());
        $this->assertEquals($first, $parsedName->getFirstName());
        $this->assertEquals($middle, $parsedName->getMiddleName());
        $this->assertEquals($errors, $parsedName->getErrors());
    }

    /**
     * @param null|string[] $errors
     */
    #[DataProvider('generatePartNames')]
    public function testPartNameList(string $original, ?string $title = null, ?string $first = null, ?string $middle = null, ?string $last = null, ?string $nick = null, ?string $suffix = null, ?array $errors = null): void
    {
        $parsedName = (new Parser(stop_on_error: false))->parse($original);
        if ($title) {
            $this->assertEquals($title, $parsedName->getAcademicTitle());
        }
        if ($first) {
            $this->assertEquals($first, $parsedName->getFirstName());
        }
        if ($middle) {
            $this->assertEquals($middle, $parsedName->getMiddleName());
        }
        if ($last) {
            $this->assertEquals($last, $parsedName->getLastName());
        }
        if ($nick) {
            $this->assertEquals($nick, $parsedName->getNicknames());
        }
        if ($suffix) {
            $this->assertEquals($suffix, $parsedName->getSuffix());
        }
        if ($errors) {
            $this->assertEquals($errors, $parsedName->getErrors());
        }
    }

    public function testNoLastNameDefaultException(): void
    {
        $this->expectException(LastNameNotFoundException::class);
        $name = 'Edward';
        $this->parser->parse($name);
    }

    public function testNoFirstNameDefaultException(): void
    {
        $this->expectException(FirstNameNotFoundException::class);
        $name = 'Mr. Hyde';
        $this->parser->parse($name);
    }

    public function testFlipStringException(): void
    {
        $this->expectException(FlipStringException::class);
        $name = 'Jüan, Martinez, de Lorenzo y Gutierez';
        $this->parser->parse($name);
    }

    public function testMultipleMatchesException(): void
    {
        $this->expectException(MultipleMatchesException::class);
        $name = 'Jüan Martinez, Jr de Lorenzo y Gutierez, Jr';
        $this->parser->parse($name);
    }
}
