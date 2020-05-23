<?php

declare(strict_types=1);

namespace Contract;

/**
 * Interface ContractCallback
 *
 * @package Contract
 */
interface ContractCallbackInterface
{
    public function match(array $arguments): bool;
}