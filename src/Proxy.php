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
            $properties = $ref->getProperties();
            $method = $ref->getMethod($name);

            if ($method->isPublic() && !$method->isAbstract()){
                $reader = new AnnotationReader();
                $require = $reader->getMethodAnnotation($method, DbcRequire::class);
                $ensure = $reader->getMethodAnnotation($method, DbcEnsure::class);
                $invariant = $reader->getMethodAnnotation($method, DbcInvariant::class);

                $contract = new Contract();

                if (!is_null($require) && property_exists($require, 'callback') && !is_null($require->callback)) {
                    if (class_exists($require->callback)) {
                        if (!$contract->matchRequireCallback($require->callback, $arguments)) {
                            throw new RuntimeException("unmatched require callback");
                        }
                    } else {
                        throw new RuntimeException("require of contract callback {$require->callback} not exists");
                    }
                }

                if (!is_null($require) && property_exists($require, 'condition')&& !is_null($require->condition)) {
                    if (!$contract->matchRequireCondition($require->condition, $method, $arguments)) {
                        throw new RuntimeException("unmatched require condition");
                    }
                }

                if (!is_null($invariant) && property_exists($invariant, 'condition') && !is_null($invariant->condition)) {
                    if (!$contract->matchInvariantCondition($invariant->condition, $properties, $this->obj)) {
                        throw new RuntimeException("unmatched invariant condition");
                    }
                }

                $returnData = call_user_func([$this->obj, $name], ...$arguments);

                if (!is_null($ensure) && property_exists($ensure, 'callback') && !is_null($ensure->callback)) {
                    if (class_exists($ensure->callback)) {
                        if (!$contract->matchEnsureCallback($ensure->callback, $arguments)) {
                            throw new RuntimeException("unmatched ensure callback");
                        }
                    } else {
                        throw new RuntimeException("ensure of contract callback {$ensure->callback} not exists");
                    }
                }

                if (!is_null($ensure) && property_exists($ensure, 'condition') && !is_null($ensure->condition)) {
                    if (!$contract->matchEnsureCondition($ensure->condition, $method, $arguments)) {
                        throw new RuntimeException("unmatched ensure condition");
                    }
                }

                return $returnData;
            }
        }
        throw new RuntimeException("undefined method {$name}() of {$this->obj}");
    }
}