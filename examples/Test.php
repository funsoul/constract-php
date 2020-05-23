<?php

declare(strict_types=1);

namespace ContractExamples;

use Contract\DbcEnsure;
use Contract\DbcInvariant;
use Contract\DbcRequire;

class Test {
    /** @var float */
    private $discount = 0.5;

    /**
     * @DbcRequire(condition="a >= 1, a < 10, b >= 1")
     * @DbcEnsure(callback="ContractExamples\MyEnsureCallback")
     * @param int $a
     * @param int $b
     * @return int
     */
    public function addTwoNums(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * @DbcRequire(callback="ContractExamples\MyRequireCallback")
     * @DbcEnsure(callback="ContractExamples\MyEnsureCallback")
     * @param int $a
     * @param int $b
     * @return int
     */
    public function addTwoNumsCallback(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * @DbcRequire(callback="ContractExamples\MyRequireCallback")
     * @DbcEnsure(callback="ContractExamples\MyEnsureCallback")
     * @DbcInvariant(condition="discount = 0.6")
     * @param int $a
     * @param int $b
     * @return float
     */
    public function multiplyDiscount(int $a, int $b): float
    {
        return ($a + $b) * $this->discount;
    }
}