<?php

declare(strict_types=1);

namespace Contract;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionMethod;
use ReflectionParameter;
use RuntimeException;

/**
 * Class Contract
 *
 * @package Contract
 */
class Contract
{
    /**
     * @param AnnotationReader $reader
     * @param ReflectionMethod $method
     * @param array $arguments
     *
     * @return bool
     */
    public function isMatchPreCondition(AnnotationReader $reader, ReflectionMethod $method, array $arguments): bool
    {
        if (empty($arguments)) {
            return true;
        }

        $paramNames = $this->getParameterNames($method->getParameters());
        $pre = $reader->getMethodAnnotation($method, Pre::class);

        if ($pre->callback) {
            if (class_exists($pre->callback)) {
                return $this->isMatchPreCallback($pre->callback, $arguments);
            }

            throw new RuntimeException("pre contract callback {$pre->callback} not exists");
        }

        if ($pre->condition) {
            $parser = new Parser($pre->condition);
            $parseCondition = $parser->getData();

            if (!empty($parseCondition)) {
                foreach ($paramNames as $index => $paramName) {
                    if (isset($parseCondition[$paramName])) {
                        $argument = $arguments[$index];
                        $contract = $parseCondition[$paramName];

                        foreach ($contract['conditions'] as $conditionIndex => $condition) {
                            $expectValue = $contract['expects'][$conditionIndex];

                            $isMatch = $this->match($argument, $condition, $expectValue);
                            if (!$isMatch) {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param string $callback
     * @param array $arguments
     *
     * @return bool
     */
    public function isMatchPreCallback(string $callback, array $arguments): bool
    {
        $instance = new $callback();

        return $instance->match($arguments);
    }

    /**
     * @param AnnotationReader $reader
     * @param ReflectionMethod $method
     * @param $returnData
     *
     * @return bool
     */
    public function isMatchPostCondition(AnnotationReader $reader, ReflectionMethod $method, $returnData): bool
    {
        $post = $reader->getMethodAnnotation($method, Post::class);

        if ($post->callback) {
            if (class_exists($post->callback)) {
                return $this->isMatchPostCallback($post->callback, $returnData);
            }

            throw new RuntimeException("post contract callback {$post->callback} not exists");
        }

        return true;
    }

    /**
     * @param string $callback
     * @param $returnData
     * @return bool
     */
    public function isMatchPostCallback(string $callback, $returnData): bool
    {
        $instance = new $callback();

        return $instance->match($returnData);
    }

    /**
     * @param array $parameters
     * @return array
     */
    public function getParameterNames(array $parameters): array
    {
        $names = [];

        if (!empty($parameters)) {
            /** @var ReflectionParameter $parameter */
            foreach ($parameters as $parameter) {
                $names[] = $parameter->name;
            }
        }

        return $names;
    }

    /**
     * @param $argument
     * @param $condition
     * @param $expectValue
     *
     * @return bool
     */
    public function match($argument, $condition, $expectValue): bool
    {
        switch ($condition) {
            case '<':
                if ($argument >= $expectValue) {
                    return false;
                }
                break;
            case '<=':
                if ($argument > $expectValue) {
                    return false;
                }
                break;
            case '>':
                if ($argument <= $expectValue) {
                    return false;
                }
                break;
            case '>=':
                if ($argument < $expectValue) {
                    return false;
                }
                break;
            case '=':
                if ($argument != $expectValue) {
                    return false;
                }
                break;
            case '!=':
                if ($argument == $expectValue) {
                    return false;
                }
                break;
            default:
                throw new RuntimeException("unsupported condition: {$condition}");
                break;
        }

        return true;
    }
}