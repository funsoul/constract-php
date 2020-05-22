<?php

declare(strict_types=1);

namespace ContractTest;

use Contract\Parser;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserTest
 * @package ContractTest
 */
class ParserTest extends TestCase
{
    public function testOneCondition()
    {
        $condition = "a = 1";
        $parser = new Parser($condition);

        $expect = [
            'a' => [
                'conditions' => ['='],
                'expects' => [1]
            ]
        ];

        $actual = $parser->getData();

        $this->assertEquals($expect, $actual);
    }

    public function testMultipleSameCondition()
    {
        $condition = "a > 1, a < 5";
        $parser = new Parser($condition);

        $expect = [
            'a' => [
                'conditions' => ['>', '<'],
                'expects' => [1, 5]
            ]
        ];

        $actual = $parser->getData();

        $this->assertEquals($expect, $actual);
    }

    public function testMultipleCondition()
    {
        $condition = "a > 1, b < 5";
        $parser = new Parser($condition);

        $expect = [
            'a' => [
                'conditions' => ['>'],
                'expects' => [1]
            ],
            'b' => [
                'conditions' => ['<'],
                'expects' => [5]
            ]
        ];

        $actual = $parser->getData();

        $this->assertEquals($expect, $actual);
    }
}