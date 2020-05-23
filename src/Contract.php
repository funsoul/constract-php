<?php

declare(strict_types=1);

namespace Contract;

use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;

/**
 * Class Contract
 *
 * @package Contract
 */
class Contract
{
    /**
     * @param string $condition
     * @param ReflectionMethod $method
     * @param array $arguments
     *
     * @return bool
     */
    public function matchRequireCondition(string $condition, ReflectionMethod $method, array $arguments): bool
    {
        if (empty($arguments)) {
            return true;
        }

        $paramNames = $this->getParameterNames($method->getParameters());

        $parser = new Parser($condition);
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

        return true;
    }

    /**
     * @param string $callback
     * @param array $arguments
     *
     * @return bool
     */
    public function matchRequireCallback(string $callback, array $arguments): bool
    {
        $instance = new $callback();

        return $instance->match($arguments);
    }

    public function matchInvariantCondition(string $condition, array $properties, $obj)
    {
        if (empty($properties)) {
            return true;
        }

        $propertyMap = $this->getPropertyMap($properties, $obj);
        if (empty($propertyMap)) {
            return true;
        }

        $parser = new Parser($condition);
        $parseCondition = $parser->getData();

        if (!empty($parseCondition)) {
            foreach ($propertyMap as $propertyName => $propertyValue) {
                if (isset($parseCondition[$propertyName])) {
                    $contract = $parseCondition[$propertyName];

                    foreach ($contract['conditions'] as $conditionIndex => $condition) {
                        $expectValue = $contract['expects'][$conditionIndex];

                        $isMatch = $this->match($propertyValue, $condition, $expectValue);
                        if (!$isMatch) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param string $condition
     * @param ReflectionMethod $method
     * @param array $arguments
     *
     * @return bool
     */
    public function matchEnsureCondition(string $condition, ReflectionMethod $method, array $arguments): bool
    {
        if (empty($arguments)) {
            return true;
        }

        $paramNames = $this->getParameterNames($method->getParameters());

        $parser = new Parser($condition);
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

        return true;
    }

    /**
     * @param string $callback
     * @param array $arguments
     * @return bool
     */
    public function matchEnsureCallback(string $callback, array $arguments): bool
    {
        $instance = new $callback();

        return $instance->match($arguments);
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
     * @param array $properties
     * @param $obj
     * @return array
     */
    public function getPropertyMap(array $properties, $obj): array
    {
        $map = [];

        if (!empty($properties)) {
            /** @var ReflectionProperty $property */
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $map[$property->name] = $property->getValue($obj);
            }
        }

        return $map;
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