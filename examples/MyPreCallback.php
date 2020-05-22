<?php

declare(strict_types=1);

namespace ContractExamples;

use Contract\ContractCallbackInterface;

class MyPreCallback implements ContractCallbackInterface
{
    public function match($arguments): bool
    {
        list($a, $b) = $arguments;

        return $a >= 1 || $b >= 1;
    }
}