<?php

declare(strict_types=1);

namespace ContractExamples;

use Contract\ContractCallbackInterface;

class MyEnsureCallback implements ContractCallbackInterface
{
    public function match(array $arguments): bool
    {
        list($a, $b) = $arguments;

        return $a >= 1 || $b >= 1;
    }
}