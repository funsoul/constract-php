<?php

declare(strict_types=1);

namespace ContractExamples;

use Contract\Post;
use Contract\Pre;

class Test {
    /**
     * @Pre(condition="a >= 1, a < 10, b >= 1")
     * @Post(callback="ContractExamples\MyPostCallback")
     * @param int $a
     * @param int $b
     * @return int
     */
    public function addTwoNums(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * @Pre(callback="ContractExamples\MyPreCallback")
     * @Post(callback="ContractExamples\MyPostCallback")
     * @param int $a
     * @param int $b
     * @return int
     */
    public function addTwoNumsCallback(int $a, int $b): int
    {
        return $a + $b;
    }
}