# contract-php

:eyes: A toy that implements contractual design

![Build Status](https://travis-ci.org/funsoul/contract-php.svg?branch=master)
![](https://img.shields.io/badge/PHP-%3E%3D7.1.0-green)
![](https://img.shields.io/github/license/funsoul/contract-php)

## Installation

```bash
git clone https://github.com/funsoul/contract-php.git
cd contract-php
composer install
```

## Usages

### condition

- gt >
- ge >=
- lt <
- le <=
- e =
- ne !=

eg.

```php
/**
 * @Pre(condition="a > 1")
 * @param int $a
 * @param int $b
 * @return int
 */
public function addTwoNums(int $a, int $b): int
{
    return $a + $b;
}
```

### custom callback

MyPreCallback.php

```php
use Contract\ContractCallbackInterface;

class MyPreCallback implements ContractCallbackInterface
{
    public function match($arguments): bool
    {
        list($a, $b) = $arguments;

        return $a >= 1 || $b >= 1;
    }
}
```

Test.php

```php
/**
 * @Pre(callback="ContractExamples\MyPreCallback")
 * @param int $a
 * @param int $b
 * @return int
 */
public function addTwoNums(int $a, int $b): int
{
    return $a + $b;
}
```

### examples

Test.php 

```php
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
```

main.php

```php
/** @var ContractExamples\Test $proxy */
$proxy = new Contract\Proxy(new ContractExamples\Test());
$res1 = $proxy->addTwoNums(1, 1);

$res2 = $proxy->addTwoNumsCallback(1, 1);

var_dump($res1, $res2);
```