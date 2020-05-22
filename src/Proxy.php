<?php

declare(strict_types=1);

namespace Contract;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use RuntimeException;

/**
 * Class Proxy
 *
 * @package Contract
 */
class Proxy
{
    /**
     * @var Object
     */
    private $obj;

    public function __construct($obj)
    {
        $this->obj = $obj;
    }

    public function __call(string $name, array $arguments)
    {
        $ref = new ReflectionClass($this->obj);
        if ($ref->hasMethod($name)){
            $method = $ref->getMethod($name);

            if ($method->isPublic() && !$method->isAbstract()){
                $reader = new AnnotationReader();

                $contract = new Contract();

                if (!$contract->isMatchPreCondition($reader, $method, $arguments)) {
                    throw new RuntimeException("unmatched pre condition");
                }

                $returnData = call_user_func([$this->obj, $name], ...$arguments);

                if (!$contract->isMatchPostCondition($reader, $method, $returnData)) {
                    throw new RuntimeException("unmatched post condition");
                }

                return $returnData;
            }
        }
        throw new RuntimeException("undefined method {$name}() of {$this->obj}");
    }
}