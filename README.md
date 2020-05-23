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

### Design by Contract

- Require
- Ensure
- Invariant

### Using Annotations

```php
@DbcRequire(condition="a >= 1, a < 10, b >= 1")
@DbcInvariant(condition="discount = 0.6")
@DbcEnsure(callback="ContractExamples\MyEnsureCallback")
```

### Supported Conditions

- gt >
- ge >=
- lt <
- le <=
- e =
- ne !=

### Custom Callback (If conditions don't meet your needs.)

MyRequireCallback.php

```php
use Contract\ContractCallbackInterface;

class MyRequireCallback implements ContractCallbackInterface
{
    public function match(array $arguments): bool
    {
        list($a, $b) = $arguments;

        return $a >= 1 || $b >= 1;
    }
}
```

### Supplier

Test.php

```php
class Test {
    /** @var float */
    private $discount = 0.5;

    /**
     * @DbcRequire(condition="a >= 1, a < 10, b >= 1")
     * @param int $a
     * @param int $b
     * @return int
     */
    public function addTwoNums(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * @DbcRequire(callback="ContractExamples\MyRequireCallback")
     * @DbcEnsure(callback="ContractExamples\MyEnsureCallback")
     * @param int $a
     * @param int $b
     * @return int
     */
    public function addTwoNumsCallback(int $a, int $b): int
    {
        return $a + $b;
    }

    /**
     * @DbcRequire(callback="ContractExamples\MyRequireCallback")
     * @DbcEnsure(callback="ContractExamples\MyEnsureCallback")
     * @DbcInvariant(condition="discount = 0.6")
     * @param int $a
     * @param int $b
     * @return float
     */
    public function multiplyDiscount(int $a, int $b): float
    {
        return ($a + $b) * $this->discount;
    }
}
```

### Client

```php
/** @var ContractExamples\Test $proxy */
$proxy = new Contract\Proxy(new ContractExamples\Test());

$res1 = $proxy->addTwoNums(1, 1);

$res2 = $proxy->addTwoNumsCallback(1, 1);

$res3 = $proxy->multiplyDiscount(2, 2);

var_dump($res1, $res2, $res3);
```

:eyes: Of course, it's just an experimental toy.Enjoy it!