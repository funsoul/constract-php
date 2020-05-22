<?php

declare(strict_types=1);

namespace ContractExamples;

use Contract\ContractCallbackInterface;

class MyPreCallback implements ContractCallbackInterface
{
    public function match($arguments): bool
    {
        foreach ($arguments as $argument) {
            if ($argument == 2) {
                return false;
            }
        }

        return true;
    }
}