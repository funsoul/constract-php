<?php

declare(strict_types=1);

namespace Contract;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionMethod;
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
     * @return bool
     */
    public function isMatchPreCondition(AnnotationReader $reader, ReflectionMethod $method, array $arguments): bool
    {
        if (empty($arguments)) {
            return true;
        }

        $paramNames = $this->getParameterNames($method->getParameters());
        $pre = $reader->getMethodAnnotation($method, Pre::class);
        $parseCondition = $this->parseCondition($pre->condition);

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

        return true;
    }

    public function isMatchPostCondition(AnnotationReader $reader, ReflectionMethod $method, array $arguments)
    {
        return true;
    }

    /**
     * @param array $parameters
     * @return array
     */
    public function getParameterNames(array $parameters): array
    {
        $names = [];

        if (!empty($parameters)) {
            foreach ($parameters as $parameter) {
                $names[] = $parameter->name;
            }
        }

        return $names;
    }


    public function parseCondition(string $condition): array
    {
        if (preg_match_all('/([a-zA-Z0-9_]+)\s*([<=>])\s*([a-zA-Z0-9_]+)/', $condition, $matches)) {
            $len = count($matches[0]);

            list(,$params, $conditions, $expects) = $matches;

            $parses = [];

            for ($i = 0; $i < $len; $i++) {
                if (isset($parses[$params[$i]])) {
                    array_push($parses[$params[$i]]['conditions'], $conditions[$i]);
                    array_push($parses[$params[$i]]['expects'], $expects[$i]);
                } else {
                    $parses[$params[$i]] = [
                        'conditions' => [$conditions[$i]],
                        'expects' => [$expects[$i]]
                    ];
                }
            }

            return $parses;
        }

        return [];
    }

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