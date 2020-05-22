<?php

declare(strict_types=1);

namespace ContractTest;

use Contract\Pre;

class Test {
    /**
     * @Pre(condition="a >= 1, a < 10, b >= 1")
     * @param int $a
     * @param int $b
     * @return int
     */
    public function addTwoNums(int $a, int $b): int
    {
        return $a + $b;
    }
}