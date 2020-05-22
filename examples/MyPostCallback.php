<?php

declare(strict_types=1);

namespace ContractExamples;

use Contract\ContractCallbackInterface;

class MyPostCallback implements ContractCallbackInterface
{
    public function match($returnData): bool
    {
        return $returnData === 2;
    }
}