<?php

declare(strict_types=1);

namespace Contract;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class DbcInvariant
{
    /**
     * @var string
     */
    public $condition;
}