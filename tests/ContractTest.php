<?php

declare(strict_types=1);

namespace ContractTest;

use Contract\Contract;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionParameter;
use ReflectionMethod;
use ContractExamples\Test;

/**
 * Class ContractTest
 * @package ContractTest
 */
class ContractTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function testMatchTrue()
    {
        $data = [
            [1, '<', 2],
            [1, '<=', 1],
            [2, '>', 1],
            [1, '>=', 1],
            [1, '=', 1],
            [1, '!=', 2],
        ];

        $contract = new Contract();
        foreach ($data as $datum) {
            list($argument, $condition, $expect) = $datum;
            $res = $contract->match($argument, $condition, $expect);

            $this->assertTrue($res);
        }
    }

    public function testMatchFalse()
    {
        $data = [
            [1, '>', 2],
            [1, '>', 1],
            [2, '<', 1],
            [1, '<', 1],
            [1, '!=', 1],
            [1, '=', 2],
        ];

        $contract = new Contract();
        foreach ($data as $datum) {
            list($argument, $condition, $expect) = $datum;
            $res = $contract->match($argument, $condition, $expect);

            $this->assertFalse($res);
        }
    }

    public function testMatchException()
    {
        $this->expectException(\RuntimeException::class);

        $data = [
            [1, '*', 2],
            [1, '!', 1],
            [2, '@', 1],
            [1, '#', 1],
            [1, '$', 1],
            [1, '%', 2],
            [1, '^', 2],
            [1, '&', 2],
            [1, '(', 2],
            [1, ')', 2],
            [1, '_', 2],
            [1, '+', 2],
            [1, '..', 2],
        ];

        $contract = new Contract();
        foreach ($data as $datum) {
            list($argument, $condition, $expect) = $datum;

            $contract->match($argument, $condition, $expect);
        }
    }

    public function testGetParameterNamesEmpty()
    {
        $contract = new Contract();

        $names = $contract->getParameterNames([]);

        $this->assertEquals([], $names);
    }

    public function testGetParameterNames()
    {
        $ref = new ReflectionClass(Test::class);
        $method = $ref->getMethod('addTwoNums');
        $parameters = $method->getParameters();

        $contract = new Contract();
        $actual = $contract->getParameterNames($parameters);

        $expect = ['a', 'b'];

        $this->assertEquals($expect, $actual);
    }
}