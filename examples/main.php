<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

/** @var ContractExamples\Test $proxy */
$proxy = new Contract\Proxy(new ContractExamples\Test());
$res1 = $proxy->addTwoNums(1, 1);

$res2 = $proxy->addTwoNumsCallback(1, 1);

var_dump($res1, $res2);