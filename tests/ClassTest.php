<?php

declare(strict_types=1);

namespace ContractTest;

use Contract\Proxy;
use PHPUnit\Framework\TestCase;

class ClassTest extends TestCase
{
    public function testAddTwoNums()
    {
        $proxy = new Proxy(new Test());
        $res = $proxy->addTwoNums(1, 1);
        $this->assertEquals(2, $res);
    }
}