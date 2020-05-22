<?php

declare(strict_types=1);

namespace Contract;

/**
 * Class Parser
 *
 * @package Contract
 */
class Parser
{
    /** @var array */
    private $data;

    public function __construct(string $condition)
    {
        $this->parseCondition($condition);
    }

    /**
     * @param string $condition
     */
    private function parseCondition(string $condition)
    {
        if (preg_match_all('/([a-zA-Z0-9_]+)\s*([<=>])\s*([a-zA-Z0-9_]+)/', $condition, $matches)) {
            $len = count($matches[0]);

            list(,$params, $conditions, $expects) = $matches;

            for ($i = 0; $i < $len; $i++) {
                if (isset($this->data[$params[$i]])) {
                    array_push($this->data[$params[$i]]['conditions'], $conditions[$i]);
                    array_push($this->data[$params[$i]]['expects'], $expects[$i]);
                } else {
                    $this->data[$params[$i]] = [
                        'conditions' => [$conditions[$i]],
                        'expects' => [$expects[$i]]
                    ];
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}